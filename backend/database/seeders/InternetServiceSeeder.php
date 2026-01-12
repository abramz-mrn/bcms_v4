<?php

namespace Database\Seeders;

use App\Models\InternetService;
use App\Models\Product;
use App\Models\Router;
use Illuminate\Database\Seeder;

class InternetServiceSeeder extends Seeder
{
    public function run(): void
    {
        $router = Router::firstOrFail();

        $map = [
            'BASIC' => ['rate_limit' => '5M/5M', 'limit_at' => '3M/3M', 'priority' => '8/8'],
            'MEDIUM' => ['rate_limit' => '10M/10M', 'limit_at' => '5M/5M', 'priority' => '8/8'],
            'PREMIUM' => ['rate_limit' => '20M/20M', 'limit_at' => '10M/10M', 'priority' => '8/8'],
            'SOHO-20' => ['rate_limit' => '20M/20M', 'limit_at' => '10M/10M', 'priority' => '6/6'],
            'SOHO-50' => ['rate_limit' => '50M/50M', 'limit_at' => '25M/25M', 'priority' => '5/5'],
            'SOHO-100' => ['rate_limit' => '100M/100M', 'limit_at' => '50M/50M', 'priority' => '4/4'],
        ];

        foreach ($map as $code => $conf) {
            $product = Product::where('code', $code)->firstOrFail();

            InternetService::updateOrCreate(
                ['product_id' => $product->id, 'router_id' => $router->id],
                [
                    'profile' => $code,
                    'rate_limit' => $conf['rate_limit'],
                    'limit_at' => $conf['limit_at'],
                    'priority' => $conf['priority'],
                    'start_date' => now()->startOfMonth()->toDateString(),
                    'due_date' => now()->startOfMonth()->addDays(20)->toDateString(),
                    'auto_soft_limit' => 3,
                    'auto_suspend' => 7,
                ]
            );
        }
    }
}