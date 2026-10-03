<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('ai')->group(function () {
    Route::post('/generate', [AIController::class, 'generate']);
    Route::post('/chat', [AIController::class, 'chat']);
    Route::post('/substitute', [AIController::class, 'substitute']);
    Route::post('/meal-plan', [AIController::class, 'mealPlan']);
    Route::post('/mood-recipe', [AIController::class, 'moodRecipe']);
    Route::post('/leftover-recipe', [AIController::class, 'leftoverRecipe']);
    Route::post('/tips', [AIController::class, 'cookingTips']);
    Route::get('/health', [AIController::class, 'health']);
});
