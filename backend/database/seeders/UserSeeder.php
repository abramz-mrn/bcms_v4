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
        $company = Company::where('name', 'PT. Trira Inti Utama')->firstOrFail();

        $admin = UserGroup::where('name', 'Administrator')->firstOrFail();
        $sup = UserGroup::where('name', 'Supervisor')->firstOrFail();
        $fin = UserGroup::where('name', 'Finance/Kasir')->firstOrFail();
        $support = UserGroup::where('name', 'Support')->firstOrFail();

        $pw = Hash::make('PassWord@123');

        User::updateOrCreate(
            ['email' => 'abramz@maroon-net.id'],
            ['name' => 'Abramz', 'password' => $pw, 'user_group_id' => $admin->id, 'company_id' => $company->id, 'locked' => 'active']
        );

        User::updateOrCreate(
            ['email' => 'fandi@maroon-net.id'],
            ['name' => 'Fandi', 'password' => $pw, 'user_group_id' => $sup->id, 'company_id' => $company->id, 'locked' => 'active']
        );

        User::updateOrCreate(
            ['email' => 'meci@maroon-net.id'],
            ['name' => 'Meci', 'password' => $pw, 'user_group_id' => $fin->id, 'company_id' => $company->id, 'locked' => 'active']
        );

        User::updateOrCreate(
            ['email' => 'yogi@maroon-net.id'],
            ['name' => 'Yogi', 'password' => $pw, 'user_group_id' => $support->id, 'company_id' => $company->id, 'locked' => 'active']
        );
    }
}