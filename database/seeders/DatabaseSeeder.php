<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            UserSeeder::class,
            RecipeSeeder::class,
        ]);
    }
}
