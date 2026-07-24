<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Recipe;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function store(Request $request, $recipeId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'photo'   => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $recipe = Recipe::findOrFail($recipeId);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('review-photos', 'public');
        }

        $review = Review::updateOrCreate(
            ['user_id' => auth()->id(), 'recipe_id' => $recipeId],
            [
                'rating'    => $request->rating,
                'comment'   => $request->comment,
                'photo_url' => $photoPath,
            ]
        );

        // Update recipe avg rating
        $avg = $recipe->reviews()->avg('rating');
        $count = $recipe->reviews()->count();
        $recipe->update(['avg_rating' => round($avg, 2), 'review_count' => $count]);

        return response()->json([
            'success' => true,
            'review'  => $review->load('user'),
            'message' => 'Review submitted!',
        ]);
    }

    public function destroy($id)
    {
        $review = Review::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $recipe = $review->recipe;
        $review->delete();

        $avg   = $recipe->reviews()->avg('rating') ?? 0;
        $count = $recipe->reviews()->count();
        $recipe->update(['avg_rating' => round($avg, 2), 'review_count' => $count]);

        return response()->json(['success' => true]);
    }
}
