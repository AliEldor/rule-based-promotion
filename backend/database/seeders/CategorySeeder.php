<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $categories = [
            [
                'id' => 10,
                'name' => 'Electronics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'name' => 'Home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 99,
                'name' => 'Clearance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }  
    }
}
