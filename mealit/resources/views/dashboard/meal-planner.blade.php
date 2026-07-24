@extends('layouts.app')

@section('title', 'AI Meal Planner — MealIt')

@section('styles')
<style>
    .calorie-slider-val {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .meal-plan-day-card {
        border-left: 4px solid var(--primary);
    }
    
    .meal-item-row {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        transition: var(--transition-fast);
    }
    
    .meal-item-row:hover {
        border-color: var(--primary);
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h1 class="playfair fw-bold mb-1"><i class="fa-solid fa-calendar text-primary me-2"></i>AI Weekly Meal Planner</h1>
            <p class="text-muted">Generate a personalized nutritional meal plan for the entire week using Gemini AI.</p>
        </div>
    </div>

    <!-- AI Generation Form Card -->
    <div class="glass-card p-5 mb-5">
        <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Configure Meal Plan Requirements</h4>
        
        <form id="generate-meal-plan-form">
            <div class="row g-4 align-items-center">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Daily Calorie Target:</label>
                    <input type="range" class="form-range" id="calTarget" min="1000" max="4000" step="50" value="2000">
                    <div class="d-flex justify-content-between mt-2">
                        <span class="small text-muted">1000 kcal</span>
                        <span class="calorie-slider-val" id="calTargetVal">2000 kcal</span>
                        <span class="small text-muted">4000 kcal</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Diet Style Preference:</label>
                    <select class="form-select bg-light border-0 py-3" id="dietTypeSelect" aria-label="Diet style select">
                        <option value="balanced">Balanced Diet</option>
                        <option value="high-protein">High Protein (Fitness/Muscle)</option>
                        <option value="keto">Keto-friendly (Low Carb)</option>
                        <option value="vegan">Strict Vegan</option>
                        <option value="halal">Halal-compliant</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Duration (Days):</label>
                    <select class="form-select bg-light border-0 py-3" id="daysCountSelect" aria-label="Days count select">
                        <option value="7">7 Days (Full Week)</option>
                        <option value="3">3 Days (Weekend)</option>
                        <option value="1">1 Day (Quick Plan)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-custom w-100 py-3 mt-4" id="btn-planner-submit">
                        <i class="fa-solid fa-gears me-2"></i>Build Plan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Active plan preview -->
    <div id="planner-result-section">
        @if($currentPlan)
            <div class="glass-card p-5">
                <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                    <div>
                        <h3 class="playfair fw-bold mb-1 text-primary">{{ $currentPlan->name }}</h3>
                        <p class="text-muted mb-0">Target: <strong>{{ $currentPlan->calorie_target }} kcal/day</strong> | Diet: <strong>{{ ucfirst($currentPlan->diet_type) }}</strong></p>
                    </div>
                    <button class="btn btn-custom" onclick="generateShoppingList({{ $currentPlan->id }})">
                        <i class="fa-solid fa-list-check me-2"></i>Create Grocery List
                    </button>
                </div>

                <!-- Calendar layout -->
                <div class="d-flex flex-column gap-5">
                    @php 
                        $groupedItems = $currentPlan->items->groupBy(function($item) {
                            return $item->meal_date->format('Y-m-d');
                        });
                    @endphp

                    @foreach($groupedItems as $date => $items)
                        <div class="meal-plan-day-card p-4 bg-light-subtle rounded-4" style="border-radius: 16px;">
                            <h5 class="fw-bold mb-4 text-secondary"><i class="fa-solid fa-clock-rotate-left me-2"></i>{{ \Carbon\Carbon::parse($date)->format('l, M j, Y') }}</h5>
                            
                            <div class="row g-3">
                                @foreach($items as $item)
                                    @php $meal = $item->ai_recipe; @endphp
                                    <div class="col-md-6 col-lg-3">
                                        <div class="meal-item-row h-100 d-flex flex-column">
                                            <span class="badge bg-primary align-self-start mb-2 text-uppercase" style="font-size: 0.75rem;">{{ $item->meal_type }}</span>
                                            <h6 class="fw-bold text-dark mb-2">{{ $meal['name'] ?? 'Custom Meal' }}</h6>
                                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.8rem;">
                                                P: <strong>{{ $meal['protein'] ?? '0' }}g</strong> | C: <strong>{{ $meal['carbs'] ?? '0' }}g</strong> | F: <strong>{{ $meal['fats'] ?? '0' }}g</strong>
                                            </p>
                                            <span class="badge bg-light text-primary border align-self-start" style="font-size: 0.8rem;">{{ $meal['calories_per_serving'] ?? '0' }} kcal</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fa-solid fa-calendar-xmark text-primary mb-3" style="font-size: 65px;"></i>
                <h4 class="fw-bold">No active weekly meal plans found</h4>
                <p class="text-muted">Use the configuration form above to immediately formulate a calorie-targeted diet week menu.</p>
            </div>
        @endif
    </div>
</div>

<!-- Chef AI Loader Screen -->
<div class="chef-loader-overlay" id="chef-planner-loader">
    <div class="text-center">
        <i class="fa-solid fa-kitchen-set fa-bounce text-primary mb-3" style="font-size: 80px;"></i>
        <h3 class="fw-bold mt-2">ChefAI is formulating weekly menu...</h3>
        <p class="text-muted" style="max-width: 320px;">Applying nutritional formulas, building calorie matrix, and organizing custom recipes...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const slider = document.getElementById('calTarget');
    const sliderVal = document.getElementById('calTargetVal');
    const form = document.getElementById('generate-meal-plan-form');
    const loader = document.getElementById('chef-planner-loader');

    slider.addEventListener('input', () => {
        sliderVal.textContent = `${slider.value} kcal`;
    });

    // Handle AJAX AI Meal Plan generation
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        loader.style.display = 'flex';

        fetch("{{ route('meal-planner.generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                calorie_target: slider.value,
                diet_type: document.getElementById('dietTypeSelect').value,
                days: document.getElementById('daysCountSelect').value
            })
        })
        .then(res => res.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.success) {
                // Reload page to display generated plan
                window.location.reload();
            } else {
                alert('Generation failed: ' + (data.error || 'AI microservice unavailable'));
            }
        })
        .catch(() => {
            loader.style.display = 'none';
            alert('A network connectivity error occurred. Make sure your local Express AI service is running on port 3000.');
        });
    });

    // Handle automatic grocery shopping list creation
    function generateShoppingList(planId) {
        if (!confirm('Would you like ChefAI to automatically organize all the ingredients from this meal plan into a categorized shopping list?')) return;

        fetch(`{{ url("/meal-planner") }}/${planId}/shopping-list`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Shopping list successfully created!');
                window.location.href = "{{ route('shopping-list') }}";
            } else {
                alert('Failed to generate checklist.');
            }
        })
        .catch(() => {});
    }
</script>
@endsection
