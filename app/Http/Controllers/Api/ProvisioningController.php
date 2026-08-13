<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ProvisioningController extends Controller
{
    public function createTenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'required|string|max:255',
            'plan' => 'nullable|string|max:50',
            'modules' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $slug = Str::slug($validated['company']);
            $baseSlug = $slug;
            $counter = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $tenant = Tenant::create([
                'name' => $validated['company'],
                'slug' => $slug,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'currency' => 'XOF',
                'timezone' => 'Africa/Dakar',
                'locale' => 'fr',
                'status' => 'active',
                'plan' => 'starter',
                'trial_ends_at' => now()->addDays(14),
                'settings' => [
                    'modules' => $validated['modules'] ?? [],
                ],
            ]);

            $password = Str::random(12);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);

            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'tenant_slug' => $tenant->slug,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'password' => $password,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(string $tenantId): JsonResponse
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $user = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->first();

        if (!$user) {
            $user = User::where('tenant_id', $tenant->id)->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found for this tenant',
            ], 404);
        }

        $password = Str::random(12);
        $user->update(['password' => Hash::make($password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'data' => [
                'tenant_id' => $tenant->id,
                'email' => $user->email,
                'password' => $password,
                'name' => $user->name,
                'company' => $tenant->name,
            ],
        ]);
    }
}
