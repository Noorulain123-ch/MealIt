<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecipeImportSeeder extends Seeder
{
    public function run()
    {
        $dinnerMeal = Category::where('name', 'Dinner')->first();
        if (!$dinnerMeal) {
            $dinnerMeal = Category::create(['name' => 'Dinner', 'slug' => 'dinner', 'type' => 'meal', 'is_active' => true]);
        }

        // Keywords to fetch a diverse, large selection of recipes
        $keywords = ['chicken', 'beef', 'pasta', 'salad', 'soup', 'dessert'];
        $importedCount = 0;

        foreach ($keywords as $kw) {
            try {
                $response = Http::get("https://www.themealdb.com/api/json/v1/1/search.php", ['s' => $kw]);
                
                if ($response->successful() && !empty($response->json('meals'))) {
                    $meals = $response->json('meals');
                    
                    // Take up to 6 recipes per keyword to get 30+ recipes in total!
                    $subset = array_slice($meals, 0, 6);

                    foreach ($subset as $m) {
                        $title = $m['strMeal'] ?? 'Unknown Dish';
                        $slug = Str::slug($title);

                        // Prevent duplicate seeding
                        if (Recipe::where('slug', $slug)->exists()) {
                            continue;
                        }

                        // Parse instructions into an array of clean lines
                        $rawInstructions = $m['strInstructions'] ?? 'Follow standard directions.';
                        $instructionsArray = array_filter(
                            array_map('trim', explode("\r\n", $rawInstructions)),
                            fn($line) => strlen($line) > 5
                        );
                        
                        if (empty($instructionsArray)) {
                            $instructionsArray = [$rawInstructions];
                        }

                        // Create the recipe record
                        $recipe = Recipe::create([
                            'category_id'     => $dinnerMeal->id,
                            'title'           => $title,
                            'slug'            => $slug . '-' . Str::random(3),
                            'description'     => 'A highly delicious ' . ($m['strArea'] ?? 'International') . ' style recipe featuring authentic elements.',
                            'instructions'    => json_encode(array_values($instructionsArray)),
                            'cuisine_type'    => $m['strArea'] ?? 'International',
                            'meal_type'       => rand(0, 1) ? 'lunch' : 'dinner',
                            'difficulty'      => ['easy', 'medium', 'beginner'][rand(0, 2)],
                            'cooking_time'    => rand(15, 60),
                            'servings'        => rand(2, 6),
                            'calories'        => rand(320, 720),
                            'protein'         => rand(12, 45),
                            'carbs'           => rand(20, 85),
                            'fats'            => rand(6, 26),
                            'spice_level'     => ['mild', 'medium', 'spicy'][rand(0, 2)],
                            'is_halal'        => true,
                            'is_featured'     => rand(0, 10) > 7,
                            'image_url'       => $m['strMealThumb'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80',
                            'health_score'    => rand(5, 9),
                            'avg_rating'      => number_format(rand(43, 50) / 10, 1),
                            'review_count'    => rand(0, 10),
                            'view_count'      => rand(40, 480),
                        ]);

                        // Seed ingredient connections on-the-fly
                        for ($i = 1; $i <= 20; $i++) {
                            $ingName = $m['strIngredient' . $i] ?? '';
                            $ingQty = $m['strMeasure' . $i] ?? '';

                            if (!empty(trim($ingName))) {
                                // Find or create ingredient in the database
                                $ing = Ingredient::firstOrCreate(
                                    ['name' => trim(ucwords($ingName))],
                                    [
                                        'slug' => Str::slug($ingName),
                                        'category' => 'Pantry',
                                        'image_url' => 'https://www.themealdb.com/images/ingredients/' . urlencode($ingName) . '-Small.png'
                                    ]
                                );

                                $recipe->ingredients()->attach($ing->id, [
                                    'quantity' => trim($ingQty) ?: '1 unit',
                                    'is_optional' => false
                                ]);
                            }
                        }

                        $importedCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to import keyword {$kw}: " . $e->getMessage());
            }
        }
    }
}
