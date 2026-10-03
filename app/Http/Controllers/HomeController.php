<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Services\RecipeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index()
    {
        $featuredRecipes = Recipe::with('category')
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        $trendingRecipes = Recipe::with('category')
            ->popular()
            ->take(12)
            ->get();

        $topRated = Recipe::with('category')
            ->topRated()
            ->where('review_count', '>', 0)
            ->take(6)
            ->get();

        $seasonalRecipes = Recipe::seasonal()->latest()->take(4)->get();

        $cuisines = Category::where('type', 'cuisine')->where('is_active', true)->get();

        $categories = Category::where('is_active', true)->take(9)->get();

        $stats = [
            'recipes'  => Recipe::count(),
            'cuisines' => Category::where('type','cuisine')->count(),
            'users'    => \App\Models\User::count(),
        ];

        return view('home', compact(
            'featuredRecipes','trendingRecipes','topRated',
            'seasonalRecipes','cuisines','categories','stats'
        ));
    }
}
