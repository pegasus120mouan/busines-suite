<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        
        return view('settings.index', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'max:50'],
            'locale' => ['required', 'string', 'max:5'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_iban' => ['nullable', 'string', 'max:50'],
            'bank_bic' => ['nullable', 'string', 'max:20'],
            'invoice_prefix' => ['nullable', 'string', 'max:10'],
            'quote_prefix' => ['nullable', 'string', 'max:10'],
            'invoice_footer' => ['nullable', 'string', 'max:1000'],
            'quote_footer' => ['nullable', 'string', 'max:1000'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Store settings in the settings JSON field
        $settings = $tenant->settings ?? [];
        $settings['bank_name'] = $validated['bank_name'] ?? null;
        $settings['bank_iban'] = $validated['bank_iban'] ?? null;
        $settings['bank_bic'] = $validated['bank_bic'] ?? null;
        $settings['invoice_prefix'] = $validated['invoice_prefix'] ?? 'FAC-';
        $settings['quote_prefix'] = $validated['quote_prefix'] ?? 'DEV-';
        $settings['invoice_footer'] = $validated['invoice_footer'] ?? null;
        $settings['quote_footer'] = $validated['quote_footer'] ?? null;
        $settings['city'] = $validated['city'] ?? null;
        $settings['postal_code'] = $validated['postal_code'] ?? null;
        $settings['country'] = $validated['country'] ?? null;
        $settings['tax_number'] = $validated['tax_number'] ?? null;
        $settings['registration_number'] = $validated['registration_number'] ?? null;

        $tenant->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'logo' => $validated['logo'] ?? $tenant->logo,
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'locale' => $validated['locale'],
            'settings' => $settings,
        ]);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function deleteLogo()
    {
        $tenant = auth()->user()->tenant;

        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $tenant->update(['logo' => null]);

        return back()->with('success', 'Logo supprimé.');
    }
}
