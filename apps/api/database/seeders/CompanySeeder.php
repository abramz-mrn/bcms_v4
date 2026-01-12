<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::query()->create([
            'name' => 'PT. Trira Inti Utama',
            'alias' => 'Trira',
            'address' => 'Ruko Kemanggisan Blok O4 No. 6 Metland Cibitung',
            'city' => 'Kab. Bekasi',
            'state' => 'Jawa Barat',
            'pos' => '17530',
            'phone' => '021-000000',
            'email' => 'info@maroon-net.local',
            'npwp' => '50.520.877.7-413.000',
            'bank_account' => [
                'bank_name' => 'Mandiri',
                'account_number' => '156-00-2388849-0',
                'account_name' => 'PT. Trira Inti Utama',
            ],
        ]);
    }
}