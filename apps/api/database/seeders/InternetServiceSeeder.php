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
        $router = Router::query()->firstOrFail();

        $map = [
            ['code'=>'BASIC-10','rate'=>'10M/10M','limit'=>'5M/5M'],
            ['code'=>'MED-20','rate'=>'20M/20M','limit'=>'10M/10M'],
            ['code'=>'PREM-50','rate'=>'50M/50M','limit'=>'25M/25M'],
            ['code'=>'SOHO-20','rate'=>'20M/20M','limit'=>'10M/10M'],
            ['code'=>'SOHO-50','rate'=>'50M/50M','limit'=>'25M/25M'],
            ['code'=>'SOHO-100','rate'=>'100M/100M','limit'=>'50M/50M'],
        ];

        foreach ($map as $m) {
            $product = Product::query()->where('code',$m['code'])->firstOrFail();

            InternetService::query()->create([
                'products_id' => $product->id,
                'routers_id' => $router->id,
                'profile' => 'default',
                'rate_limit' => $m['rate'],
                'limit_at' => $m['limit'],
                'priority' => '8/8',
                'auto_soft_limit' => 3,
                'auto_suspend' => 7,
            ]);
        }
    }
}