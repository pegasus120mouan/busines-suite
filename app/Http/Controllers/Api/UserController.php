<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with('roles')
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->role, fn ($q, $role) => $q->role($role))
            ->orderBy($request->sort_by ?? 'name', $request->sort_order ?? 'asc');

        return UserResource::collection($query->paginate($request->per_page ?? 15));
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = User::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'message' => 'User created successfully',
            'data' => new UserResource($user->load('roles')),
        ], 201);
    }

    public function show(User $user): UserResource
    {
        $this->authorizeForTenant($user);
        return new UserResource($user->load(['roles', 'permissions']));
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->authorizeForTenant($user);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status ?? $user->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorizeForTenant($user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Cannot delete your own account',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $data = $request->only(['name', 'email', 'phone']);

        if ($request->filled('new_password')) {
            $data['password'] = Hash::make($request->new_password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    protected function authorizeForTenant(User $user): void
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access');
        }
    }
}
