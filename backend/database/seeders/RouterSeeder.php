<?php

namespace Database\Seeders;

use App\Models\Router;
use Illuminate\Database\Seeder;

class RouterSeeder extends Seeder
{
    public function run(): void
    {
        Router::updateOrCreate(
            ['ip_address' => '10.0.0.1', 'api_port' => 8729],
            [
                'name' => 'RTR-POP-CIBITUNG-01',
                'location' => 'POP Cibitung',
                'description' => 'Dummy router (offline)',
                'ssh_port' => 22,
                'api_username' => 'admin',
                'api_password' => 'password',
                'tls_enabled' => true,
                'ssh_enabled' => false,
                'status' => 'offline',
                'sync_interval' => 300,
                'config_backup' => [],
            ]
        );
    }
}