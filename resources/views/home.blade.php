@extends('layouts.app')

@section('title', 'MealIt — AI-Powered Custom Recipe Discovery & Meal Planner')

@section('styles')
<style>
    .hero-section {
        padding: 100px 0 140px;
        position: relative;
        background: radial-gradient(circle at 10% 20%, rgba(232, 93, 4, 0.05) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(27, 67, 50, 0.04) 0%, transparent 40%);
        overflow: hidden;
    }

    .hero-badge {
        background: rgba(232, 93, 4, 0.1);
        border: 1px solid rgba(232, 93, 4, 0.2);
        color: var(--primary);
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
        margin-bottom: 20px;
    }

    .hero-title {
        font-size: 3.8rem;
        line-height: 1.15;
        margin-bottom: 24px;
        color: var(--text);
    }

    .hero-title span {
        color: var(--primary);
    }

    .search-bar-container {
        position: relative;
        max-width: 600px;
        margin-top: 35px;
    }

    .search-input {
        width: 100%;
        padding: 18px 30px 18px 60px;
        border-radius: 50px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text);
        box-shadow: var(--shadow);
        font-size: 1.1rem;
        font-weight: 500;
        outline: none;
        transition: var(--transition);
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 10px 40px rgba(232, 93, 4, 0.12);
    }

    .search-icon {
        position: absolute;
        top: 50%;
        left: 24px;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.25rem;
    }

    .voice-search-btn {
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .voice-search-btn:hover {
        color: var(--primary);
    }

    .autocomplete-box {
        position: absolute;
        top: 105%;
        left: 0;
        right: 0;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        z-index: 999;
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    .autocomplete-item {
        padding: 12px 24px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: var(--transition-fast);
        text-decoration: none;
    }

    .autocomplete-item:hover {
        background-color: var(--bg-page);
        color: var(--primary);
    }

    .stat-card {
        padding: 24px;
        text-align: center;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--bg-card);
    }

    .stat-number {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--primary);
    }

    .section-title {
        font-size: 2.2rem;
        margin-bottom: 12px;
        position: relative;
    }

    .section-subtitle {
        color: var(--text-muted);
        margin-bottom: 45px;
        font-size: 1.05rem;
    }

    /* Cards customization */
    .recipe-img-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .recipe-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .glass-card:hover .recipe-img {
        transform: scale(1.08);
    }

    .recipe-cuisine-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(0, 0, 0, 0.65);
        color: white;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    .recipe-rating {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.9);
        color: #E85D04;
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 700;
        box-shadow: var(--shadow-sm);
    }

    .recipe-meta-item {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cuisine-chip {
        padding: 12px 28px;
        border-radius: 50px;
        background: var(--bg-card);
        color: var(--text);
        font-weight: 600;
        border: 1px solid var(--border);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .cuisine-chip:hover {
        background: var(--primary);
        color: white !important;
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.6rem;
        }
        .hero-section {
            padding: 60px 0 100px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="hero-badge"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Powered by ChefAI Gemini 1.5</span>
                <h1 class="hero-title playfair">What will you <br><span>cook today?</span></h1>
                <p class="lead text-muted" style="max-width: 500px;">
                    Discover personalized recipes, design smart meal plans based on your exact caloric and macro goals, and auto-build grocery shopping lists.
                </p>
                
                <!-- Autocomplete Search Bar -->
                <div class="search-bar-container">
                    <form action="{{ route('search') }}" method="GET">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="q" class="search-input" id="search-input-field" placeholder="Search dish names, ingredients, or cuisines..." autocomplete="off">
                        <button type="button" class="voice-search-btn" id="voice-search" aria-label="Voice search"><i class="fa-solid fa-microphone"></i></button>
                    </form>
                    <div class="autocomplete-box" id="autocomplete-results"></div>
                </div>
            </div>
            
            <div class="col-lg-6 d-none d-lg-block" data-aos="zoom-in" data-aos-delay="200">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=80" alt="Exquisite culinary dish representation" class="img-fluid rounded-circle border border-4 border-white shadow-lg" style="width: 500px; height: 500px; object-fit: cover;">
                    <div class="glass-card position-absolute p-3 d-flex align-items-center gap-3" style="top: 20px; left: -20px; border-radius: 20px;">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-fire fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Biryani Recipe</small>
                            <span class="fw-bold">650 kcal | Spicy</span>
                        </div>
                    </div>
                    
                    <div class="glass-card position-absolute p-3 d-flex align-items-center gap-3" style="bottom: 40px; right: 0; border-radius: 20px;">
                        <i class="fa-solid fa-clock text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">Cooking Time</small>
                            <span class="fw-bold">Under 30 Mins</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Showcase -->
<section class="py-5 bg-white border-top border-bottom border-light">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['recipes'] }}</div>
                    <div class="text-muted fw-semibold">Healthy Recipes</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['cuisines'] }}</div>
                    <div class="text-muted fw-semibold">World Cuisines</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['users'] }}</div>
                    <div class="text-muted fw-semibold">Happy Cooks</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Recipes -->
