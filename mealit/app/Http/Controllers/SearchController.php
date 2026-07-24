<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\SearchHistory;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        // 1. Log query
        if ($q && auth()->check()) {
            SearchHistory::create([
                'user_id' => auth()->id(),
                'query'   => $q,
                'type'    => 'keyword',
            ]);
        }

        // 2. Perform database search first
        $localQuery = Recipe::with('category');
        if ($q) {
            $localQuery->where(function($qb) use ($q) {
                $qb->where('title', 'LIKE', "%{$q}%")
                   ->orWhere('description', 'LIKE', "%{$q}%")
                   ->orWhere('cuisine_type', 'LIKE', "%{$q}%");
            });
        }
        
        $recipesCount = $localQuery->count();

        // 3. Fallback: If less than 4 recipes match locally, search external APIs (TheMealDB, Spoonacular, Edamam)
        if ($q && $recipesCount < 4) {
            $externalRecipes = $this->recipeService->searchRecipes($q);

            foreach ($externalRecipes as $ext) {
                $slug = Str::slug($ext['title']);
                
                // Avoid duplicating if slug or title already exists in our database
                if (!Recipe::where('slug', $slug)->exists()) {
                    Recipe::create([
                        'title'        => $ext['title'],
                        'slug'         => $slug,
                        'description'  => !empty($ext['instructions']) 
                            ? Str::limit(strip_tags($ext['instructions']), 160) 
                            : "Delicious {$ext['cuisine_type']} dish sourced dynamically from ChefAI recipe partners.",
                        'cuisine_type' => $ext['cuisine_type'] ?? 'International',
                        'meal_type'    => 'lunch',
                        'image'        => $ext['image_url'],
                        'cooking_time' => 30,
                        'servings'     => 2,
                        'difficulty'   => 'medium',
                        'spice_level'  => 'medium',
                        'calories'     => $ext['calories'] ?? rand(300, 600),
                        'protein'      => $ext['protein'] ?? rand(15, 35),
                        'carbs'        => $ext['carbs'] ?? rand(30, 60),
                        'fats'         => $ext['fats'] ?? rand(8, 20),
                        'instructions' => json_encode([$ext['instructions'] ?: 'Follow standard cooking directions.']),
                        'view_count'   => 1,
                        'is_active'    => true
                    ]);
                }
            }

            // Re-run the local query to fetch newly dynamically cached external recipes
            $localQuery = Recipe::with('category');
            if ($q) {
                $localQuery->where(function($qb) use ($q) {
                    $qb->where('title', 'LIKE', "%{$q}%")
                       ->orWhere('description', 'LIKE', "%{$q}%")
                       ->orWhere('cuisine_type', 'LIKE', "%{$q}%");
                });
            }
        }

        $recipes = $localQuery->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html'  => view('recipes.partials.grid', compact('recipes'))->render(),
                'total' => $recipes->total(),
            ]);
        }

        // Use index/explore view or search view depending on routes
        return view('recipes.index', [
            'recipes'    => $recipes,
            'categories' => \App\Models\Category::where('is_active', true)->get(),
            'cuisines'   => \App\Models\Category::where('type', 'cuisine')->get()->pluck('name')->toArray()
        ]);
    }

    public function suggest(Request $request)
    {
        $q = $request->get('q', '');
        $ingredients = Ingredient::where('name', 'LIKE', "%{$q}%")
            ->orderBy('name')
            ->take(10)
            ->get(['id','name','category','image_url']);

        return response()->json($ingredients);
    }

    public function recipeAutocomplete(Request $request)
    {
        $q = $request->get('q', '');
        $recipes = Recipe::where('title', 'LIKE', "%{$q}%")
            ->take(8)
            ->get(['id','title','slug','image_url','cuisine_type']);

        return response()->json($recipes);
    }
}
