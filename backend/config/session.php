<?php

return [
    'driver' => env('SESSION_DRIVER', 'cookie'),
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'bcms_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', '127.0.0.1'),
    'secure' => false,
    'http_only' => true,
    'same_site' => 'lax',
];