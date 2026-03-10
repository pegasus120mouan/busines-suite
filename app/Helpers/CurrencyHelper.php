<?php

namespace App\Helpers;

class CurrencyHelper
{
    protected static array $symbols = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'XOF' => 'FCFA',
        'XAF' => 'FCFA',
        'MAD' => 'DH',
        'TND' => 'DT',
        'CHF' => 'CHF',
        'CAD' => 'CAD',
        'JPY' => '¥',
        'CNY' => '¥',
    ];

    public static function symbol(?string $currency = null): string
    {
        $currency = $currency ?? static::getTenantCurrency();
        return static::$symbols[$currency] ?? $currency;
    }

    public static function format(float|int|null $amount, ?string $currency = null, int $decimals = 0): string
    {
        $amount = $amount ?? 0;
        $symbol = static::symbol($currency);
        $formatted = number_format($amount, $decimals, ',', ' ');
        
        // Pour XOF/XAF, le symbole vient après
        if (in_array($currency ?? static::getTenantCurrency(), ['XOF', 'XAF'])) {
            return $formatted . ' ' . $symbol;
        }
        
        return $formatted . ' ' . $symbol;
    }

    public static function getTenantCurrency(): string
    {
        $user = auth()->user();
        if ($user && $user->tenant) {
            return $user->tenant->currency ?? 'EUR';
        }
        return 'EUR';
    }
}
