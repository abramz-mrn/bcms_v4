<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code' => 'BASIC', 'name' => 'Paket Basic', 'price' => 150000, 'rate' => 11],
            ['code' => 'MEDIUM', 'name' => 'Paket Medium', 'price' => 250000, 'rate' => 11],
            ['code' => 'PREMIUM', 'name' => 'Paket Premium', 'price' => 350000, 'rate' => 11],
            ['code' => 'SOHO-20', 'name' => 'SOHO-20', 'price' => 600000, 'rate' => 11],
            ['code' => 'SOHO-50', 'name' => 'SOHO-50', 'price' => 1000000, 'rate' => 11],
            ['code' => 'SOHO-100', 'name' => 'SOHO-100', 'price' => 1750000, 'rate' => 11],
        ];

        foreach ($items as $it) {
            Product::updateOrCreate(
                ['code' => $it['code']],
                [
                    'name' => $it['name'],
                    'type' => 'Internet Services',
                    'description' => 'Seed sample product',
                    'market_segment' => 'Residensial',
                    'billing_cycle' => 'Monthly',
                    'price' => $it['price'],
                    'tax_rate' => $it['rate'],
                    'tax_included' => false,
                ]
            );
        }
    }
}