@if(count($featuredRecipes) > 0)
<section class="py-5 my-3">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title playfair">Weekly Featured Recipes</h2>
            <p class="section-subtitle">Exquisite culinary masterpieces handpicked by our culinary experts.</p>
        </div>
        
        <div class="row g-4">
            @foreach($featuredRecipes as $recipe)
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <div class="glass-card h-100 overflow-hidden d-flex flex-column">
                        <div class="recipe-img-container">
                            <img src="{{ $recipe->image }}" class="recipe-img" alt="{{ $recipe->title }}">
                            <span class="recipe-cuisine-badge">{{ $recipe->cuisine_type }}</span>
                            <span class="recipe-rating"><i class="fa-solid fa-star me-1"></i>{{ $recipe->avg_rating }}</span>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold mb-2"><a href="{{ route('recipes.show', $recipe->slug) }}" class="text-decoration-none" style="color: var(--text);">{{ $recipe->title }}</a></h5>
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($recipe->description, 75) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3 border-light">
                                <span class="recipe-meta-item"><i class="fa-regular fa-clock"></i>{{ $recipe->cooking_time }} mins</span>
                                <span class="recipe-meta-item"><i class="fa-solid fa-bowl-rice"></i>{!! $recipe->difficulty_badge !!}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- World Cuisines Slider -->
<section class="py-5 bg-light-subtle border-top border-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title playfair">Explore World Cuisines</h2>
            <p class="section-subtitle">Jump straight to your favorite flavor profile.</p>
        </div>
        
        <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
            @foreach($cuisines as $cuisine)
                <a href="{{ route('recipes.index', ['cuisine' => $cuisine->name]) }}" class="cuisine-chip">
                    <i class="fa-solid {{ $cuisine->icon }} text-primary"></i>
                    <span>{{ $cuisine->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Call-to-Action for AI recipe finder -->
<section class="py-5 my-5">
    <div class="container">
        <div class="glass-card p-5 overflow-hidden position-relative rounded-4" style="background: linear-gradient(135deg, rgba(27, 67, 50, 0.95), rgba(45, 157, 94, 0.95)); border: none; color: white;" data-aos="zoom-in">
            <div class="position-absolute" style="right: -50px; bottom: -50px; opacity: 0.15;">
                <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 250px;"></i>
            </div>
            
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge mb-3 py-2 px-3 bg-white text-success fw-bold"><i class="fa-solid fa-seedling me-2"></i>SMART RECOVERY</span>
                    <h2 class="playfair text-white fs-1 mb-3">Tired of thinking what to cook?</h2>
                    <p class="lead text-white-50 mb-0" style="max-width: 600px;">
                        Simply tell ChefAI the leftover ingredients in your fridge, and let it generate a perfectly balanced recipe immediately!
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('generate') }}" class="btn btn-light btn-custom text-success py-3 px-5 fs-5 shadow border-0" style="background: white !important; color: #1B4332 !important; border-radius: 30px;">
                        Try AI Generator<i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Search Autocomplete suggestions implementation
    const searchField = document.getElementById('search-input-field');
    const autocompleteResults = document.getElementById('autocomplete-results');

    searchField.addEventListener('input', () => {
        const query = searchField.value.trim();
        if (query.length < 2) {
            autocompleteResults.style.display = 'none';
            return;
        }

        fetch('{{ url("/recipes/autocomplete") }}?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    autocompleteResults.style.display = 'none';
                    return;
                }

                autocompleteResults.innerHTML = '';
                data.forEach(item => {
                    const a = document.createElement('a');
                    a.className = 'autocomplete-item';
                    a.href = '{{ url("/recipes") }}/' + item.slug;
                    a.innerHTML = `
                        <img src="${item.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&q=80'}" width="36" height="36" class="rounded-circle object-fit-cover" alt="Image">
                        <div>
                            <span class="fw-bold d-block">${item.title}</span>
                            <small class="text-muted">${item.cuisine_type} Cuisine</small>
                        </div>
                    `;
                    autocompleteResults.appendChild(a);
                });
                autocompleteResults.style.display = 'flex';
            })
            .catch(() => {});
    });

    document.addEventListener('click', (e) => {
        if (!searchField.contains(e.target) && !autocompleteResults.contains(e.target)) {
            autocompleteResults.style.display = 'none';
        }
    });

    // Web Speech API Voice Search integration
    const voiceBtn = document.getElementById('voice-search');
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';

        voiceBtn.addEventListener('click', () => {
            voiceBtn.innerHTML = '<i class="fa-solid fa-microphone fa-beat text-danger"></i>';
            recognition.start();
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            searchField.value = transcript;
            voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
            // Auto trigger search
            searchField.closest('form').submit();
        };

        recognition.onerror = () => {
            voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
        };

        recognition.onend = () => {
            voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
        };
    } else {
        voiceBtn.style.display = 'none';
    }
</script>
@endsection
