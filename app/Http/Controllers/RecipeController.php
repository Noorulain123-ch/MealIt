<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['category', 'reviews']);

        // Filters
        if ($request->cuisine)    $query->where('cuisine_type', $request->cuisine);
        if ($request->meal_type)  $query->where('meal_type', $request->meal_type);
        if ($request->difficulty) $query->where('difficulty', $request->difficulty);
        if ($request->spice)      $query->where('spice_level', $request->spice);
        if ($request->is_halal)   $query->where('is_halal', true);
        if ($request->is_vegan)   $query->where('is_vegan', true);
        if ($request->is_vegetarian) $query->where('is_vegetarian', true);
        if ($request->is_gluten_free) $query->where('is_gluten_free', true);
        if ($request->is_keto)    $query->where('is_keto', true);
        if ($request->max_time)   $query->where('cooking_time', '<=', $request->max_time);
        if ($request->min_cal)    $query->where('calories', '>=', $request->min_cal);
        if ($request->max_cal)    $query->where('calories', '<=', $request->max_cal);

        // Sort
        match($request->sort ?? 'popular') {
            'newest'   => $query->latest(),
            'rating'   => $query->orderBy('avg_rating', 'desc'),
            'fastest'  => $query->orderBy('cooking_time', 'asc'),
            'calories' => $query->orderBy('calories', 'asc'),
            default    => $query->orderBy('view_count', 'desc'),
        };

        $recipes   = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $cuisines   = Category::where('type', 'cuisine')->get()->pluck('name')->toArray();

        if ($request->ajax()) {
            return response()->json([
                'html'  => view('recipes.partials.grid', compact('recipes'))->render(),
                'total' => $recipes->total(),
                'next'  => $recipes->nextPageUrl(),
            ]);
        }

        return view('recipes.index', compact('recipes', 'categories', 'cuisines'));
    }

    public function show(string $slug)
    {
        $recipe = Recipe::with(['category', 'reviews.user', 'ingredients'])
            ->where('slug', $slug)
            ->firstOrFail();

        $recipe->increment('view_count');

        $isFavorited = auth()->check()
            ? $recipe->favorites()->where('user_id', auth()->id())->exists()
            : false;

        $userReview = auth()->check()
            ? $recipe->reviews()->where('user_id', auth()->id())->first()
            : null;

        $related = Recipe::where('cuisine_type', $recipe->cuisine_type)
            ->where('id', '!=', $recipe->id)
            ->take(4)->get();

        return view('recipes.show', compact('recipe', 'isFavorited', 'userReview', 'related'));
    }

    public function generatePage()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        $categories  = Category::where('type', 'cuisine')->get();
        return view('recipes.generate', compact('ingredients', 'categories'));
    }

    public function compare(Request $request)
    {
        $ids = array_filter([$request->r1, $request->r2]);
        $recipes = count($ids) ? Recipe::with('ingredients')->whereIn('id', $ids)->get() : collect();
        $allRecipes = Recipe::orderBy('title')->get(['id','title']);
        return view('recipes.compare', compact('recipes', 'allRecipes'));
    }
}
