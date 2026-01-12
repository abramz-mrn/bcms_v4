<?php

namespace App\Services\Mikrotik;

use App\Models\Router;
use RuntimeException;

class MikrotikClientFactory
{
    public static function make(Router $router): MikrotikClientContract
    {
        $errors = [];

        if ($router->tls_enabled) {
            try {
                $client = new RouterOsApiTlsClient($router);
                // light smoke test (optional): call a cheap command
                $client->systemIdentity();
                return $client;
            } catch (\Throwable $e) {
                $errors[] = 'TLS API failed: '.$e->getMessage();
            }
        }

        if ($router->ssh_enabled) {
            try {
                $client = new RouterSshClient($router);
                $client->systemIdentity();
                return $client;
            } catch (\Throwable $e) {
                $errors[] = 'SSH failed: '.$e->getMessage();
            }
        }

        throw new RuntimeException('Router connection failed. '.implode(' | ', $errors));
    }
}