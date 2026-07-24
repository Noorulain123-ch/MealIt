@extends('layouts.app')

@section('title', 'Compare Recipes — MealIt')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="playfair text-gradient" style="background: linear-gradient(135deg, var(--primary), #FF7A1A); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Compare Recipes</h1>
        <p class="text-muted">Analyze cooking times, nutritional statistics, and ingredients side by side.</p>
    </div>

    <!-- Select Dropdown card -->
    <div class="glass-card p-4 mb-5">
        <form action="{{ route('compare') }}" method="GET" class="row g-3 justify-content-center">
            <div class="col-md-5">
                <label class="form-label fw-bold">Recipe 1:</label>
                <select name="r1" class="form-select bg-light border-0 py-3" aria-label="Compare recipe 1">
                    <option value="">-- Choose Recipe --</option>
                    @foreach($allRecipes as $r)
                        <option value="{{ $r->id }}" {{ request('r1') == $r->id ? 'selected' : '' }}>{{ $r->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-bold">Recipe 2:</label>
                <select name="r2" class="form-select bg-light border-0 py-3" aria-label="Compare recipe 2">
                    <option value="">-- Choose Recipe --</option>
                    @foreach($allRecipes as $r)
                        <option value="{{ $r->id }}" {{ request('r2') == $r->id ? 'selected' : '' }}>{{ $r->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-custom w-100 py-3 fs-6">Compare Now</button>
            </div>
        </form>
    </div>

    @if(count($recipes) > 0)
        <!-- Side by side comparison table -->
        <div class="glass-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle" style="background: var(--bg-card); color: var(--text);">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 20%;" class="p-3">Stat Matrix</th>
                            @foreach($recipes as $recipe)
                                <th class="text-center p-3 fs-5 fw-bold text-primary" style="width: 40%;">{{ $recipe->title }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold p-3">Cover Image</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3">
                                    <img src="{{ $recipe->image }}" class="rounded object-fit-cover shadow-sm" style="width: 100%; max-width: 250px; height: 150px;" alt="{{ $recipe->title }}">
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Cuisine Category</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3 fw-semibold">{{ $recipe->cuisine_type }} Cuisine</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Meal Category</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3 text-uppercase fw-semibold">{{ $recipe->meal_type }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Cooking Time</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3 text-primary fw-bold fs-5">{{ $recipe->cooking_time }} Mins</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Difficulty Level</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3">{!! $recipe->difficulty_badge !!}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Calorie Intake</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3 fw-bold text-danger">{{ $recipe->calories }} kcal</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Macro Composition</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3">
                                    <span class="badge bg-success p-2">P: {{ $recipe->protein }}g</span>
                                    <span class="badge bg-warning text-dark p-2 mx-1">C: {{ $recipe->carbs }}g</span>
                                    <span class="badge bg-primary p-2">F: {{ $recipe->fats }}g</span>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold p-3">Ingredients Count</td>
                            @foreach($recipes as $recipe)
                                <td class="text-center p-3 fw-semibold">{{ $recipe->ingredients->count() }} Ingredients</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-code-compare text-primary mb-3" style="font-size: 65px;"></i>
            <h4 class="fw-bold">Select two recipes above to compare side-by-side</h4>
        </div>
    @endif
</div>
@endsection
