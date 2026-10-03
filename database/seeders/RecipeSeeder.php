<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        $pakistaniCuisine = Category::where('name', 'Pakistani')->first();
        $italianCuisine = Category::where('name', 'Italian')->first();
        $dinnerMeal = Category::where('name', 'Dinner')->first();
        $lunchMeal = Category::where('name', 'Lunch')->first();

        // 1. Biryani
        $biryani = Recipe::create([
            'category_id'     => $dinnerMeal->id,
            'title'           => 'Authentic Chicken Biryani',
            'slug'            => 'authentic-chicken-biryani',
            'description'     => 'Fragrant Basmati rice layered with juicy, spicy marinated chicken, fresh mint, and caramelized onions.',
            'instructions'    => json_encode([
                '1. Wash and soak basmati rice for 30 minutes, then boil with spices till 70% cooked.',
                '2. Heat ghee, fry onions till golden brown, then set half aside for garnishing.',
                '3. Add chicken, garlic, ginger, and biryani spices; cook until chicken is tender.',
                '4. Layer chicken gravy and boiled rice in a heavy pot.',
                '5. Garnish with caramelized onions, saffron milk, fresh mint, and coriander.',
                '6. Seal the pot and cook on low heat (dum) for 15-20 minutes.'
            ]),
            'cuisine_type'    => 'Pakistani',
            'meal_type'       => 'dinner',
            'difficulty'      => 'medium',
            'cooking_time'    => 50,
            'servings'        => 4,
            'calories'        => 650,
            'protein'         => 38,
            'carbs'           => 70,
            'fats'            => 22,
            'spice_level'     => 'spicy',
            'is_halal'        => true,
            'is_featured'     => true,
            'is_seasonal'     => true,
            'season_tag'      => 'Eid',
            'image_url'       => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800&q=80',
            'health_score'    => 6,
            'avg_rating'      => 4.9,
            'review_count'    => 1,
            'view_count'      => 342,
        ]);

        // Attach ingredients
        $this->attachIngredient($biryani, 'Chicken Breast', '600g');
        $this->attachIngredient($biryani, 'Basmati Rice', '400g');
        $this->attachIngredient($biryani, 'Ghee', '3 tbsp');
        $this->attachIngredient($biryani, 'Onion', '2 large');
        $this->attachIngredient($biryani, 'Tomato', '2 medium');
        $this->attachIngredient($biryani, 'Yogurt', '150g');
        $this->attachIngredient($biryani, 'Garam Masala', '2 tsp');

        // 2. Chicken Karahi
        $karahi = Recipe::create([
            'category_id'     => $dinnerMeal->id,
            'title'           => 'Traditional Peshawari Chicken Karahi',
            'slug'            => 'traditional-peshawari-chicken-karahi',
            'description'     => 'A culinary masterpiece from Peshawar featuring tender chicken wok-cooked with tomatoes, garlic, ginger, and green chilies.',
            'instructions'    => json_encode([
                '1. Heat oil or ghee in a wok (karahi) and fry chicken on high heat until it changes color.',
                '2. Add ginger-garlic paste and salt, and sauté for 3 minutes.',
                '3. Add halved tomatoes. Cover and cook on medium heat until tomato skins loosen.',
                '4. Remove skins, crush tomatoes, and stir-fry until oil separates.',
                '5. Add freshly crushed black pepper, green chilies, and julienned ginger.',
                '6. Serve hot with naan or roti.'
            ]),
            'cuisine_type'    => 'Pakistani',
            'meal_type'       => 'dinner',
            'difficulty'      => 'easy',
            'cooking_time'    => 35,
            'servings'        => 3,
            'calories'        => 480,
            'protein'         => 42,
            'carbs'           => 12,
            'fats'            => 28,
            'spice_level'     => 'spicy',
            'is_halal'        => true,
            'is_featured'     => true,
            'image_url'       => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=800&q=80',
            'health_score'    => 7,
            'avg_rating'      => 4.8,
            'review_count'    => 1,
            'view_count'      => 210,
        ]);

        $this->attachIngredient($karahi, 'Chicken Breast', '500g');
        $this->attachIngredient($karahi, 'Tomato', '4 medium');
        $this->attachIngredient($karahi, 'Garlic Cloves', '4 cloves');
        $this->attachIngredient($karahi, 'Ginger Root', '2 inch');
        $this->attachIngredient($karahi, 'Green Chilies', '4 pcs');
        $this->attachIngredient($karahi, 'Ghee', '4 tbsp');
        $this->attachIngredient($karahi, 'Red Chili Powder', '1 tsp');

        // 3. Pasta Penne Arrabbiata
        $pasta = Recipe::create([
            'category_id'     => $lunchMeal->id,
            'title'           => 'Spicy Pasta Penne Arrabbiata',
            'slug'            => 'spicy-pasta-penne-arrabbiata',
            'description'     => 'Classic spicy Italian pasta dish with penne cooked in a rich, fiery garlic and tomato sauce.',
            'instructions'    => json_encode([
                '1. Boil penne pasta in salted water until al dente, then drain.',
                '2. Heat olive oil in a pan, add minced garlic and crushed red pepper flakes.',
                '3. Add crushed tomatoes, tomato paste, and simmer for 15 minutes.',
                '4. Stir in fresh basil leaves and adjust salt.',
                '5. Toss the penne pasta in the arrabbiata sauce.',
                '6. Serve hot garnished with freshly grated parmesan cheese.'
            ]),
            'cuisine_type'    => 'Italian',
            'meal_type'       => 'lunch',
            'difficulty'      => 'easy',
            'cooking_time'    => 25,
            'servings'        => 2,
            'calories'        => 380,
            'protein'         => 12,
            'carbs'           => 58,
            'fats'            => 10,
            'spice_level'     => 'medium',
            'is_halal'        => true,
            'is_vegetarian'   => true,
            'is_featured'     => false,
            'image_url'       => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800&q=80',
            'health_score'    => 8,
            'avg_rating'      => 4.5,
            'review_count'    => 0,
            'view_count'      => 98,
        ]);

        $this->attachIngredient($pasta, 'Pasta (Penne)', '200g');
        $this->attachIngredient($pasta, 'Tomato', '3 medium');
        $this->attachIngredient($pasta, 'Garlic Cloves', '3 cloves');
        $this->attachIngredient($pasta, 'Olive Oil', '2 tbsp');
        $this->attachIngredient($pasta, 'Red Chili Powder', '1 tsp');
        $this->attachIngredient($pasta, 'Cheddar Cheese', '50g'); // Substitute for parmesan
    }

    private function attachIngredient($recipe, $name, $quantity)
    {
        $ing = Ingredient::where('name', $name)->first();
        if ($ing) {
            $recipe->ingredients()->attach($ing->id, ['quantity' => $quantity]);
        }
    }
}
