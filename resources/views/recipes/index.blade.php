@extends('layouts.app')

@section('title', 'Explore All Custom Healthy Recipes — MealIt')

@section('styles')
<style>
    .filter-panel {
        position: sticky;
        top: 90px;
        z-index: 100;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar filters -->
        <div class="col-lg-3">
            <div class="glass-card p-4 filter-panel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-sliders text-primary me-2"></i>Filters</h5>
                    <button class="btn btn-sm btn-link text-decoration-none p-0 text-primary" id="clear-filters-btn">Clear All</button>
                </div>

                <form id="filters-form">
                    <!-- Cuisine -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Cuisine</label>
                        <select class="form-select bg-light border-0 py-2" name="cuisine" aria-label="Cuisine select">
                            <option value="">All Cuisines</option>
                            @foreach($cuisines as $c)
                                <option value="{{ $c }}" {{ request('cuisine') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Meal Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Meal Type</label>
                        <select class="form-select bg-light border-0 py-2" name="meal_type" aria-label="Meal type select">
                            <option value="">Any Meal</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>

                    <!-- Difficulty -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Difficulty</label>
                        <select class="form-select bg-light border-0 py-2" name="difficulty" aria-label="Difficulty select">
                            <option value="">Any Level</option>
                            <option value="beginner">Beginner</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="advanced">Advanced</option>
                            <option value="professional">Professional</option>
                        </select>
                    </div>

                    <!-- Spice Level -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Spice Level</label>
                        <select class="form-select bg-light border-0 py-2" name="spice" aria-label="Spice select">
                            <option value="">Any Spice</option>
                            <option value="mild">Mild</option>
                            <option value="medium">Medium</option>
                            <option value="spicy">Spicy</option>
                            <option value="extra_spicy">Extra Spicy</option>
                        </select>
                    </div>

                    <!-- Dietary Flags -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Dietary Restrictions</label>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_halal" value="1" id="filter-halal">
                                <label class="form-check-label fw-semibold" for="filter-halal">Halal-compliant</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_veg" value="1" id="filter-veg">
                                <label class="form-check-label fw-semibold" for="filter-veg">Vegetarian</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_vegan" value="1" id="filter-vegan">
                                <label class="form-check-label fw-semibold" for="filter-vegan">Vegan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_keto" value="1" id="filter-keto">
                                <label class="form-check-label fw-semibold" for="filter-keto">Keto-friendly</label>
                            </div>
                        </div>
                    </div>

                    <!-- Max cooking time -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Max Cooking Time (mins)</label>
                        <input type="range" class="form-range" name="max_time" min="5" max="180" step="5" value="180" id="timeRange">
                        <div class="d-flex justify-content-between small text-muted">
                            <span>5 mins</span>
                            <span id="timeRangeLabel" class="fw-bold text-primary">180 mins</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recipe grid listing -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="playfair fw-bold mb-1">Explore Recipes</h2>
                    <p class="text-muted mb-0"><span id="total-recipes-count">{{ $recipes->total() }}</span> delicious recipes match your filter preferences.</p>
                </div>
                
                <div>
                    <select class="form-select bg-white border-light shadow-sm py-2" id="sort-selector" aria-label="Sort selector" style="border-radius: 30px;">
                        <option value="popular">Most Popular</option>
                        <option value="newest">Newest Additions</option>
                        <option value="rating">Top Rated</option>
                        <option value="fastest">Fastest Cook Time</option>
                    </select>
                </div>
            </div>

            <!-- Recipe Grid list output -->
            <div class="row g-4" id="recipes-grid-target">
                @include('recipes.partials.grid', ['recipes' => $recipes])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const form = document.getElementById('filters-form');
    const sortSelect = document.getElementById('sort-selector');
    const gridTarget = document.getElementById('recipes-grid-target');
    const totalCount = document.getElementById('total-recipes-count');
    const timeRange = document.getElementById('timeRange');
    const timeRangeLabel = document.getElementById('timeRangeLabel');

    timeRange.addEventListener('input', () => {
        timeRangeLabel.textContent = `${timeRange.value} mins`;
        triggerAjaxFilter();
    });

    form.querySelectorAll('select, input[type="checkbox"]').forEach(el => {
        el.addEventListener('change', triggerAjaxFilter);
    });

    sortSelect.addEventListener('change', triggerAjaxFilter);

    document.getElementById('clear-filters-btn').addEventListener('click', () => {
        form.reset();
        timeRangeLabel.textContent = '180 mins';
        triggerAjaxFilter();
    });

    function triggerAjaxFilter() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Add sort parameter
        params.append('sort', sortSelect.value);

        // Fetch grid partial via AJAX
        fetch(`{{ route('recipes.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            gridTarget.innerHTML = data.html;
            totalCount.textContent = data.total;
        })
        .catch(() => {});
    }
</script>
@endsection
