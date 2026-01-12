<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code'=>'BASIC-10','name'=>'Paket Basic','market_segment'=>'Residensial','price'=>150000],
            ['code'=>'MED-20','name'=>'Paket Medium','market_segment'=>'Residensial','price'=>250000],
            ['code'=>'PREM-50','name'=>'Paket Premium','market_segment'=>'Residensial','price'=>400000],
            ['code'=>'SOHO-20','name'=>'SOHO-20','market_segment'=>'SOHO/UMKM','price'=>600000],
            ['code'=>'SOHO-50','name'=>'SOHO-50','market_segment'=>'SOHO/UMKM','price'=>900000],
            ['code'=>'SOHO-100','name'=>'SOHO-100','market_segment'=>'SOHO/UMKM','price'=>1400000],
        ];

        foreach ($items as $it) {
            Product::query()->create([
                'code' => $it['code'],
                'name' => $it['name'],
                'type' => 'Internet Services',
                'description' => 'Seeded product',
                'market_segment' => $it['market_segment'],
                'billing_cycle' => 'Monthly',
                'price' => $it['price'],
                'tax_rate' => 11,
                'tax_included' => true,
            ]);
        }
    }
}