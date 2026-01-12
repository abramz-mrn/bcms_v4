<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Normalize Indonesian MSISDN for WhatsApp.
     * - removes non-digits
     * - if starts with "0" => replace with "62"
     * - if starts with "62" => keep
     * - landline "021..." => return null (skip)
     */
    public static function normalizeWhatsapp(string $raw): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $raw ?? '');
        if ($digits === '' || strlen($digits) < 8) return null;

        // Skip landline like 021xxxx
        if (str_starts_with($digits, '021')) {
            return null;
        }

        // Convert 08xx / 0xxx => 62xxx
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // Must start with country code 62 now
        if (!str_starts_with($digits, '62')) {
            return null;
        }

        return $digits;
    }
}