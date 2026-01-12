<?php

namespace App\Services\Bandwidth;

class BandwidthCalculator
{
    /**
     * Input example: "10M/10M"
     * Output 50%: "5M/5M"
     * Keeps unit (K/M/G) and rounds down to integer.
     */
    public static function halfDuplex(string $rate): string
    {
        $parts = explode('/', $rate);
        if (count($parts) !== 2) return $rate;

        return self::halfOne(trim($parts[0])) . '/' . self::halfOne(trim($parts[1]));
    }

    private static function halfOne(string $v): string
    {
        // e.g. 10M, 512K, 1G
        if (!preg_match('/^([0-9.]+)\s*([KMG])$/i', $v, $m)) {
            return $v;
        }
        $num = (float) $m[1];
        $unit = strtoupper($m[2]);

        $half = floor($num / 2);
        if ($half < 1) $half = 1; // avoid 0

        return $half . $unit;
    }
}