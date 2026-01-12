<?php

namespace App\Services\Notifications\Whatsapp;

use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WablasWhatsappDriver implements WhatsappDriver
{
    public function send(string $to, string $message): void
    {
        $baseUrl = rtrim((string) env('WABLAS_BASE_URL', ''), '/');
        $token = (string) env('WABLAS_TOKEN', '');
        $timeout = (int) env('WABLAS_TIMEOUT', 10);

        if ($baseUrl === '' || $token === '') {
            throw new RuntimeException('WABLAS_BASE_URL / WABLAS_TOKEN is not configured');
        }

        $phone = PhoneNumber::normalizeWhatsapp($to);

        if (!$phone) {
            // skip invalid / landline
            Log::warning('[WA:WABLAS] skipped invalid phone', ['to' => $to]);
            return;
        }

        $url = $baseUrl . '/api/send-message';

        $res = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => $token,
            ])
            ->asMultipart()
            ->post($url, [
                ['name' => 'phone', 'contents' => $phone],
                ['name' => 'message', 'contents' => $message],
            ]);

        if (!$res->successful()) {
            Log::error('[WA:WABLAS] send failed', [
                'status' => $res->status(),
                'body' => $res->body(),
                'phone' => $phone,
            ]);
            throw new RuntimeException('WABLAS send-message failed: HTTP '.$res->status());
        }

        Log::info('[WA:WABLAS] sent', [
            'phone' => $phone,
            'status' => $res->status(),
        ]);
    }
}