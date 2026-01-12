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
        $creator = User::where('email', 'abramz@maroon-net.id')->first();
        $router = Router::firstOrFail();

        $s1 = Subscription::where('subscription_no','SUB-000001')->firstOrFail();
        $s2 = Subscription::where('subscription_no','SUB-000002')->firstOrFail();

        Provisioning::updateOrCreate(
            ['subscription_id' => $s1->id],
            [
                'subscription_no' => $s1->subscription_no,
                'router_id' => $router->id,
                'device_conn' => 'PPPoE',
                'pppoe_name' => 'pppoe_sub_000001',
                'pppoe_password' => 'secret123',
                'device_brand' => 'Mikrotik',
                'device_type' => 'hAP lite',
                'device_sn' => 'SN-000001',
                'device_mac' => 'AA:BB:CC:DD:EE:01',
                'activation_date' => now()->subDays(28)->toDateString(),
                'technisian_name' => 'Technician A',
                'technisian_notes' => 'Installed successfully',
                'created_by' => $creator?->id,
            ]
        );

        Provisioning::updateOrCreate(
            ['subscription_id' => $s2->id],
            [
                'subscription_no' => $s2->subscription_no,
                'router_id' => $router->id,
                'device_conn' => 'Static-IP',
                'static_ip' => '10.10.10.10',
                'static_gateway' => '10.10.10.1',
                'device_brand' => 'TP-Link',
                'device_type' => 'CPE',
                'device_sn' => 'SN-000002',
                'device_mac' => 'AA:BB:CC:DD:EE:02',
                'activation_date' => now()->subDays(12)->toDateString(),
                'technisian_name' => 'Technician B',
                'technisian_notes' => 'Static IP configured',
                'created_by' => $creator?->id,
            ]
        );
    }
}