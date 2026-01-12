<?php

return [
    'server' => env('OCTANE_SERVER', 'roadrunner'),
    'https' => false,
    'listeners' => [
        'tick' => [
            \Laravel\Octane\Listeners\FlushTemporaryContainerInstances::class,
            \Laravel\Octane\Listeners\FlushQueuedCookies::class,
            \Laravel\Octane\Listeners\FlushSessionState::class,
            \Laravel\Octane\Listeners\FlushAuthenticationState::class,
        ],
    ],
];