<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log only API mutations (starter)
        if (str_starts_with($request->path(), 'api/')
            && in_array($request->method(), ['POST','PUT','PATCH','DELETE'], true)
        ) {
            AuditLog::create([
                'user_id' => $request->user()?->id,
                'user_name' => $request->user()?->name,
                'ip_address' => $request->ip(),
                'action' => strtolower($request->method()),
                'resource_type' => $request->path(),
                'old_value' => null,
                'new_value' => ['payload' => $request->except(['password'])],
                'description' => 'API mutation',
            ]);
        }

        return $response;
    }
}