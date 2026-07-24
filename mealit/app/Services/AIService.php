<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AIService
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function isAvailable(): bool
    {
        return $this->gemini->isAvailable();
    }

    // ─────────────────────────────────────────────────────────
    // Recipe Generation
    // ─────────────────────────────────────────────────────────
    public function generateRecipes(array $inputs): array
    {
        try {
            $system = "You are ChefAI, an elite culinary AI. Always output strictly valid JSON arrays of recipe objects. " .
                      "Each recipe must have: name, description, cuisine, difficulty, cooking_time (int minutes), " .
                      "servings (int), spice_level, calories_per_serving (int), protein (int grams), carbs (int grams), " .
                      "fats (int grams), health_score (1-10), ingredients (array of {name, quantity}), " .
                      "steps (array of strings), cooking_tips (array of strings), substitutions (object), mood_match (string). " .
                      "Return exactly 3 recipes. Output ONLY a JSON array, no markdown, no extra text.";

            $ings      = implode(', ', $inputs['ingredients'] ?? []);
            $cuisine   = $inputs['cuisine']     ?? 'Any';
            $spice     = $inputs['spice_level'] ?? 'medium';
            $dietary   = implode(', ', $inputs['dietary'] ?? []);
            $maxTime   = $inputs['max_time']    ?? 60;
            $goal      = $inputs['health_goal'] ?? 'maintenance';
            $mood      = $inputs['mood']        ?? 'any';
            $diff      = $inputs['difficulty']  ?? 'easy';

            $prompt = "Generate 3 creative recipes using these ingredients: {$ings}. " .
                      "Cuisine: {$cuisine}. Spice level: {$spice}. Dietary: {$dietary}. " .
                      "Max cooking time: {$maxTime} minutes. Health goal: {$goal}. Mood: {$mood}. Difficulty: {$diff}. " .
                      "Return a JSON array of 3 recipe objects.";

            $recipes = $this->gemini->generateJSON($prompt, $system);

            // If Gemini returns a wrapped object, unwrap it
            if (isset($recipes['recipes'])) {
                $recipes = $recipes['recipes'];
            }

            return [
                'success'    => true,
                'recipes'    => is_array($recipes) ? $recipes : [],
                'model_used' => 'gemini-2.5-flash',
            ];
        } catch (\Exception $e) {
            Log::error('Recipe generation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Chat
    // ─────────────────────────────────────────────────────────
    public function chat(array $messages, string $context = ''): array
    {
        try {
            $system = "You are ChefAI, a warm and expert culinary AI assistant. " .
                      "You help users with recipes, cooking techniques, ingredient substitutions, meal planning, and nutrition. " .
                      "Be friendly, concise, and always practical. Use markdown for emphasis (*bold*, *italic*). " .
                      "Keep responses under 200 words unless asked for detailed steps.";

            $history = '';
            if ($context) {
                $history .= "Context: {$context}\n\n";
            }
            foreach ($messages as $msg) {
                $role     = $msg['sender'] === 'user' ? 'User' : 'ChefAI';
                $history .= "{$role}: {$msg['text']}\n";
            }
            $history .= "\nGenerate ChefAI's next response only.";

            $reply = $this->gemini->generateText($history, $system);

            return [
                'success'    => true,
                'reply'      => $reply,
                'model_used' => 'gemini-2.5-flash',
            ];
        } catch (\Exception $e) {
            Log::error('Chat failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Ingredient Substitutions
    // ─────────────────────────────────────────────────────────
    public function substitute(string $ingredient, array $recipeContext = []): array
    {
        try {
            $system = "You are ChefAI, a culinary expert. Output ONLY valid JSON with key 'substitutions' containing an array of objects with: name, ratio, reason.";
            $prompt = "Give 3 substitutions for '{$ingredient}' in cooking. Return JSON: {\"ingredient\": \"name\", \"substitutions\": [{\"name\": \"\", \"ratio\": \"\", \"reason\": \"\"}]}";

            $result = $this->gemini->generateJSON($prompt, $system);
            return array_merge(['success' => true], $result);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Meal Plan Generation
    // ─────────────────────────────────────────────────────────
    public function generateMealPlan(array $prefs): array
    {
        try {
            $system = "You are ChefAI meal planner. Output ONLY valid JSON. " .
                      "Return {\"calorie_target\": int, \"diet_type\": string, \"days_count\": int, \"meal_plan\": [array of day objects]}. " .
                      "Each day: {\"day_number\": int, \"date\": \"YYYY-MM-DD\", \"total_calories\": int, \"meals\": [array]}. " .
                      "Each meal: {\"meal_type\": string, \"name\": string, \"calories_per_serving\": int, \"protein\": int, \"carbs\": int, \"fats\": int, \"ingredients\": [array]}.";

            $calories = $prefs['calorie_target'] ?? 2000;
            $diet     = $prefs['diet_type']      ?? 'balanced';
            $days     = $prefs['days']           ?? 7;
            $prompt   = "Generate a {$days}-day meal plan. Calories per day: {$calories}. Diet: {$diet}. Include breakfast, lunch, dinner.";

            $result = $this->gemini->generateJSON($prompt, $system);
            return array_merge(['success' => true], $result);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Mood Recipe
    // ─────────────────────────────────────────────────────────
    public function moodRecipe(string $mood, array $preferences = []): array
    {
        try {
            $system = "You are ChefAI. Return ONLY a JSON array of 3 recipe objects matching the given mood.";
            $prompt = "Give me 3 recipes that match the mood: '{$mood}'. Return a JSON array of recipe objects with name, description, cuisine, cooking_time, calories_per_serving, ingredients, and steps.";
            $recipes = $this->gemini->generateJSON($prompt, $system);
            $list    = isset($recipes['recipes']) ? $recipes['recipes'] : (isset($recipes[0]) ? $recipes : [$recipes]);
            return ['success' => true, 'recipes' => $list];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Leftover Recipe
    // ─────────────────────────────────────────────────────────
    public function leftoverRecipe(array $ingredients): array
    {
        try {
            $system = "You are ChefAI. Return ONLY a JSON array of 3 recipe objects that can be made with the given leftover ingredients.";
            $ings   = implode(', ', $ingredients);
            $prompt = "I have these leftover ingredients: {$ings}. Suggest 3 recipes I can make. Return a JSON array.";
            $recipes = $this->gemini->generateJSON($prompt, $system);
            $list    = isset($recipes['recipes']) ? $recipes['recipes'] : (isset($recipes[0]) ? $recipes : [$recipes]);
            return ['success' => true, 'recipes' => $list];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────
    // Cooking Tips
    // ─────────────────────────────────────────────────────────
    public function cookingTips(string $recipeName, string $cuisine = ''): array
    {
        try {
            $system = "You are ChefAI. Return ONLY JSON: {\"tips\": [array of tip strings]}.";
            $prompt = "Give 5 professional cooking tips for making '{$recipeName}'" . ($cuisine ? " ({$cuisine} cuisine)" : '') . ". Return JSON with key 'tips'.";
            $result = $this->gemini->generateJSON($prompt, $system);
            return array_merge(['success' => true], $result);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
