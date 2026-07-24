<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Services\AIService;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->middleware('auth');
        $this->ai = $ai;
    }

    public function index()
    {
        $plans = auth()->user()->mealPlans()->with('items.recipe')->latest()->get();
        $currentPlan = $plans->first();
        return view('dashboard.meal-planner', compact('plans','currentPlan'));
    }

    public function generate(Request $request)
    {
        $result = $this->ai->generateMealPlan([
            'calorie_target' => $request->calorie_target ?? 2000,
            'diet_type'      => $request->diet_type ?? 'balanced',
            'days'           => $request->days ?? 7,
        ]);

        if (!($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'AI unavailable'], 503);
        }

        $plan = MealPlan::create([
            'user_id'        => auth()->id(),
            'name'           => 'AI Meal Plan - ' . now()->format('M j, Y'),
            'start_date'     => now()->toDateString(),
            'end_date'       => now()->addDays(($request->days ?? 7) - 1)->toDateString(),
            'calorie_target' => $request->calorie_target,
            'diet_type'      => $request->diet_type,
            'is_ai_generated'=> true,
        ]);

        foreach ($result['meal_plan'] ?? [] as $day) {
            foreach ($day['meals'] ?? [] as $meal) {
                MealPlanItem::create([
                    'meal_plan_id' => $plan->id,
                    'ai_recipe'    => $meal,
                    'meal_date'    => $day['date'] ?? now()->toDateString(),
                    'meal_type'    => $meal['meal_type'] ?? 'lunch',
                    'servings'     => 1,
                ]);
            }
        }

        return response()->json(['success' => true, 'plan' => $plan->load('items')]);
    }

    private function categorizeIngredient(string $name): string
    {
        $name = strtolower($name);
        if (preg_match('/chicken|beef|lamb|fish|shrimp|mutton|meat/', $name)) return 'Meat & Seafood';
        if (preg_match('/milk|cheese|butter|yogurt|cream|ghee/', $name)) return 'Dairy';
        if (preg_match('/onion|tomato|garlic|potato|carrot|spinach/', $name)) return 'Produce';
        if (preg_match('/rice|flour|bread|pasta|oat|wheat/', $name)) return 'Grains & Pantry';
        if (preg_match('/oil|salt|sugar|cumin|turmeric|masala|chili/', $name)) return 'Spices & Condiments';
        return 'Other';
    }
}
