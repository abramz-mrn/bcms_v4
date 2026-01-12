<?php

namespace Database\Seeders;

use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        UserGroup::query()->create([
            'name' => 'Administrator',
            'permissions' => ['*' => true],
        ]);

        UserGroup::query()->create([
            'name' => 'Supervisor',
            'permissions' => [
                'products.manage' => true,
                'routers.manage' => true,
                'brands.manage' => true,
                'reports.view' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'Finance/Kasir',
            'permissions' => [
                'billing.manage' => true,
                'payments.manage' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'Support',
            'permissions' => [
                'tickets.manage' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'NOC/Technician',
            'permissions' => [
                'provisioning.manage' => true,
                'routers.tools' => true,
            ],
        ]);
    }
}