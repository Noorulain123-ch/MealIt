<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WebRecipesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/generate', [RecipeController::class, 'generatePage'])->name('generate');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/compare', [RecipeController::class, 'compare'])->name('compare');
Route::get('/ingredients/suggest', [SearchController::class, 'suggest']);
Route::get('/recipes/autocomplete', [SearchController::class, 'recipeAutocomplete']);

// Web Recipes (TheMealDB live integration)
Route::get('/web-recipes', [WebRecipesController::class, 'index'])->name('web-recipes.index');
Route::get('/web-recipes/random', [WebRecipesController::class, 'random'])->name('web-recipes.random');
Route::get('/web-recipes/search', [WebRecipesController::class, 'search'])->name('web-recipes.search');
Route::get('/web-recipes/{mealId}', [WebRecipesController::class, 'show'])->name('web-recipes.show');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Bookmarks & Favorites
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');
    Route::post('/bookmarks/{recipeId}', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{recipeId}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    
    // Reviews
    Route::post('/recipes/{recipeId}/review', [ReviewController::class, 'store'])->name('recipes.review.store');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Meal Planner
    Route::get('/meal-planner', [MealPlanController::class, 'index'])->name('meal-planner');
    Route::post('/meal-planner/generate', [MealPlanController::class, 'generate'])->name('meal-planner.generate');
    Route::post('/meal-planner/{plan}/shopping-list', [MealPlanController::class, 'generateShoppingList'])->name('meal-planner.shopping-list');
    
    // Shopping List
    Route::get('/shopping-list', [ShoppingListController::class, 'index'])->name('shopping-list');
    Route::post('/shopping-list', [ShoppingListController::class, 'store'])->name('shopping-list.store');
    Route::post('/shopping-list/{list}/item', [ShoppingListController::class, 'addItem'])->name('shopping-list.add-item');
    Route::post('/shopping-list/item/{itemId}/toggle', [ShoppingListController::class, 'toggle'])->name('shopping-list.toggle-item');
    Route::delete('/shopping-list/item/{itemId}', [ShoppingListController::class, 'destroyItem'])->name('shopping-list.delete-item');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/dark-mode', [ProfileController::class, 'updateDarkMode'])->name('profile.dark-mode');
});
