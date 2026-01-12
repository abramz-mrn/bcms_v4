<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('health', function () {
    $this->info('ok');
})->purpose('Health check for console');