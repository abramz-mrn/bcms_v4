<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['name' => 'PT. Trira Inti Utama'],
            [
                'initial' => 'TIU',
                'address' => 'Ruko Kemanggisan Blok O4 No. 6 Metland Cibitung',
                'city' => 'Kab. Bekasi',
                'state' => 'Jawa Barat',
                'pos' => '17530',
                'phone' => null,
                'email' => null,
                'npwp' => '50.520.877.7-413.000',
                'bank_account' => [
                    'bank_name' => 'Mandiri',
                    'account_number' => '156-00-2388849-0',
                    'account_name' => 'PT. Trira Inti Utama',
                ],
            ]
        );
    }
}