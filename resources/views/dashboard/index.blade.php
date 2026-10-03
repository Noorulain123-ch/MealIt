@extends('layouts.app')

@section('title', 'Dashboard — MealIt')

@section('styles')
<style>
    .dashboard-stat-box {
        padding: 30px;
        text-align: center;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--bg-card);
        transition: var(--transition);
    }
    .dashboard-stat-box:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    .stat-val {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary);
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="playfair fw-bold mb-1">Hello, {{ $user->name }}!</h1>
            <p class="text-muted">Welcome back to your personalized culinary control panel.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meal-planner') }}" class="btn btn-custom"><i class="fa-solid fa-calendar me-2"></i>Weekly Planner</a>
            <a href="{{ route('generate') }}" class="btn btn-outline-custom"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>AI Recipe Finder</a>
        </div>
    </div>

    <!-- Quick Stats row -->
    <div class="row g-4 mb-5" data-aos="fade-up">
        <div class="col-md-3 col-6">
            <div class="dashboard-stat-box">
                <div class="stat-val">{{ $stats['saved'] }}</div>
                <div class="text-muted fw-semibold">Saved Recipes</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dashboard-stat-box">
                <div class="stat-val">{{ $stats['plans'] }}</div>
                <div class="text-muted fw-semibold">Meal Plans</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dashboard-stat-box">
                <div class="stat-val">{{ $stats['lists'] }}</div>
                <div class="text-muted fw-semibold">Shopping Lists</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dashboard-stat-box">
                <div class="stat-val">{{ $stats['reviews'] }}</div>
                <div class="text-muted fw-semibold">User Reviews</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Saved recipes overview -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
            <div class="glass-card p-4 h-100">
                <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-bookmark me-2"></i>Recently Saved Recipes</h4>
                
                @if(count($recentFavorites) > 0)
                    <div class="row g-3">
                        @foreach($recentFavorites as $recipe)
                            <div class="col-md-6">
                                <div class="card bg-light border-0 overflow-hidden h-100 d-flex flex-row" style="border-radius: 12px;">
                                    <img src="{{ $recipe->image }}" style="width: 100px; height: 100%; object-fit: cover;" alt="Image">
                                    <div class="p-3">
                                        <h6 class="fw-bold mb-1"><a href="{{ route('recipes.show', $recipe->slug) }}" class="text-decoration-none text-dark">{{ $recipe->title }}</a></h6>
                                        <small class="text-muted d-block">{{ $recipe->cuisine_type }} Cuisine</small>
                                        <small class="text-primary fw-bold">{{ $recipe->calories }} kcal</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa-solid fa-folder-open text-primary mb-3" style="font-size: 50px;"></i>
                        <h6 class="fw-bold">No saved recipes yet</h6>
                        <p class="text-muted small">Explore and click the favorite star button on any recipe cover.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Weekly menu preview -->
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="glass-card p-4 h-100">
                <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-utensils me-2"></i>Active Meal Plan</h4>
                
                @if($currentPlan)
                    <div class="alert alert-success border-0 mb-4 p-3" style="border-radius: 12px;">
                        <div class="fw-bold mb-1">{{ $currentPlan->name }}</div>
                        <small class="text-success-emphasis">Target: {{ $currentPlan->calorie_target }} kcal | Diet: {{ $currentPlan->diet_type }}</small>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        @foreach($currentPlan->items->take(4) as $item)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-semibold">{{ $item->meal_type }}</small>
                                    <span class="fw-bold">{{ $item->ai_recipe['name'] ?? ($item->recipe->title ?? 'Custom Recipe') }}</span>
                                </div>
                                <span class="badge bg-light text-primary border">{{ $item->ai_recipe['calories_per_serving'] ?? ($item->recipe->calories ?? '0') }} kcal</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa-solid fa-calendar-xmark text-primary mb-3" style="font-size: 50px;"></i>
                        <h6 class="fw-bold">No active weekly menu</h6>
                        <p class="text-muted small mb-4">Design a calorie-optimized weekly plan immediately using Gemini AI.</p>
                        <a href="{{ route('meal-planner') }}" class="btn btn-custom btn-sm py-2 px-3">Create Meal Plan</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
