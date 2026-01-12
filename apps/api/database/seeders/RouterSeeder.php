<?php

namespace Database\Seeders;

use App\Models\Router;
use Illuminate\Database\Seeder;

class RouterSeeder extends Seeder
{
    public function run(): void
    {
        Router::query()->create([
            'name' => 'POP-CIBITUNG-01',
            'location' => 'Cibitung',
            'description' => 'Dummy router offline',
            'ip_address' => '192.0.2.10',
            'api_port' => 8729,
            'ssh_port' => 22,
            'api_username' => 'admin',
            'api_password' => 'password',
            'tls_enabled' => true,
            'ssh_enabled' => true,
            'status' => 'offline',
            'config_backup' => ['last_backup' => null],
        ]);

        Router::query()->create([
            'name' => 'POP-TAMBUN-01',
            'location' => 'Tambun',
            'description' => 'Dummy router offline',
            'ip_address' => '192.0.2.11',
            'api_port' => 8729,
            'ssh_port' => 22,
            'api_username' => 'admin',
            'api_password' => 'password',
            'tls_enabled' => true,
            'ssh_enabled' => false,
            'status' => 'offline',
        ]);
    }
}