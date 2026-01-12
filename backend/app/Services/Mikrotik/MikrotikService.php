<?php

namespace App\Services\Mikrotik;

use App\Models\Router;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    public function pingTest(Router $router, string $targetIp, int $seconds = 5): array
    {
        // Stub implementation.
        // TODO: Implement RouterOS API ping via TLS + fallback SSH.
        Log::info('[MIKROTIK:PING:STUB]', [
            'router' => $router->id,
            'router_ip' => $router->ip_address,
            'target' => $targetIp,
            'seconds' => $seconds,
        ]);

        $samples = [];
        for ($i = 1; $i <= $seconds; $i++) {
            $samples[] = ['seq' => $i, 'time_ms' => rand(1, 30), 'ttl' => 64, 'ok' => true];
        }

        return [
            'router_id' => $router->id,
            'target' => $targetIp,
            'duration_seconds' => $seconds,
            'sent' => $seconds,
            'received' => $seconds,
            'loss_percent' => 0,
            'samples' => $samples,
        ];
    }
}