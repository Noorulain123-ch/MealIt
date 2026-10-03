<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | OLD 3 RECIPES
        |--------------------------------------------------------------------------
        */

        $pakistaniCategory = Category::where('name', 'Pakistani')->first();
        $italianCategory = Category::where('name', 'Italian')->first();
        $dinnerCategory = Category::where('name', 'Dinner')->first();
        $lunchCategory = Category::where('name', 'Lunch')->first();

        /*
        |--------------------------------------------------------------------------
        | 1. Authentic Chicken Biryani
        |--------------------------------------------------------------------------
        */

        $biryani = Recipe::updateOrCreate(
            ['slug' => 'authentic-chicken-biryani'],
            [
                'title' => 'Authentic Chicken Biryani',
                'description' => 'A flavorful and aromatic Pakistani chicken biryani.',
                'instructions' => 'Marinate chicken with spices and yogurt. Cook onions until golden. Add chicken and cook until tender. Add rice and cook until fully done. Layer rice and chicken, then steam on low heat.',
                'prep_time' => 30,
                'cook_time' => 60,
                'servings' => 6,
                'difficulty' => 'Medium',
                'category_id' => $pakistaniCategory?->id,
                'cuisine_type' => 'Pakistani',
                'user_id' => null,
                'video_url' => null,
                'is_ai_generated' => false,
                'is_seasonal' => false,
            ]
        );

        $biryaniIngredients = [
            ['name' => 'Chicken', 'quantity' => '1', 'unit' => 'kg'],
            ['name' => 'Basmati Rice', 'quantity' => '1', 'unit' => 'kg'],
            ['name' => 'Yogurt', 'quantity' => '1', 'unit' => 'cup'],
            ['name' => 'Onions', 'quantity' => '3', 'unit' => 'large'],
            ['name' => 'Tomatoes', 'quantity' => '3', 'unit' => 'medium'],
            ['name' => 'Biryani Masala', 'quantity' => '2', 'unit' => 'tbsp'],
        ];

        foreach ($biryaniIngredients as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $ingredientData['name']],
                [
                    'slug' => Str::slug($ingredientData['name']),
                ]
            );

            $biryani->ingredients()->syncWithoutDetaching([
                $ingredient->id => [
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Traditional Peshawari Chicken Karahi
        |--------------------------------------------------------------------------
        */

        $karahi = Recipe::updateOrCreate(
            ['slug' => 'traditional-peshawari-chicken-karahi'],
            [
                'title' => 'Traditional Peshawari Chicken Karahi',
                'description' => 'A traditional spicy and flavorful Peshawari chicken karahi.',
                'instructions' => 'Heat oil and fry chicken until lightly golden. Add tomatoes, ginger, garlic and spices. Cook until tomatoes soften and oil separates. Garnish with fresh coriander and green chilies.',
                'prep_time' => 15,
                'cook_time' => 40,
                'servings' => 4,
                'difficulty' => 'Medium',
                'category_id' => $pakistaniCategory?->id,
                'cuisine_type' => 'Pakistani',
                'user_id' => null,
                'video_url' => null,
                'is_ai_generated' => false,
                'is_seasonal' => false,
            ]
        );

        $karahiIngredients = [
            ['name' => 'Chicken', 'quantity' => '1', 'unit' => 'kg'],
            ['name' => 'Tomatoes', 'quantity' => '6', 'unit' => 'medium'],
            ['name' => 'Green Chilies', 'quantity' => '6', 'unit' => 'pieces'],
            ['name' => 'Ginger', 'quantity' => '2', 'unit' => 'tbsp'],
            ['name' => 'Garlic', 'quantity' => '1', 'unit' => 'tbsp'],
            ['name' => 'Black Pepper', 'quantity' => '1', 'unit' => 'tsp'],
        ];

        foreach ($karahiIngredients as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $ingredientData['name']],
                [
                    'slug' => Str::slug($ingredientData['name']),
                ]
            );

            $karahi->ingredients()->syncWithoutDetaching([
                $ingredient->id => [
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Spicy Pasta Penne Arrabbiata
        |--------------------------------------------------------------------------
        */

        $pasta = Recipe::updateOrCreate(
            ['slug' => 'spicy-pasta-penne-arrabbiata'],
            [
                'title' => 'Spicy Pasta Penne Arrabbiata',
                'description' => 'A classic Italian pasta with a spicy tomato sauce.',
                'instructions' => 'Boil pasta until al dente. Prepare tomato sauce with garlic, chili flakes and tomatoes. Mix cooked pasta with the sauce and serve hot with cheese and fresh herbs.',
                'prep_time' => 10,
                'cook_time' => 25,
                'servings' => 4,
                'difficulty' => 'Easy',
                'category_id' => $italianCategory?->id,
                'cuisine_type' => 'Italian',
                'user_id' => null,
                'video_url' => null,
                'is_ai_generated' => false,
                'is_seasonal' => false,
            ]
        );

        $pastaIngredients = [
            ['name' => 'Penne Pasta', 'quantity' => '400', 'unit' => 'g'],
            ['name' => 'Tomatoes', 'quantity' => '4', 'unit' => 'medium'],
            ['name' => 'Garlic', 'quantity' => '4', 'unit' => 'cloves'],
            ['name' => 'Red Chili Flakes', 'quantity' => '1', 'unit' => 'tsp'],
            ['name' => 'Olive Oil', 'quantity' => '3', 'unit' => 'tbsp'],
            ['name' => 'Parmesan Cheese', 'quantity' => '50', 'unit' => 'g'],
        ];

        foreach ($pastaIngredients as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $ingredientData['name']],
                [
                    'slug' => Str::slug($ingredientData['name']),
                ]
            );

            $pasta->ingredients()->syncWithoutDetaching([
                $ingredient->id => [
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | NEW 20 RECIPES
        |--------------------------------------------------------------------------
        */

        // Get the first available category for the additional recipes.
        $defaultCategoryId = DB::table('categories')->value('id');

        $recipes = [
            [
                'title' => 'Chicken Biryani',
                'slug' => 'chicken-biryani',
                'cuisine_type' => 'Pakistani',
            ],
            [
                'title' => 'Creamy Chicken Pasta',
                'slug' => 'creamy-chicken-pasta',
                'cuisine_type' => 'Italian',
            ],
            [
                'title' => 'Vegetable Salad',
                'slug' => 'vegetable-salad',
                'cuisine_type' => 'Mediterranean',
            ],
            [
                'title' => 'Classic Pancakes',
                'slug' => 'classic-pancakes',
                'cuisine_type' => 'American',
            ],
            [
                'title' => 'Beef Tacos',
                'slug' => 'beef-tacos',
                'cuisine_type' => 'Mexican',
            ],
            [
                'title' => 'Grilled Chicken Sandwich',
                'slug' => 'grilled-chicken-sandwich',
                'cuisine_type' => 'American',
            ],
            [
                'title' => 'Lentil Curry',
                'slug' => 'lentil-curry',
                'cuisine_type' => 'Indian',
            ],
            [
                'title' => 'Fresh Fruit Smoothie',
                'slug' => 'fresh-fruit-smoothie',
                'cuisine_type' => 'International',
            ],
            [
                'title' => 'Vegetable Pasta',
                'slug' => 'vegetable-pasta',
                'cuisine_type' => 'Italian',
            ],
            [
                'title' => 'Healthy Chicken Soup',
                'slug' => 'healthy-chicken-soup',
                'cuisine_type' => 'International',
            ],
            [
                'title' => 'Chocolate Cake',
                'slug' => 'chocolate-cake',
                'cuisine_type' => 'American',
            ],
            [
                'title' => 'French Toast',
                'slug' => 'french-toast',
                'cuisine_type' => 'French',
            ],
            [
                'title' => 'Classic Hummus',
                'slug' => 'classic-hummus',
                'cuisine_type' => 'Middle Eastern',
            ],
            [
                'title' => 'Grilled Chicken Breast',
                'slug' => 'grilled-chicken-breast',
                'cuisine_type' => 'American',
            ],
            [
                'title' => 'Mediterranean Chickpea Salad',
                'slug' => 'mediterranean-chickpea-salad',
                'cuisine_type' => 'Mediterranean',
            ],
            [
                'title' => 'Grilled Beef Steak',
                'slug' => 'grilled-beef-steak',
                'cuisine_type' => 'American',
            ],
            [
                'title' => 'Spicy Vegetable Curry',
                'slug' => 'spicy-vegetable-curry',
                'cuisine_type' => 'Indian',
            ],
            [
                'title' => 'Grilled Salmon',
                'slug' => 'grilled-salmon',
                'cuisine_type' => 'Mediterranean',
            ],
            [
                'title' => 'Mango Lassi',
                'slug' => 'mango-lassi',
                'cuisine_type' => 'Pakistani',
            ],
            [
                'title' => 'Keto Egg Breakfast',
                'slug' => 'keto-egg-breakfast',
                'cuisine_type' => 'International',
            ],
        ];

        foreach ($recipes as $recipe) {
            DB::table('recipes')->updateOrInsert(
                ['slug' => $recipe['slug']],
                [
                    'title' => $recipe['title'],
                    'description' => 'A delicious and easy-to-prepare ' . $recipe['title'] . '.',
                    'instructions' => 'Prepare the ingredients. Cook according to the recipe requirements. Serve fresh and enjoy.',
                    'prep_time' => 15,
                    'cook_time' => 30,
                    'servings' => 4,
                    'difficulty' => 'Easy',
                    'category_id' => $defaultCategoryId,
                    'cuisine_type' => $recipe['cuisine_type'],
                    'user_id' => null,
                    'video_url' => null,
                    'is_ai_generated' => false,
                    'is_seasonal' => false,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('23 recipes are now available in MealIt!');
    }
}