<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Alimentari',
            'Trasporti',
            'Casa',
            'Svago',
            'Salute',
            'Vestiti',
            'Regali',
            'Altro',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
