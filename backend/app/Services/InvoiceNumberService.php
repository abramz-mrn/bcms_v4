<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public function nextInvoiceNo(): string
    {
        $year = now()->format('Y');

        // Use DB sequence table approach
        $row = DB::table('counters')->where('key', "invoice:$year")->lockForUpdate()->first();

        if (!$row) {
            DB::table('counters')->insert(['key' => "invoice:$year", 'value' => 1]);
            $n = 1;
        } else {
            $n = (int)$row->value + 1;
            DB::table('counters')->where('key', "invoice:$year")->update(['value' => $n]);
        }

        return sprintf('INV/TIU/%s/%06d', $year, $n);
    }
}