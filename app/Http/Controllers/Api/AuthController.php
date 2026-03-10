<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RegisterTenantRequest;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterTenantRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'email' => $request->email,
                'phone' => $request->phone,
                'currency' => $request->currency ?? 'EUR',
                'timezone' => $request->timezone ?? 'UTC',
                'locale' => $request->locale ?? 'fr',
                'status' => 'active',
                'plan' => 'free',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            $user->assignRole('admin');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'data' => [
                    'user' => $user->load('tenant'),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        });
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->logFailedLogin($request->email);

            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'message' => 'Account is inactive',
            ], 403);
        }

        if ($user->tenant && !$user->tenant->isActive()) {
            return response()->json([
                'message' => 'Tenant account is suspended',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        $user->updateLastLogin();
        $this->logSuccessfulLogin($user);

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load(['tenant', 'roles', 'permissions']),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logLogout($request->user());
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->load(['tenant', 'roles', 'permissions']),
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    protected function logFailedLogin(string $email): void
    {
        AuditLog::create([
            'tenant_id' => null,
            'user_id' => null,
            'auditable_type' => User::class,
            'auditable_id' => 0,
            'event' => 'failed_login',
            'old_values' => null,
            'new_values' => ['email' => $email],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }

    protected function logSuccessfulLogin(User $user): void
    {
        AuditLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'login',
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }

    protected function logLogout(User $user): void
    {
        AuditLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'logout',
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }
}
