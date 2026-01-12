<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $groups = [
            [
                'name' => 'Administrator',
                'permissions' => json_encode(['*' => true]),
            ],
            [
                'name' => 'Supervisor',
                'permissions' => json_encode([
                    'brands.manage' => true,
                    'products.manage' => true,
                    'routers.manage' => true,
                    'reports.view' => true,
                ]),
            ],
            [
                'name' => 'Finance/Kasir',
                'permissions' => json_encode([
                    'billing.view' => true,
                    'billing.manage' => true,
                    'payments.manage' => true,
                ]),
            ],
            [
                'name' => 'Support',
                'permissions' => json_encode([
                    'tickets.manage' => true,
                    'tickets.view' => true,
                ]),
            ],
            [
                'name' => 'NOC/Technician',
                'permissions' => json_encode([
                    'provisioning.manage' => true,
                    'routers.tools' => true,
                ]),
            ],
        ];

        foreach ($groups as $g) {
            DB::table('user_groups')->updateOrInsert(
                ['name' => $g['name']],
                [
                    'permissions' => $g['permissions'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}