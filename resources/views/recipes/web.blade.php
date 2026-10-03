@extends('layouts.app')

@section('title', 'Web Recipes — Explore Thousands of Real Dishes | MealIt')

@section('styles')
<style>
    .filter-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        margin-bottom: 32px;
    }
    .recipe-web-card {
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--bg-card);
        border: 1px solid var(--border);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .recipe-web-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(232, 93, 4, 0.3);
    }
    .recipe-web-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .recipe-web-card:hover img {
        transform: scale(1.05);
    }
    .recipe-web-card .card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .recipe-web-card .card-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text);
        line-height: 1.3;
    }
    .badge-area {
        background: rgba(232, 93, 4, 0.12);
        color: var(--primary);
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(232, 93, 4, 0.2);
    }
    .badge-cat {
        background: rgba(45, 157, 94, 0.12);
        color: var(--success);
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(45, 157, 94, 0.2);
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 30px;
        border: 2px solid var(--border);
        background: var(--bg-page);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-fast);
        text-decoration: none;
        white-space: nowrap;
    }
    .filter-chip:hover, .filter-chip.active {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(232, 93, 4, 0.06);
    }
    .hero-web {
        background: linear-gradient(135deg, #1B0000 0%, #3D1000 40%, #1B4332 100%);
        padding: 60px 0 50px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .hero-web::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="80" r="3" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="1.5" fill="rgba(255,255,255,0.05)"/></svg>');
    }
    .search-web-input {
        border-radius: 30px 0 0 30px;
        padding: 14px 24px;
        border: none;
        font-size: 1rem;
        background: white;
        color: #333;
    }
    .search-web-btn {
        border-radius: 0 30px 30px 0;
        padding: 14px 28px;
        font-size: 1rem;
    }
    .results-count {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
    .no-results {
        text-align: center;
        padding: 80px 20px;
        color: var(--text-muted);
    }
    .random-btn {
        background: linear-gradient(135deg, #FF7A1A, #E85D04);
        border: none;
        border-radius: 30px;
        color: white;
        padding: 10px 24px;
        font-weight: 600;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .random-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(232, 93, 4, 0.35);
        color: white;
    }
    .cat-pills-wrapper {
        overflow-x: auto;
        padding-bottom: 8px;
        scrollbar-width: none;
    }
    .cat-pills-wrapper::-webkit-scrollbar { display: none; }
    .cat-pills {
        display: flex;
        gap: 10px;
        min-width: max-content;
    }
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="hero-web">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <p class="small text-warning fw-semibold mb-2 text-uppercase letter-spacing-2">
                    <i class="fa-solid fa-globe me-2"></i>Live from the Web
                </p>
                <h1 class="display-5 fw-bold text-white mb-3">
                    Explore Thousands of <span style="color: #FF7A1A;">Real Recipes</span>
                </h1>
                <p class="lead text-white-50 mb-4">
                    Powered by TheMealDB — browse, search & filter recipes from across the globe. All free, all real.
                </p>

                <form action="{{ route('web-recipes.index') }}" method="GET" class="d-flex" role="search">
                    @if($category)<input type="hidden" name="category" value="{{ $category }}">@endif
                    @if($area)<input type="hidden" name="area" value="{{ $area }}">@endif
                    <input
                        type="text"
                        name="search"
                        class="form-control search-web-input shadow-sm"
                        placeholder="Search any dish (e.g. Biryani, Pasta, Sushi)..."
                        value="{{ $search }}"
                        autocomplete="off"
                    >
                    <button type="submit" class="btn btn-custom search-web-btn">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                    </button>
                </form>
            </div>
            <div class="col-lg-5 text-end d-none d-lg-block">
                <div class="d-flex flex-column align-items-end gap-3">
                    <a href="{{ route('web-recipes.random') }}" class="random-btn">
                        <i class="fa-solid fa-shuffle"></i> Surprise Me!
                    </a>
                    <div class="text-white-50 small">
                        <i class="fa-solid fa-database me-1"></i>
                        300,000+ recipes from TheMealDB
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">

    {{-- Filters Row --}}
    <div class="filter-bar">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="fw-bold text-nowrap"><i class="fa-solid fa-sliders me-2 text-primary"></i>Filter:</div>

            {{-- Cuisine (Area) --}}
            <div class="flex-grow-1">
                <div class="cat-pills-wrapper">
                    <div class="cat-pills">
                        <a href="{{ route('web-recipes.index', array_merge(request()->except(['area', 'page']), [])) }}"
                           class="filter-chip {{ !$area ? 'active' : '' }}">
                            🌍 All Cuisines
                        </a>
                        @foreach($areas as $a)
                            <a href="{{ route('web-recipes.index', array_merge(request()->except(['area', 'page', 'search']), ['area' => $a['strArea']])) }}"
                               class="filter-chip {{ $area === $a['strArea'] ? 'active' : '' }}">
                                {{ $a['strArea'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Random --}}
            <a href="{{ route('web-recipes.random') }}" class="btn btn-outline-custom btn-sm text-nowrap">
                <i class="fa-solid fa-shuffle me-1"></i>Random
            </a>
        </div>

        {{-- Category pills --}}
        @if(count($categories))
        <hr class="my-3" style="opacity: 0.1;">
        <div class="cat-pills-wrapper">
            <div class="cat-pills">
                <a href="{{ route('web-recipes.index') }}"
                   class="filter-chip {{ !$category ? 'active' : '' }}">
                    📋 All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('web-recipes.index', array_merge(request()->except(['category', 'page', 'search', 'area']), ['category' => $cat['strCategory']])) }}"
                       class="filter-chip {{ $category === $cat['strCategory'] ? 'active' : '' }}">
                        {{ $cat['strCategory'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Results Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if($search)
                <h2 class="fw-bold h4 mb-1">Search results for "<span class="text-primary">{{ $search }}</span>"</h2>
            @elseif($category)
                <h2 class="fw-bold h4 mb-1">Category: <span class="text-primary">{{ $category }}</span></h2>
            @elseif($area)
                <h2 class="fw-bold h4 mb-1"><span class="text-primary">{{ $area }}</span> Cuisine</h2>
            @else
                <h2 class="fw-bold h4 mb-1">Featured <span class="text-primary">Web Recipes</span></h2>
            @endif
            <p class="results-count mb-0">{{ count($recipes) }} recipe(s) found</p>
        </div>
        @if($search || $category || $area)
            <a href="{{ route('web-recipes.index') }}" class="btn btn-outline-custom btn-sm">
                <i class="fa-solid fa-xmark me-1"></i>Clear Filters
            </a>
        @endif
    </div>

    {{-- Recipe Grid --}}
    @if(count($recipes) > 0)
        <div class="row g-4">
            @foreach($recipes as $meal)
                <div class="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="recipe-web-card">
                        <div style="overflow: hidden;">
                            <img
                                src="{{ $meal['strMealThumb'] ?? 'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=400&q=80' }}"
                                alt="{{ $meal['strMeal'] }}"
                                loading="lazy"
                            >
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap mb-2">
                                @if(!empty($meal['strArea']))
                                    <span class="badge-area">{{ $meal['strArea'] }}</span>
                                @endif
                                @if(!empty($meal['strCategory']))
                                    <span class="badge-cat">{{ $meal['strCategory'] }}</span>
                                @endif
                            </div>
                            <div class="card-title">{{ $meal['strMeal'] }}</div>
                            <div class="mt-auto pt-2 d-flex gap-2">
                                <a href="{{ route('web-recipes.show', $meal['idMeal']) }}"
                                   class="btn btn-custom btn-sm flex-grow-1">
                                    <i class="fa-solid fa-eye me-1"></i>View Recipe
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-results glass-card p-5">
            <i class="fa-solid fa-bowl-food fa-3x text-muted mb-4 d-block"></i>
            <h4 class="fw-bold mb-2">No recipes found</h4>
            <p class="text-muted mb-4">Try a different search term or browse a category below.</p>
            <a href="{{ route('web-recipes.index') }}" class="btn btn-custom px-5">Browse All Recipes</a>
        </div>
    @endif
</div>
@endsection
