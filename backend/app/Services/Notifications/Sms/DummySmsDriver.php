<?php

namespace App\Services\Notifications\Sms;

use Illuminate\Support\Facades\Log;

class DummySmsDriver implements SmsDriver
{
    public function send(string $to, string $message): void
    {
        Log::info('[SMS:DUMMY]', ['to' => $to, 'message' => $message]);
    }
}