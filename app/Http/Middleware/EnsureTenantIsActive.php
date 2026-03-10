<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->tenant) {
            return response()->json(['message' => 'No tenant associated'], 403);
        }

        if (!$user->tenant->isActive()) {
            return response()->json([
                'message' => 'Tenant account is suspended or cancelled',
            ], 403);
        }

        return $next($request);
    }
}
