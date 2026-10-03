<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Favorite;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $favorites = auth()->user()->favoriteRecipes()
            ->with('category')
            ->paginate(12);
        $collections = auth()->user()->collections()->withCount('recipes')->get();
        return view('dashboard.bookmarks', compact('favorites','collections'));
    }

    public function store(Request $request, $recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        $exists = Favorite::where('user_id', auth()->id())->where('recipe_id', $recipeId)->exists();

        if ($exists) {
            Favorite::where('user_id', auth()->id())->where('recipe_id', $recipeId)->delete();
            return response()->json(['bookmarked' => false, 'message' => 'Removed from favorites']);
        }

        Favorite::create(['user_id' => auth()->id(), 'recipe_id' => $recipeId]);
        return response()->json(['bookmarked' => true, 'message' => 'Added to favorites']);
    }

    public function destroy($recipeId)
    {
        Favorite::where('user_id', auth()->id())->where('recipe_id', $recipeId)->delete();
        return response()->json(['bookmarked' => false]);
    }
}
