<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function show(): TenantResource
    {
        $tenant = auth()->user()->tenant;
        return new TenantResource($tenant);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'size:3'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'string', 'max:5'],
            'settings' => ['nullable', 'array'],
        ]);

        $tenant = auth()->user()->tenant;
        $tenant->update($request->only([
            'name', 'phone', 'address', 'logo', 'currency', 'timezone', 'locale', 'settings'
        ]));

        return response()->json([
            'message' => 'Tenant updated successfully',
            'data' => new TenantResource($tenant),
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => ['required', 'array'],
        ]);

        $tenant = auth()->user()->tenant;
        $currentSettings = $tenant->settings ?? [];
        $tenant->update([
            'settings' => array_merge($currentSettings, $request->settings),
        ]);

        return response()->json([
            'message' => 'Settings updated successfully',
            'data' => new TenantResource($tenant),
        ]);
    }
}
