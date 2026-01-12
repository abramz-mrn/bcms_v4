<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'PT. Trira Inti Utama')->firstOrFail();

        Brand::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Maroon-NET'],
            ['description' => 'ISP brand']
        );
    }
}