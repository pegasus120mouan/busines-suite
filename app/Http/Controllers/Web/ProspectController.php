<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Services\HsmsSmsService;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index(Request $request, HsmsSmsService $hsms)
    {
        $prospects = Prospect::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('contact', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $tenant = auth()->user()->tenant;
        $hsmsSettings = $tenant->settings['hsms'] ?? [];
        $hsmsConfigured = $hsms->isConfigured($tenant);
        $smsTemplate = $hsmsSettings['message_template'] ?? HsmsSmsService::defaultTemplate();

        // Remplacer l'ancien modèle "Bonjour..." par le nouveau message OVL
        if (str_contains($smsTemplate, 'Bonjour {prenom}') || str_contains($smsTemplate, 'est un service de livraison')) {
            $smsTemplate = HsmsSmsService::defaultTemplate();
            $hsmsSettings['message_template'] = $smsTemplate;
            $hsmsSettings['company_name'] = $hsmsSettings['company_name'] ?? 'OVL Delivery Services';
            $hsmsSettings['contact_phone'] = $hsmsSettings['contact_phone'] ?? '0787703000';
            $hsmsSettings['whatsapp_phone'] = '084828385';
            $settings = $tenant->settings ?? [];
            $settings['hsms'] = array_merge($hsmsSettings, [
                'client_id' => $hsmsSettings['client_id'] ?? '',
                'client_secret' => $hsmsSettings['client_secret'] ?? '',
                'token' => $hsmsSettings['token'] ?? '',
            ]);
            $tenant->update(['settings' => $settings]);
        }

        return view('prospects.index', compact('prospects', 'hsmsSettings', 'hsmsConfigured', 'smsTemplate', 'tenant'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
        ], [
            'last_name.required' => 'Le nom est obligatoire.',
            'first_name.required' => 'Les prénoms sont obligatoires.',
            'contact.required' => 'Le contact est obligatoire.',
        ]);

        Prospect::create($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospect ajouté avec succès.');
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
        ], [
            'last_name.required' => 'Le nom est obligatoire.',
            'first_name.required' => 'Les prénoms sont obligatoires.',
            'contact.required' => 'Le contact est obligatoire.',
        ]);

        $prospect->update($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }

    public function destroy(Prospect $prospect)
    {
        $prospect->delete();

        return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }

    public function updateHsmsSettings(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255'],
            'token' => ['required', 'string', 'max:2000'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'message_template' => ['nullable', 'string', 'max:918'],
        ], [
            'client_id.required' => 'Le Client ID est obligatoire.',
            'client_secret.required' => 'Le Client Secret est obligatoire.',
            'token.required' => 'Le Token API est obligatoire.',
        ]);

        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];
        $existing = $settings['hsms'] ?? [];

        $settings['hsms'] = [
            'client_id' => $validated['client_id'],
            'client_secret' => $validated['client_secret'],
            'token' => $validated['token'],
            'company_name' => $validated['company_name'] ?? ($existing['company_name'] ?? 'OVL Delivery Services'),
            'contact_phone' => $validated['contact_phone'] ?? ($existing['contact_phone'] ?? '0787703000'),
            'whatsapp_phone' => $validated['whatsapp_phone'] ?? ($existing['whatsapp_phone'] ?? '084828385'),
            'message_template' => $validated['message_template']
                ?: ($existing['message_template'] ?? HsmsSmsService::defaultTemplate()),
        ];
        $tenant->update(['settings' => $settings]);

        return redirect()->route('prospects.index')->with('success', 'Paramètres de l\'intégrateur HSMS enregistrés.');
    }

    public function sendSms(Request $request, Prospect $prospect, HsmsSmsService $hsms)
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:918'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $phone = $validated['phone'] ?: ($prospect->whatsapp ?: $prospect->contact);
        $message = filled($validated['message'] ?? null)
            ? $validated['message']
            : $hsms->buildMessage($prospect);

        try {
            $hsms->send($phone, $message);

            $prospect->increment('sms_sent_count');
            $prospect->update(['last_sms_sent_at' => now()]);

            return redirect()->route('prospects.index')
                ->with('success', 'SMS envoyé avec succès à ' . $prospect->full_name . '.');
        } catch (\Throwable $e) {
            return redirect()->route('prospects.index')
                ->with('error', $e->getMessage());
        }
    }
}
