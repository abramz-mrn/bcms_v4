<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perms = $user->group?->permissions ?? [];

        if (is_array($perms)) {
            if (($perms['*'] ?? false) === true) {
                return $next($request);
            }
            if (($perms[$permission] ?? false) === true) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Forbidden',
            'required_permission' => $permission,
        ], 403);
    }
}