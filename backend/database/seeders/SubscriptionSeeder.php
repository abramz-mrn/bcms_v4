<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('email', 'abramz@maroon-net.id')->first();

        $c1 = Customer::where('code','CUST-0001')->firstOrFail();
        $c2 = Customer::where('code','CUST-0002')->firstOrFail();

        $basic = Product::where('code','BASIC')->firstOrFail();
        $medium = Product::where('code','MEDIUM')->firstOrFail();

        Subscription::updateOrCreate(
            ['subscription_no' => 'SUB-000001'],
            [
                'customer_id' => $c1->id,
                'product_id' => $basic->id,
                'registration_date' => now()->subDays(30)->toDateString(),
                'installation_address' => 'Metland Cibitung',
                'email_consent' => true,
                'sms_consent' => true,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => $creator?->id,
            ]
        );

        Subscription::updateOrCreate(
            ['subscription_no' => 'SUB-000002'],
            [
                'customer_id' => $c2->id,
                'product_id' => $medium->id,
                'registration_date' => now()->subDays(15)->toDateString(),
                'installation_address' => 'Tambun Selatan',
                'email_consent' => true,
                'sms_consent' => false,
                'whatsapp_consent' => true,
                'status' => 'Active',
                'created_by' => $creator?->id,
            ]
        );
    }
}