@extends('layouts.app')

@section('title', $recipe->title . ' — AI Recipes')

@section('styles')
<style>
    .recipe-header-container {
        height: 400px;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    .recipe-header-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .recipe-header-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 100%);
        display: flex;
        align-items: flex-end;
        padding-bottom: 40px;
    }

    .calculator-card {
        background-color: var(--primary);
        color: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow-primary);
        border: none;
    }

    .calculator-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-weight: 700;
        transition: var(--transition-fast);
    }

    .calculator-btn:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .ingredient-check-item {
        cursor: pointer;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        display: flex;
        align-items: center;
        gap: 12px;
        transition: var(--transition-fast);
    }

    .ingredient-check-item:hover {
        border-color: var(--primary);
    }

    .ingredient-check-item.checked {
        opacity: 0.65;
        background-color: var(--bg-page);
    }

    .ingredient-check-item.checked span {
        text-decoration: line-through;
    }

    .macro-chart-container {
        width: 100%;
        max-width: 320px;
        margin: 0 auto;
    }

    /* Rating Stars input */
    .star-rating-input {
        display: flex;
        flex-direction: row-reverse;
        gap: 6px;
        font-size: 1.5rem;
    }

    .star-rating-input input { display: none; }
    .star-rating-input label {
        color: var(--text-muted);
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .star-rating-input input:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label {
        color: #FFC107;
    }
</style>
@endsection

@section('content')
<!-- Exquisite Header with Cover Image -->
<div class="recipe-header-container" data-aos="fade-down">
    <img src="{{ $recipe->image }}" class="recipe-header-img" alt="{{ $recipe->title }}">
    <div class="recipe-header-overlay">
        <div class="container text-white">
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <span class="badge bg-primary px-3 py-2 fs-6">{{ $recipe->cuisine_type }} Cuisine</span>
                <span class="badge bg-success px-3 py-2 fs-6"><i class="fa-solid fa-clock me-2"></i>{{ $recipe->cooking_time }} Mins</span>
                <span class="badge bg-warning text-dark px-3 py-2 fs-6"><i class="fa-solid fa-star me-2"></i>{{ $recipe->avg_rating }} ({{ $recipe->review_count }} Reviews)</span>
            </div>
            
            <h1 class="playfair text-white display-4 fw-bold mb-2">{{ $recipe->title }}</h1>
            <p class="lead text-white-50 mb-0" style="max-width: 700px;">{{ $recipe->description }}</p>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Main content column -->
        <div class="col-lg-8">
            <!-- Portions scaling calculator -->
            <div class="calculator-card p-4 mb-5" data-aos="fade-up">
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <h4 class="fw-bold mb-1"><i class="fa-solid fa-calculator me-2"></i>Portion Calculator</h4>
                        <p class="mb-0 text-white-50 small">Scale ingredients list dynamically based on desired servings.</p>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <div class="d-inline-flex align-items-center gap-3">
                            <button class="calculator-btn" id="servings-dec">-</button>
                            <span class="fs-4 fw-bold" id="servings-count">{{ $recipe->servings }}</span>
                            <button class="calculator-btn" id="servings-inc">+</button>
                            <span class="fw-semibold">Servings</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients checklist -->
            <div class="mb-5" data-aos="fade-up">
                <h3 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-basket-shopping me-2"></i>Ingredients Checklist</h3>
                <div class="d-flex flex-column gap-2">
                    @foreach($recipe->ingredients as $ing)
                        <div class="ingredient-check-item" onclick="toggleIngredient(this)">
                            <div class="form-check p-0 m-0">
                                <input class="form-check-input ms-0 float-none" type="checkbox" aria-label="Check ingredient">
                            </div>
                            <span class="ingredient-qty fw-bold text-primary" data-base-qty="{{ floatval($ing->pivot->quantity) ?: 1 }}" data-unit="{{ preg_replace('/[0-9.\s]+/', '', $ing->pivot->quantity) }}">
                                {{ $ing->pivot->quantity }}
                            </span>
                            <span class="ingredient-name" style="color: var(--text);">{{ $ing->name }}</span>
                            @if($ing->pivot->is_optional)
                                <small class="text-muted">(Optional)</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Directions -->
            <div class="mb-5" data-aos="fade-up">
                <h3 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-list-ol me-2"></i>Directions</h3>
                <div class="d-flex flex-column gap-4">
                    @php $steps = is_array($recipe->instructions) ? $recipe->instructions : json_decode($recipe->instructions, true); @endphp
                    @foreach($steps as $idx => $step)
                        <div class="d-flex gap-3">
                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 36px; height: 36px; font-weight: 700;">
                                {{ $idx + 1 }}
                            </div>
                            <div class="pt-1">
                                <p class="mb-0 fs-5" style="color: var(--text); line-height: 1.6;">{{ preg_replace('/^[0-9.\s]+/', '', $step) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Macro chart column -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4" data-aos="fade-left">
                <h4 class="fw-bold mb-4 text-center"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Nutrition Matrix</h4>
                
                <div class="macro-chart-container mb-4">
                    <canvas id="macroChart" width="200" height="200"></canvas>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Calories</span>
                        <span class="fw-bold text-primary">{{ $recipe->calories }} kcal</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Protein</span>
                        <span class="fw-bold">{{ $recipe->protein }}g</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Carbohydrates</span>
                        <span class="fw-bold">{{ $recipe->carbs }}g</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">Fats</span>
                        <span class="fw-bold">{{ $recipe->fats }}g</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEO Structured Data Schema.org Recipe -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Recipe",
  "name": "{{ $recipe->title }}",
  "image": "{{ $recipe->image }}",
  "description": "{{ $recipe->description }}",
  "cookTime": "PT{{ $recipe->cooking_time }}M",
  "recipeYield": "{{ $recipe->servings }} servings",
  "recipeCategory": "{{ $recipe->meal_type }}",
  "recipeCuisine": "{{ $recipe->cuisine_type }}",
  "nutrition": {
    "@type": "NutritionInformation",
    "calories": "{{ $recipe->calories }} calories",
    "proteinContent": "{{ $recipe->protein }}g",
    "carbohydrateContent": "{{ $recipe->carbs }}g",
    "fatContent": "{{ $recipe->fats }}g"
  }
}
</script>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Scale quantity multiplier logic
    const baseServings = {{ $recipe->servings }};
    let currentServings = baseServings;
    const servingsCount = document.getElementById('servings-count');
    const ingredientQties = document.querySelectorAll('.ingredient-qty');

    document.getElementById('servings-inc').addEventListener('click', () => {
        currentServings++;
        updateIngredients();
    });

    document.getElementById('servings-dec').addEventListener('click', () => {
        if (currentServings > 1) {
            currentServings--;
            updateIngredients();
        }
    });

    function updateIngredients() {
        servingsCount.textContent = currentServings;
        const scale = currentServings / baseServings;

        ingredientQties.forEach(el => {
            const baseQty = parseFloat(el.getAttribute('data-base-qty'));
            const unit = el.getAttribute('data-unit') || '';
            if (!isNaN(baseQty)) {
                const scaledQty = (baseQty * scale).toFixed(1).replace(/\.0$/, '');
                el.textContent = `${scaledQty} ${unit}`;
            }
        });
    }

    function toggleIngredient(card) {
        card.classList.toggle('checked');
        const check = card.querySelector('input');
        if (check) check.checked = !check.checked;
    }

    // ChartJS Nutrition breakdown
    const ctx = document.getElementById('macroChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Protein', 'Carbs', 'Fats'],
            datasets: [{
                data: [{{ $recipe->protein }}, {{ $recipe->carbs }}, {{ $recipe->fats }}],
                backgroundColor: ['#2D9D5E', '#F4A261', '#E85D04'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#E8E8F0' : '#212529'
                    }
                }
            }
        }
    });
</script>
@endsection
