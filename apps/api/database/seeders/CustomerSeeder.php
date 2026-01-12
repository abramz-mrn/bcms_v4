<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Abramz')->firstOrFail();

        Customer::query()->create([
            'code' => 'CUST-0001',
            'name' => 'Budi Santoso',
            'id_card_number' => '3201xxxxxxxxxxxx',
            'address' => 'Perumahan Dummy Blok A1',
            'city' => 'Kab. Bekasi',
            'state' => 'Jawa Barat',
            'pos' => '17530',
            'group_area' => 'Cibitung',
            'phone' => '081300000001',
            'email' => 'budi@example.local',
            'notes' => 'Seed customer 1',
            'created_by' => $creator->id,
        ]);

        Customer::query()->create([
            'code' => 'CUST-0002',
            'name' => 'Siti Aminah',
            'address' => 'Ruko Dummy No.2',
            'city' => 'Kab. Bekasi',
            'state' => 'Jawa Barat',
            'pos' => '17530',
            'group_area' => 'Tambun',
            'phone' => '081300000002',
            'email' => 'siti@example.local',
            'notes' => 'Seed customer 2',
            'created_by' => $creator->id,
        ]);
    }
}