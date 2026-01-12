<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Log only mutating requests by default (POST/PUT/PATCH/DELETE),
     * and also allow explicit logging via header for special actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Capture request snapshot (before)
        $shouldLog = in_array($request->method(), ['POST','PUT','PATCH','DELETE'], true);

        $response = $next($request);

        // If unauthenticated, skip (you can change this if you want log guest actions)
        if (!$user) {
            return $response;
        }

        // Only log when request is successful-ish (or always log? choose: here we log all mutating, even errors)
        if ($shouldLog) {
            $this->writeLog($request, $response);
        }

        return $response;
    }

    protected function writeLog(Request $request, Response $response): void
    {
        $user = $request->user();

        $action = match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($request->method()),
        };

        // Resource type: try to infer from route name / uri
        $resourceType = $request->route()?->getName()
            ?: ($request->route()?->uri() ?? $request->path());

        // Avoid logging sensitive fields
        $payload = $request->except([
            'password',
            'pppoe_password',
            'api_password',
            'token',
            'secret',
        ]);

        // Keep payload small (starter). For full audit trail, we can store full json.
        $newValue = [
            'path' => $request->path(),
            'method' => $request->method(),
            'status' => $response->getStatusCode(),
            'payload' => $payload,
        ];

        AuditLog::query()->create([
            'users_id' => $user->id,
            'users_name' => $user->name,
            'ip_address' => $request->ip(),
            'action' => $action,
            'resource_type' => (string) $resourceType,
            'old_value' => null,
            'new_value' => $newValue,
            'description' => $this->buildDescription($request, $action),
        ]);
    }

    protected function buildDescription(Request $request, string $action): string
    {
        $uri = $request->route()?->uri() ?? $request->path();
        return strtoupper($action) . " " . $uri;
    }
}