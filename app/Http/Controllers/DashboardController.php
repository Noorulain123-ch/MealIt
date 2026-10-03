<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\MealPlan;
use App\Models\ShoppingList;
use App\Models\AiGeneration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'saved'    => $user->favorites()->count(),
            'reviews'  => $user->reviews()->count(),
            'plans'    => $user->mealPlans()->count(),
            'lists'    => $user->shoppingLists()->count(),
        ];

        $recentFavorites = $user->favoriteRecipes()
            ->with('category')
            ->latest('favorites.created_at')
            ->take(4)->get();

        $currentPlan = $user->mealPlans()
            ->where('end_date', '>=', now()->toDateString())
            ->with('items.recipe')
            ->latest()
            ->first();

        $recentAiGenerations = $user->aiGenerations()
            ->latest()->take(5)->get();

        $notifications = $user->notifications()
            ->where('is_read', false)
            ->latest()->take(5)->get();

        $trendingRecipes = Recipe::popular()->with('category')->take(6)->get();

        return view('dashboard.index', compact(
            'user','stats','recentFavorites','currentPlan',
            'recentAiGenerations','notifications','trendingRecipes'
        ));
    }
}
