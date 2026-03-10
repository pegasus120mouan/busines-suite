<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share currency symbol with all views
        view()->composer('*', function ($view) {
            $view->with('currencySymbol', CurrencyHelper::symbol());
            $view->with('tenantCurrency', CurrencyHelper::getTenantCurrency());
        });
    }
}

// Register global helper function outside the class
if (!function_exists('currency')) {
    function currency(float|int|null $amount, ?string $currency = null, int $decimals = 0): string
    {
        return CurrencyHelper::format($amount, $currency, $decimals);
    }
}
