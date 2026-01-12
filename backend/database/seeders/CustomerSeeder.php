<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('email', 'abramz@maroon-net.id')->first();

        $customers = [
            ['code' => 'CUST-0001', 'name' => 'Budi Santoso', 'phone' => '081200000001', 'email' => 'budi@example.com', 'group_area' => 'Cibitung'],
            ['code' => 'CUST-0002', 'name' => 'Siti Aminah', 'phone' => '081200000002', 'email' => 'siti@example.com', 'group_area' => 'Tambun'],
            ['code' => 'CUST-0003', 'name' => 'PT Maju Jaya', 'phone' => '0217000003', 'email' => 'admin@majujaya.co.id', 'group_area' => 'Cikarang'],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(
                ['code' => $c['code']],
                $c + [
                    'address' => 'Kab. Bekasi',
                    'city' => 'Kab. Bekasi',
                    'state' => 'Jawa Barat',
                    'pos' => '17530',
                    'created_by' => $creator?->id,
                ]
            );
        }
    }
}