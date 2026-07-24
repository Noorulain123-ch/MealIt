<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\AiGeneration;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    // ─── Recipe Generation ───────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'ingredients'   => 'required|array|min:1|max:20',
            'ingredients.*' => 'string|max:100',
            'cuisine'       => 'nullable|string',
            'spice_level'   => 'nullable|in:mild,medium,spicy,extra_spicy',
            'dietary'       => 'nullable|array',
            'difficulty'    => 'nullable|string',
            'max_time'      => 'nullable|integer|min:5|max:480',
            'health_goal'   => 'nullable|string',
            'mood'          => 'nullable|string',
        ]);

        $result = $this->ai->generateRecipes($request->all());

        // Log the generation attempt
        try {
            AiGeneration::create([
                'user_id'     => auth()->id(),
                'type'        => 'recipe',
                'input_data'  => $request->all(),
                'output_data' => $result,
                'model_used'  => $result['model_used'] ?? 'gemini-1.5-flash',
                'success'     => $result['success'] ?? false,
            ]);
        } catch (\Exception $e) {
            // Logging failure should not block response
        }

        return response()->json($result);
    }

    // ─── Chat ────────────────────────────────────────────────
    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'context'  => 'nullable|string',
        ]);

        $result = $this->ai->chat($request->messages, $request->context ?? '');
        return response()->json($result);
    }

    // ─── Ingredient Substitutions ────────────────────────────
    public function substitute(Request $request)
    {
        $request->validate([
            'ingredient'     => 'required|string|max:100',
            'recipe_context' => 'nullable|array',
        ]);

        $result = $this->ai->substitute($request->ingredient, $request->recipe_context ?? []);
        return response()->json($result);
    }

    // ─── Meal Plan ───────────────────────────────────────────
    public function mealPlan(Request $request)
    {
        $request->validate([
            'calorie_target' => 'nullable|integer|min:500|max:5000',
            'diet_type'      => 'nullable|string',
            'days'           => 'nullable|integer|min:1|max:14',
        ]);

        $result = $this->ai->generateMealPlan($request->all());
        return response()->json($result);
    }

    // ─── Mood Recipe ─────────────────────────────────────────
    public function moodRecipe(Request $request)
    {
        $request->validate(['mood' => 'required|string|max:50']);
        $result = $this->ai->moodRecipe($request->mood, $request->preferences ?? []);
        return response()->json($result);
    }

    // ─── Leftover Recipe ─────────────────────────────────────
    public function leftoverRecipe(Request $request)
    {
        $request->validate(['ingredients' => 'required|array|min:1']);
        $result = $this->ai->leftoverRecipe($request->ingredients);
        return response()->json($result);
    }

    // ─── Cooking Tips ────────────────────────────────────────
    public function cookingTips(Request $request)
    {
        $request->validate([
            'recipe_name' => 'required|string|max:200',
            'cuisine'     => 'nullable|string|max:100',
        ]);

        $result = $this->ai->cookingTips($request->recipe_name, $request->cuisine ?? '');
        return response()->json($result);
    }

    // ─── Health Check ─────────────────────────────────────────
    public function health()
    {
        return response()->json([
            'ai_service'    => $this->ai->isAvailable() ? 'online' : 'offline (no API key)',
            'provider'      => 'gemini-direct',
            'timestamp'     => now()->toISOString(),
        ]);
    }
}
