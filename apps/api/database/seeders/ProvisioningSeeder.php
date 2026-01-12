<?php

namespace Database\Seeders;

use App\Models\Provisioning;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProvisioningSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Yogi')->firstOrFail();
        $router = Router::query()->firstOrFail();
        $sub = Subscription::query()->firstOrFail();

        Provisioning::query()->create([
            'subscriptions_id' => $sub->id,
            'routers_id' => $router->id,
            'device_brand' => 'TP-Link',
            'device_type_device_sn' => 'Archer C6 / SN12345',
            'device_mac' => 'AA:BB:CC:DD:EE:FF',
            'device_conn' => 'PPPoE',
            'pppoe_name' => 'cust0001',
            'pppoe_password' => 'pppoe-secret',
            'activation_date' => now()->subDays(15)->toDateString(),
            'technisian_name' => 'Yogi',
            'technisian_notes' => 'Provisioning dummy',
            'created_by' => $creator->id,
        ]);
    }
}