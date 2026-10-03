<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Cuisines
            ['name' => 'Pakistani', 'type' => 'cuisine', 'icon' => 'fa-drumstick-bite', 'description' => 'Aromatic, rich, and highly flavored traditional dishes.'],
            ['name' => 'Indian', 'type' => 'cuisine', 'icon' => 'fa-pepper-hot', 'description' => 'Spicy and colorful vegetarian and non-vegetarian delicacies.'],
            ['name' => 'Italian', 'type' => 'cuisine', 'icon' => 'fa-pizza-slice', 'description' => 'Classic Mediterranean cuisine focusing on fresh ingredients, pasta, and pizza.'],
            ['name' => 'Chinese', 'type' => 'cuisine', 'icon' => 'fa-bowl-rice', 'description' => 'Delicious stir-fries, dim sums, and noodle dishes from East Asia.'],
            ['name' => 'Mexican', 'type' => 'cuisine', 'icon' => 'fa-taco', 'description' => 'Tacos, quesadillas, and rich spicy flavors with vibrant ingredients.'],
            ['name' => 'Turkish', 'type' => 'cuisine', 'icon' => 'fa-stroopwafel', 'description' => 'Savory kebabs, delicious flatbreads, and aromatic Turkish tea.'],
            ['name' => 'Thai', 'type' => 'cuisine', 'icon' => 'fa-leaf', 'description' => 'Harmonious blend of sweet, sour, salty, and spicy elements.'],
            ['name' => 'Mediterranean', 'type' => 'cuisine', 'icon' => 'fa-seedling', 'description' => 'Healthy olives, fresh herbs, fish, and rich olive oils.'],
            
            // Meal Types
            ['name' => 'Breakfast', 'type' => 'meal_type', 'icon' => 'fa-egg', 'description' => 'Energetic morning start meals.'],
            ['name' => 'Lunch', 'type' => 'meal_type', 'icon' => 'fa-burger', 'description' => 'Filling midday recipes.'],
            ['name' => 'Dinner', 'type' => 'meal_type', 'icon' => 'fa-utensils', 'description' => 'Delightful evening culinary creations.'],
            ['name' => 'Snack', 'type' => 'meal_type', 'icon' => 'fa-cookie', 'description' => 'Quick bites and dynamic appetizers.'],
            ['name' => 'Dessert', 'type' => 'meal_type', 'icon' => 'fa-ice-cream', 'description' => 'Sweet treats to end your meals.']
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'type'        => $cat['type'],
                'icon'        => $cat['icon'],
                'description' => $cat['description'],
                'is_active'   => true,
            ]);
        }
    }
}
