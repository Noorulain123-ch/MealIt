<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run()
    {
        $ingredients = [
            // Proteins
            ['name' => 'Chicken Breast', 'category' => 'Protein', 'unit' => 'g'],
            ['name' => 'Beef Chuck', 'category' => 'Protein', 'unit' => 'g'],
            ['name' => 'Mutton', 'category' => 'Protein', 'unit' => 'g'],
            ['name' => 'Eggs', 'category' => 'Protein', 'unit' => 'pcs'],
            ['name' => 'Shrimp', 'category' => 'Protein', 'unit' => 'g'],
            ['name' => 'Tofu', 'category' => 'Protein', 'unit' => 'g'],
            ['name' => 'Salmon Fillet', 'category' => 'Protein', 'unit' => 'g'],
            
            // Vegetables
            ['name' => 'Onion', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Tomato', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Garlic Cloves', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Ginger Root', 'category' => 'Vegetable', 'unit' => 'g'],
            ['name' => 'Potato', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Bell Pepper', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Spinach', 'category' => 'Vegetable', 'unit' => 'g'],
            ['name' => 'Carrot', 'category' => 'Vegetable', 'unit' => 'pcs'],
            ['name' => 'Green Chilies', 'category' => 'Vegetable', 'unit' => 'pcs'],
            
            // Dairy & Oils
            ['name' => 'Milk', 'category' => 'Dairy', 'unit' => 'ml'],
            ['name' => 'Butter', 'category' => 'Dairy', 'unit' => 'g'],
            ['name' => 'Ghee', 'category' => 'Dairy', 'unit' => 'tbsp'],
            ['name' => 'Yogurt', 'category' => 'Dairy', 'unit' => 'g'],
            ['name' => 'Cheddar Cheese', 'category' => 'Dairy', 'unit' => 'g'],
            ['name' => 'Mozzarella Cheese', 'category' => 'Dairy', 'unit' => 'g'],
            ['name' => 'Olive Oil', 'category' => 'Dairy', 'unit' => 'ml'],
            
            // Grains & Pantry
            ['name' => 'Basmati Rice', 'category' => 'Grain', 'unit' => 'g'],
            ['name' => 'All-Purpose Flour', 'category' => 'Grain', 'unit' => 'g'],
            ['name' => 'Wheat Bread', 'category' => 'Grain', 'unit' => 'slices'],
            ['name' => 'Pasta (Penne)', 'category' => 'Grain', 'unit' => 'g'],
            ['name' => 'Sugar', 'category' => 'Grain', 'unit' => 'g'],
            ['name' => 'Soy Sauce', 'category' => 'Grain', 'unit' => 'ml'],
            
            // Spices & Seasonings
            ['name' => 'Salt', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Black Pepper', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Cumin Powder', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Coriander Powder', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Turmeric Powder', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Garam Masala', 'category' => 'Spice', 'unit' => 'tsp'],
            ['name' => 'Red Chili Powder', 'category' => 'Spice', 'unit' => 'tsp'],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::create($ing);
        }
    }
}
