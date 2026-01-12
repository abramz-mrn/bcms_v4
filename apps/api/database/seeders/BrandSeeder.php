<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrFail();

        Brand::query()->create([
            'companies_id' => $company->id,
            'name' => 'Maroon-NET',
            'description' => 'Brand ISP Maroon-NET (dummy seed)',
        ]);
    }
}