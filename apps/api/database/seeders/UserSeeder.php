<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrFail();

        $admin = UserGroup::query()->where('name','Administrator')->firstOrFail();
        $finance = UserGroup::query()->where('name','Finance/Kasir')->firstOrFail();
        $support = UserGroup::query()->where('name','Support')->firstOrFail();
        $noc = UserGroup::query()->where('name','NOC/Technician')->firstOrFail();

        $defaultPass = Hash::make('Password123!');

        User::query()->create([
            'name' => 'Abramz',
            'email' => 'abramz@maroon-net.local',
            'password' => $defaultPass,
            'user_groups_id' => $admin->id,
            'companies_id' => $company->id,
            'phone' => '081200000001',
            'nik' => 'ADM-0001',
            'locked' => 'active',
        ]);

        User::query()->create([
            'name' => 'Fandi',
            'email' => 'fandi@maroon-net.local',
            'password' => $defaultPass,
            'user_groups_id' => $finance->id,
            'companies_id' => $company->id,
            'phone' => '081200000002',
            'nik' => 'FIN-0001',
            'locked' => 'active',
        ]);

        User::query()->create([
            'name' => 'Meci',
            'email' => 'meci@maroon-net.local',
            'password' => $defaultPass,
            'user_groups_id' => $support->id,
            'companies_id' => $company->id,
            'phone' => '081200000003',
            'nik' => 'SUP-0001',
            'locked' => 'active',
        ]);

        User::query()->create([
            'name' => 'Yogi',
            'email' => 'yogi@maroon-net.local',
            'password' => $defaultPass,
            'user_groups_id' => $noc->id,
            'companies_id' => $company->id,
            'phone' => '081200000004',
            'nik' => 'NOC-0001',
            'locked' => 'active',
        ]);
    }
}