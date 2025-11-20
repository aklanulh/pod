<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\StockMovementSeeder;
use Database\Seeders\StockOpnameSeeder;
use Database\Seeders\CustomerScheduleSeeder;
use Database\Seeders\KsoRoiSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductCategorySeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            StockMovementSeeder::class,
            StockOpnameSeeder::class,
            CustomerScheduleSeeder::class,
            KsoRoiSeeder::class,
        ]);
    }
}
