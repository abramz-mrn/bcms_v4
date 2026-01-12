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
        $isAdmin = (bool)($perms['*'] ?? false);

        if (!$isAdmin && empty($perms[$permission])) {
            return response()->json(['message' => 'Forbidden', 'permission' => $permission], 403);
        }

        return $next($request);
    }
}