<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();

        return response()->json([
            'data' => $roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
                'users_count' => $role->users()->count(),
            ]),
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
                'users_count' => $role->users()->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
        ], 201);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if (in_array($role->name, ['super-admin', 'admin'])) {
            return response()->json([
                'message' => 'Cannot modify system roles',
            ], 422);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if (in_array($role->name, ['super-admin', 'admin', 'manager', 'accountant', 'sales', 'warehouse', 'viewer'])) {
            return response()->json([
                'message' => 'Cannot delete system roles',
            ], 422);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    public function permissions(): JsonResponse
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return end($parts);
        });

        return response()->json([
            'data' => $permissions->map(fn ($group) => $group->pluck('name')),
        ]);
    }
}
