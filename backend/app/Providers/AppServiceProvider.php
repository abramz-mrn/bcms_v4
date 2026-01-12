<?php

namespace App\Providers;

use App\Services\Notifications\Sms\DummySmsDriver;
use App\Services\Notifications\Sms\SmsDriver;
use App\Services\Notifications\Whatsapp\DummyWhatsappDriver;
use App\Services\Notifications\Whatsapp\WablasWhatsappDriver;
use App\Services\Notifications\Whatsapp\WhatsappDriver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsDriver::class, function () {
            return new DummySmsDriver();
        });

        $this->app->bind(WhatsappDriver::class, function () {
            $driver = env('WHATSAPP_DRIVER', 'dummy');

            return match ($driver) {
                'wablas' => new WablasWhatsappDriver(),
                default => new DummyWhatsappDriver(),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}