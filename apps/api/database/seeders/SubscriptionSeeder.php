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
        $creator = User::query()->where('name','Abramz')->firstOrFail();

        $cust1 = Customer::query()->where('code','CUST-0001')->firstOrFail();
        $cust2 = Customer::query()->where('code','CUST-0002')->firstOrFail();

        $basic = Product::query()->where('code','BASIC-10')->firstOrFail();
        $soho = Product::query()->where('code','SOHO-50')->firstOrFail();

        Subscription::query()->create([
            'customers_id' => $cust1->id,
            'products_id' => $basic->id,
            'registration_date' => now()->subDays(20)->toDateString(),
            'email_consent' => true,
            'sms_consent' => true,
            'whatsapp_consent' => true,
            'status' => 'Active',
            'created_by' => $creator->id,
        ]);

        Subscription::query()->create([
            'customers_id' => $cust2->id,
            'products_id' => $soho->id,
            'registration_date' => now()->subDays(10)->toDateString(),
            'email_consent' => true,
            'sms_consent' => false,
            'whatsapp_consent' => true,
            'status' => 'Registered',
            'created_by' => $creator->id,
        ]);
    }
}