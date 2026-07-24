<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecipeService
{
    private string $mealDbUrl;
    private ?string $spoonacularKey;
    private ?string $edamamAppId;
    private ?string $edamamAppKey;
    private ?string $unsplashKey;

    public function __construct()
    {
        $this->mealDbUrl      = config('apis.themealdb_url', 'https://www.themealdb.com/api/json/v1/1');
        $this->spoonacularKey = config('apis.spoonacular_key');
        $this->edamamAppId    = config('apis.edamam_app_id');
        $this->edamamAppKey   = config('apis.edamam_app_key');
        $this->unsplashKey    = config('apis.unsplash_key');
    }

    /**
     * Unified search across active API integrations (TheMealDB, Spoonacular, Edamam)
     */
    public function searchRecipes(string $query): array
    {
        $cacheKey = 'unified_search_' . md5($query);

        return Cache::remember($cacheKey, 3600, function () use ($query) {
            $results = [];

            // 1. Search TheMealDB (always active, free)
            $mealDbResults = $this->searchMealDB($query);
            $results = array_merge($results, $mealDbResults);

            // 2. Search Spoonacular if key is provided
            if ($this->spoonacularKey) {
                $spoonResults = $this->searchSpoonacular($query);
                $results = array_merge($results, $spoonResults);
            }

            // 3. Search Edamam if keys are provided
            if ($this->edamamAppId && $this->edamamAppKey) {
                $edamamResults = $this->searchEdamam($query);
                $results = array_merge($results, $edamamResults);
            }

            return $results;
        });
    }

    /**
     * Search TheMealDB API
     */
    public function searchMealDB(string $query): array
    {
        try {
            $response = Http::timeout(8)->get("{$this->mealDbUrl}/search.php", ['s' => $query]);
            if ($response->successful() && $response->json('meals')) {
                return $this->transformMealDBRecipes($response->json('meals'));
            }
        } catch (\Exception $e) {
            Log::warning('TheMealDB API error: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Search Spoonacular API (Free Tier ready)
     */
    public function searchSpoonacular(string $query): array
    {
        try {
            $response = Http::timeout(8)->get('https://api.spoonacular.com/recipes/complexSearch', [
                'query'    => $query,
                'apiKey'   => $this->spoonacularKey,
                'number'   => 8,
                'addRecipeNutrition' => 'true'
            ]);

            if ($response->successful() && $response->json('results')) {
                return $this->transformSpoonacularRecipes($response->json('results'));
            }
        } catch (\Exception $e) {
            Log::warning('Spoonacular API error: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Search Edamam Recipe API (Free Tier ready)
     */
    public function searchEdamam(string $query): array
    {
        try {
            $response = Http::timeout(8)->get('https://api.edamam.com/search', [
                'q'       => $query,
                'app_id'  => $this->edamamAppId,
                'app_key' => $this->edamamAppKey,
                'to'      => 8
            ]);

            if ($response->successful() && $response->json('hits')) {
                return $this->transformEdamamRecipes($response->json('hits'));
            }
        } catch (\Exception $e) {
            Log::warning('Edamam API error: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Search Unsplash for matching high-res dish covers
     */
    public function getUnsplashImage(string $query): ?string
    {
        if (!$this->unsplashKey) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->unsplashKey
            ])->timeout(5)->get('https://api.unsplash.com/search/photos', [
                'query'       => $query . ' food meal dish cooked',
                'orientation' => 'landscape',
                'per_page'    => 1,
            ]);
            return $response->json('results.0.urls.regular');
        } catch (\Exception $e) {
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Transformer functions (Normalizes all API outputs into identical structures)
    |--------------------------------------------------------------------------
    */

    private function transformMealDBRecipes(array $meals): array
    {
        return array_map(fn($m) => [
            'title'        => $m['strMeal'] ?? 'Unknown Meal',
            'image_url'    => $m['strMealThumb'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80',
            'cuisine_type' => $m['strArea'] ?? 'International',
            'instructions' => $m['strInstructions'] ?? '',
            'video_url'    => $m['strYoutube'] ?? null,
            'external_id'  => $m['idMeal'] ?? null,
            'source_api'   => 'themealdb',
            'calories'     => null,
            'protein'      => null,
            'carbs'        => null,
            'fats'         => null,
        ], $meals);
    }

    private function transformSpoonacularRecipes(array $recipes): array
    {
        return array_map(function($r) {
            $nutrition = $r['nutrition']['nutrients'] ?? [];
            $calories  = collect($nutrition)->firstWhere('name', 'Calories')['amount'] ?? null;
            $protein   = collect($nutrition)->firstWhere('name', 'Protein')['amount'] ?? null;
            $carbs     = collect($nutrition)->firstWhere('name', 'Carbohydrates')['amount'] ?? null;
            $fats      = collect($nutrition)->firstWhere('name', 'Fat')['amount'] ?? null;

            return [
                'title'        => $r['title'] ?? 'Spoonacular Dish',
                'image_url'    => $r['image'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80',
                'cuisine_type' => !empty($r['cuisines']) ? $r['cuisines'][0] : 'International',
                'instructions' => $r['summary'] ?? '',
                'video_url'    => null,
                'external_id'  => $r['id'] ?? null,
                'source_api'   => 'spoonacular',
                'calories'     => $calories ? round($calories) : null,
                'protein'      => $protein ? round($protein) : null,
                'carbs'        => $carbs ? round($carbs) : null,
                'fats'         => $fats ? round($fats) : null,
            ];
        }, $recipes);
    }

    private function transformEdamamRecipes(array $hits): array
    {
        return array_map(function($h) {
            $r = $h['recipe'] ?? [];
            
            $calories = isset($r['calories']) && isset($r['yield']) && $r['yield'] > 0 
                ? round($r['calories'] / $r['yield']) 
                : null;

            $protein = isset($r['totalNutrients']['PROCNT']['quantity']) && isset($r['yield']) && $r['yield'] > 0
                ? round($r['totalNutrients']['PROCNT']['quantity'] / $r['yield'])
                : null;

            $carbs = isset($r['totalNutrients']['CHOCDF']['quantity']) && isset($r['yield']) && $r['yield'] > 0
                ? round($r['totalNutrients']['CHOCDF']['quantity'] / $r['yield'])
                : null;

            $fats = isset($r['totalNutrients']['FAT']['quantity']) && isset($r['yield']) && $r['yield'] > 0
                ? round($r['totalNutrients']['FAT']['quantity'] / $r['yield'])
                : null;

            return [
                'title'        => $r['label'] ?? 'Edamam Dish',
                'image_url'    => $r['image'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80',
                'cuisine_type' => !empty($r['cuisineType']) ? ucfirst($r['cuisineType'][0]) : 'International',
                'instructions' => implode(' ', $r['ingredientLines'] ?? []),
                'video_url'    => null,
                'external_id'  => isset($r['uri']) ? basename($r['uri']) : null,
                'source_api'   => 'edamam',
                'calories'     => $calories,
                'protein'      => $protein,
                'carbs'        => $carbs,
                'fats'         => $fats,
            ];
        }, $hits);
    }
}
