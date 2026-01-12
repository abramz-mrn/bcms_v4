<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ...seeder lain yang sudah ada...

        $this->call([
            MessageTemplateSeeder::class,
        ]);
    }
}