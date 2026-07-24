@extends('layouts.app')

@section('title', ($recipe['strMeal'] ?? 'Recipe') . ' — Web Recipes | MealIt')

@section('styles')
<style>
    .recipe-hero-img {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
    }
    .ingredient-list li {
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        font-size: 0.92rem;
    }
    .ingredient-list li:last-child { border-bottom: none; }
    .step-item {
        background: var(--bg-page);
        border-radius: var(--radius-sm);
        padding: 14px 18px;
        margin-bottom: 12px;
        border-left: 4px solid var(--primary);
        font-size: 0.93rem;
        line-height: 1.6;
    }
    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .youtube-embed {
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .tags-wrap .tag {
        background: rgba(232, 93, 4, 0.1);
        color: var(--primary);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid rgba(232, 93, 4, 0.2);
        display: inline-block;
        margin: 3px;
    }
    .ai-tips-box {
        background: linear-gradient(135deg, rgba(232,93,4,0.08), rgba(255,122,26,0.05));
        border: 1px solid rgba(232,93,4,0.2);
        border-radius: var(--radius);
        padding: 24px;
    }
</style>
@endsection

@section('content')
@php
    // Parse ingredients from MealDB format (strIngredient1..20 + strMeasure1..20)
    $ingredients = [];
    for ($i = 1; $i <= 20; $i++) {
        $ing = trim($recipe['strIngredient' . $i] ?? '');
        $mea = trim($recipe['strMeasure' . $i] ?? '');
        if ($ing) {
            $ingredients[] = ['name' => $ing, 'measure' => $mea];
        }
    }

    // Parse steps (strInstructions) by newline or numbered steps
    $rawSteps = trim($recipe['strInstructions'] ?? '');
    $steps    = array_filter(preg_split('/\r\n|\r|\n/', $rawSteps));
    $steps    = array_values(array_filter($steps, fn($s) => strlen(trim($s)) > 10));

    // YouTube ID
    $ytUrl  = $recipe['strYoutube'] ?? '';
    $ytId   = '';
    if ($ytUrl && preg_match('/[?&]v=([^&]+)/', $ytUrl, $m)) {
        $ytId = $m[1];
    }

    // Tags
    $tags = $recipe['strTags'] ? explode(',', $recipe['strTags']) : [];
@endphp

<div class="container py-5">

    {{-- Back navigation --}}
    <div class="mb-4">
        <a href="{{ route('web-recipes.index') }}" class="btn btn-outline-custom btn-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Web Recipes
        </a>
    </div>

    <div class="row g-5">

        {{-- Left: Image + Meta --}}
        <div class="col-lg-5" data-aos="fade-right">
            <img
                src="{{ $recipe['strMealThumb'] ?? 'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=800&q=80' }}"
                alt="{{ $recipe['strMeal'] }}"
                class="recipe-hero-img mb-4"
            >

            {{-- Meta badges --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                @if(!empty($recipe['strArea']))
                <span class="meta-badge" style="background: rgba(232,93,4,0.1); color: var(--primary);">
                    <i class="fa-solid fa-globe"></i> {{ $recipe['strArea'] }} Cuisine
                </span>
                @endif
                @if(!empty($recipe['strCategory']))
                <span class="meta-badge" style="background: rgba(45,157,94,0.1); color: var(--success);">
                    <i class="fa-solid fa-tag"></i> {{ $recipe['strCategory'] }}
                </span>
                @endif
            </div>

            {{-- Tags --}}
            @if(count($tags))
            <div class="tags-wrap mb-4">
                @foreach($tags as $tag)
                    @if(trim($tag))
                        <span class="tag">{{ trim($tag) }}</span>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- Source link --}}
            @if(!empty($recipe['strSource']))
            <a href="{{ $recipe['strSource'] }}" target="_blank" class="btn btn-outline-custom btn-sm w-100 mb-3">
                <i class="fa-solid fa-external-link me-2"></i>Original Source
            </a>
            @endif

            {{-- YouTube embed --}}
            @if($ytId)
            <div class="youtube-embed mt-4">
                <div class="ratio ratio-16x9">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $ytId }}"
                        title="{{ $recipe['strMeal'] }} video"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
                <div class="text-center py-2 text-muted small">
                    <i class="fa-brands fa-youtube text-danger me-1"></i>Watch video tutorial
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Details --}}
        <div class="col-lg-7" data-aos="fade-left">
            <h1 class="playfair fw-bold mb-3" style="font-size: 2rem; line-height: 1.2;">
                {{ $recipe['strMeal'] }}
            </h1>

            {{-- Quick action buttons --}}
            <div class="d-flex gap-2 flex-wrap mb-4">
                <a href="{{ route('web-recipes.random') }}" class="btn btn-outline-custom btn-sm">
                    <i class="fa-solid fa-shuffle me-1"></i>Try Random Recipe
                </a>
                @auth
                <button class="btn btn-custom btn-sm" onclick="saveToLocal()">
                    <i class="fa-solid fa-bookmark me-1"></i>Save Recipe
                </button>
                @endauth
            </div>

            {{-- Ingredients --}}
            <div class="glass-card p-4 mb-4">
                <h4 class="fw-bold mb-3">
                    <i class="fa-solid fa-basket-shopping text-primary me-2"></i>
                    Ingredients <span class="text-muted fw-normal fs-6">({{ count($ingredients) }} items)</span>
                </h4>
                <ul class="ingredient-list list-unstyled mb-0">
                    @foreach($ingredients as $ing)
                    <li>
                        <span class="fw-semibold">{{ $ing['name'] }}</span>
                        <span class="text-muted">{{ $ing['measure'] ?: '—' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Instructions --}}
            <div class="glass-card p-4 mb-4">
                <h4 class="fw-bold mb-3">
                    <i class="fa-solid fa-list-ol text-primary me-2"></i>Cooking Instructions
                </h4>
                @if(count($steps))
                    @foreach($steps as $i => $step)
                        <div class="step-item">
                            <strong class="text-primary me-2">{{ $i + 1 }}.</strong>
                            {{ trim($step) }}
                        </div>
                    @endforeach
                @else
                    <div class="step-item">{{ $rawSteps }}</div>
                @endif
            </div>

            {{-- AI Tips Box --}}
            <div class="ai-tips-box">
                <h5 class="fw-bold mb-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i>ChefAI Tips
                </h5>
                <p class="text-muted small mb-3">Want personalized cooking advice for this recipe?</p>
                <div id="ai-tips-content">
                    <button class="btn btn-custom btn-sm" id="btn-get-tips"
                        onclick="getAITips('{{ addslashes($recipe['strMeal']) }}', '{{ addslashes($recipe['strArea'] ?? '') }}')">
                        <i class="fa-solid fa-robot me-2"></i>Get AI Cooking Tips
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function getAITips(recipeName, cuisine) {
    const btn  = document.getElementById('btn-get-tips');
    const box  = document.getElementById('ai-tips-content');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>ChefAI thinking...';

    fetch('{{ url("/api/ai/tips") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ recipe_name: recipeName, cuisine: cuisine })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.tips) {
            const tipsHtml = data.tips.map((tip, i) =>
                `<div class="step-item mt-2"><strong class="text-primary me-1">${i+1}.</strong>${tip}</div>`
            ).join('');
            box.innerHTML = `<div class="fw-semibold mb-2 text-primary"><i class="fa-solid fa-check-circle me-1"></i>ChefAI's Top Tips:</div>${tipsHtml}`;
        } else {
            box.innerHTML = `<p class="text-muted small">${data.error || 'Could not load tips. Try again!'}</p>`;
        }
    })
    .catch(() => {
        box.innerHTML = '<p class="text-muted small">Could not connect to ChefAI. Try again later.</p>';
    });
}

function saveToLocal() {
    // Just show a simple toast for now
    alert('Recipe saved to bookmarks! (Feature coming soon)');
}
</script>
@endsection
