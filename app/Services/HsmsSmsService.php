<?php

namespace App\Services;

use App\Models\Prospect;
use App\Models\Tenant;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HsmsSmsService
{
    public function isConfigured(?Tenant $tenant = null): bool
    {
        $config = $this->credentials($tenant);

        return filled($config['client_id'] ?? null)
            && filled($config['client_secret'] ?? null)
            && filled($config['token'] ?? null);
    }

    public function credentials(?Tenant $tenant = null): array
    {
        $tenant ??= auth()->user()?->tenant;

        return $tenant?->settings['hsms'] ?? [];
    }

    public function send(string $phone, string $message, ?Tenant $tenant = null): array
    {
        if (! $this->isConfigured($tenant)) {
            throw new RuntimeException('L\'intégrateur SMS HSMS n\'est pas configuré. Renseignez Client ID, Client Secret et Token.');
        }

        $config = $this->credentials($tenant);
        $telephone = $this->normalizePhone($phone);

        if ($telephone === '' || strlen($telephone) < 10) {
            throw new RuntimeException('Numéro de téléphone invalide.');
        }

        $payload = [
            'clientid' => $config['client_id'],
            'clientsecret' => $config['client_secret'],
            'telephone' => $telephone,
            'message' => $message,
        ];

        // Essayer v2 puis legacy (URLs avec slash final pour éviter le 301 POST→GET)
        $endpoints = [
            $this->url('v2/sms/send/'),
            $this->url('envoi-sms/'),
            $this->url('envoi-sms'),
        ];

        $lastResponse = null;

        foreach ($endpoints as $endpoint) {
            $response = $this->post($endpoint, $config['token'], $payload);
            $lastResponse = $response;

            if ($response->successful()) {
                return $response->json() ?? ['success' => true, 'message' => 'SMS envoyé'];
            }

            // Si 405/404, tenter l'endpoint suivant
            if (in_array($response->status(), [404, 405], true)) {
                Log::warning('HSMS endpoint unavailable, trying next', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                continue;
            }

            break;
        }

        Log::warning('HSMS SMS send failed', [
            'status' => $lastResponse?->status(),
            'body' => $lastResponse?->body(),
            'phone' => $telephone,
        ]);

        throw new RuntimeException($this->extractErrorMessage($lastResponse));
    }

    public function checkBalance(?Tenant $tenant = null): array
    {
        if (! $this->isConfigured($tenant)) {
            throw new RuntimeException('L\'intégrateur SMS HSMS n\'est pas configuré.');
        }

        $config = $this->credentials($tenant);

        $response = $this->post($this->url('check-sms/'), $config['token'], [
            'clientid' => $config['client_id'],
            'clientsecret' => $config['client_secret'],
        ]);

        if (! $response->successful()) {
            $response = $this->post($this->url('check-sms'), $config['token'], [
                'clientid' => $config['client_id'],
                'clientsecret' => $config['client_secret'],
            ]);
        }

        if (! $response->successful()) {
            throw new RuntimeException($this->extractErrorMessage($response));
        }

        return $response->json() ?? [];
    }

    /**
     * Template SMS par défaut (personnalisable dans les paramètres).
     */
    public static function defaultTemplate(): string
    {
        return 'Besoin d\'un service de livraison, contact {entreprise} au {contact} whatsapp {whatsapp}';
    }

    public function buildMessage(Prospect $prospect, ?Tenant $tenant = null): string
    {
        $tenant ??= auth()->user()?->tenant;
        $config = $this->credentials($tenant);

        $template = $config['message_template'] ?? self::defaultTemplate();

        $replacements = [
            '{prenom}' => $prospect->first_name ?: $prospect->last_name,
            '{nom}' => $prospect->last_name,
            '{nom_complet}' => $prospect->full_name,
            '{entreprise}' => $config['company_name'] ?? 'OVL Delivery Services',
            '{contact}' => $config['contact_phone'] ?? '0787703000',
            '{whatsapp}' => $config['whatsapp_phone'] ?? '084828385',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Normalise un numéro CI vers le format HSMS (ex: 2250787703000).
     * Conserve le 0 local après l'indicatif, comme dans la doc HSMS.
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        // 00225...
        if (str_starts_with($digits, '00225')) {
            $digits = substr($digits, 2);
        }

        // Déjà au format international 2250...
        if (str_starts_with($digits, '225') && strlen($digits) >= 12) {
            return $digits;
        }

        // Local 10 chiffres : 0787703000 → 2250787703000
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '225' . $digits;
        }

        // 9 chiffres sans 0 : 787703000 → 2250787703000
        if (strlen($digits) === 9) {
            return '2250' . $digits;
        }

        return $digits;
    }

    private function url(string $path): string
    {
        return rtrim(config('services.hsms.base_url'), '/') . '/' . ltrim($path, '/');
    }

    private function post(string $url, string $token, array $payload): Response
    {
        // Ne pas suivre les redirections (un 301 transformerait POST en GET → 405)
        return Http::timeout(30)
            ->withOptions(['allow_redirects' => false])
            ->withToken(trim($token))
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);
    }

    private function extractErrorMessage(?Response $response): string
    {
        if (! $response) {
            return 'Échec de l\'envoi du SMS.';
        }

        $json = $response->json();

        if (is_string($json['detail'] ?? null)) {
            return $json['detail'];
        }

        if (is_string($json['message'] ?? null)) {
            return $json['message'];
        }

        if (is_string($json['error'] ?? null)) {
            return $json['error'];
        }

        return match ($response->status()) {
            401 => 'Token ou identifiants HSMS invalides.',
            403 => 'Accès refusé par HSMS. Vérifiez le token.',
            405 => 'Endpoint HSMS incorrect (méthode non autorisée). Vérifiez l\'URL API.',
            400 => 'Requête refusée par HSMS (solde, numéro ou paramètres).',
            default => 'Échec de l\'envoi du SMS (HTTP ' . $response->status() . ').',
        };
    }
}
