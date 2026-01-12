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
                // CRM (manage)
                'customers.view' => true,
                'customers.manage' => true,
                'subscriptions.view' => true,
                'subscriptions.manage' => true,
                'provisionings.view' => true,
                'provisionings.manage' => true,

                // Master data
                'products.manage' => true,
                'routers.manage' => true,
                'brands.manage' => true,

                // Reports
                'reports.view' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'Finance/Kasir',
            'permissions' => [
                'customers.view' => true,
                'subscriptions.view' => true,

                'billing.manage' => true,
                'payments.manage' => true,

                'reports.view' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'Support',
            'permissions' => [
                'customers.view' => true,
                'subscriptions.view' => true,

                'tickets.manage' => true,
                'reports.view' => true,
            ],
        ]);

        UserGroup::query()->create([
            'name' => 'NOC/Technician',
            'permissions' => [
                'customers.view' => true,
                'subscriptions.view' => true,
                'provisionings.view' => true,
                'provisionings.manage' => true,

                'routers.tools' => true,
                'reports.view' => true,
            ],
        ]);
    }
}