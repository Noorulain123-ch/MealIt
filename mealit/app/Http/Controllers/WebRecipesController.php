<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WebRecipesController extends Controller
{
    private string $mealDbUrl = 'https://www.themealdb.com/api/json/v1/1';

    // ─── Main page ───────────────────────────────────────────
    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $category = $request->get('category', '');
        $area     = $request->get('area', '');

        $recipes    = [];
        $categories = $this->getCategories();
        $areas      = $this->getAreas();

        if ($search) {
            $recipes = $this->searchByName($search);
        } elseif ($category) {
            $recipes = $this->filterByCategory($category);
        } elseif ($area) {
            $recipes = $this->filterByArea($area);
        } else {
            // Load popular featured recipes by default
            $recipes = $this->getFeaturedRecipes();
        }

        return view('recipes.web', compact('recipes', 'categories', 'areas', 'search', 'category', 'area'));
    }

    // ─── Detail page ─────────────────────────────────────────
    public function show(string $mealId)
    {
        $recipe = $this->getMealById($mealId);

        if (!$recipe) {
            abort(404, 'Recipe not found on TheMealDB.');
        }

        return view('recipes.web-show', compact('recipe'));
    }

    // ─── AJAX search endpoint ─────────────────────────────────
    public function search(Request $request)
    {
        $q       = $request->get('q', '');
        $results = $q ? $this->searchByName($q) : [];
        return response()->json($results);
    }

    // ─── Random recipe ────────────────────────────────────────
    public function random()
    {
        $cacheKey = 'mealdb_random_' . now()->format('H');
        $recipe   = Cache::remember($cacheKey, 3600, function () {
            $r = Http::timeout(10)->get("{$this->mealDbUrl}/random.php");
            return $r->successful() ? ($r->json('meals.0') ?? null) : null;
        });

        if (!$recipe) {
            return redirect()->route('web-recipes.index')->with('error', 'Could not load a random recipe. Try again!');
        }

        return view('recipes.web-show', compact('recipe'));
    }

    // ─── Private helpers ──────────────────────────────────────
    private function getFeaturedRecipes(): array
    {
        return Cache::remember('mealdb_featured', 3600, function () {
            $queries  = ['chicken', 'pasta', 'beef', 'vegetarian', 'fish', 'lamb', 'rice', 'soup'];
            $all      = [];
            foreach ($queries as $q) {
                $r = Http::timeout(8)->get("{$this->mealDbUrl}/search.php?s={$q}");
                if ($r->successful() && $r->json('meals')) {
                    $all = array_merge($all, array_slice($r->json('meals'), 0, 4));
                }
            }
            return $all;
        });
    }

    private function searchByName(string $name): array
    {
        try {
            $r = Http::timeout(10)->get("{$this->mealDbUrl}/search.php?s={$name}");
            return $r->successful() ? ($r->json('meals') ?? []) : [];
        } catch (\Exception $e) {
            Log::error('MealDB search failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function filterByCategory(string $cat): array
    {
        try {
            $r = Http::timeout(10)->get("{$this->mealDbUrl}/filter.php?c={$cat}");
            if (!$r->successful() || !$r->json('meals')) return [];
            $ids     = array_slice($r->json('meals'), 0, 24);
            $results = [];
            foreach ($ids as $meal) {
                $detail = $this->getMealById($meal['idMeal']);
                if ($detail) $results[] = $detail;
                if (count($results) >= 20) break;
            }
            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function filterByArea(string $area): array
    {
        try {
            $r = Http::timeout(10)->get("{$this->mealDbUrl}/filter.php?a={$area}");
            if (!$r->successful() || !$r->json('meals')) return [];
            $ids     = array_slice($r->json('meals'), 0, 20);
            $results = [];
            foreach ($ids as $meal) {
                $detail = $this->getMealById($meal['idMeal']);
                if ($detail) $results[] = $detail;
                if (count($results) >= 16) break;
            }
            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getMealById(string $id): ?array
    {
        return Cache::remember("mealdb_meal_{$id}", 86400, function () use ($id) {
            try {
                $r = Http::timeout(10)->get("{$this->mealDbUrl}/lookup.php?i={$id}");
                return $r->successful() ? ($r->json('meals.0') ?? null) : null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    private function getCategories(): array
    {
        return Cache::remember('mealdb_categories', 86400, function () {
            try {
                $r = Http::timeout(8)->get("{$this->mealDbUrl}/categories.php");
                return $r->successful() ? ($r->json('categories') ?? []) : [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    private function getAreas(): array
    {
        return Cache::remember('mealdb_areas', 86400, function () {
            try {
                $r = Http::timeout(8)->get("{$this->mealDbUrl}/list.php?a=list");
                return $r->successful() ? ($r->json('meals') ?? []) : [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }
}
