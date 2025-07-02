<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'id' => 123,
                'name' => 'Widget A',
                'category_id' => 10,
                'unit_price' => 100.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 456,
                'name' => 'Gadget B',
                'category_id' => 20,
                'unit_price' => 80.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 789,
                'name' => 'Flash Deal C',
                'category_id' => 10,
                'unit_price' => 120.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 555,
                'name' => 'Intro SKU D',
                'category_id' => 20,
                'unit_price' => 60.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 888,
                'name' => 'Legacy Thing E',
                'category_id' => 99,
                'unit_price' => 50.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['id' => $product['id']],
                $product
            );
        }
    }
}
