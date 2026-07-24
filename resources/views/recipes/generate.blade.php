@extends('layouts.app')

@section('title', 'ChefAI Custom Recipe Generator — MealIt')

@section('styles')
<style>
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }

    .step-indicator::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: var(--border);
        z-index: 1;
    }

    .step-node {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--bg-card);
        border: 3px solid var(--border);
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        z-index: 2;
        transition: var(--transition);
        color: var(--text-muted);
    }

    .step-node.active {
        border-color: var(--primary);
        color: var(--primary);
        background-color: var(--bg-card);
        box-shadow: 0 0 15px rgba(232, 93, 4, 0.25);
    }

    .step-node.completed {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .chip-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg-page);
        min-height: 55px;
    }

    .chip {
        background-color: var(--primary);
        color: white;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        animation: scaleIn 0.2s ease-out;
    }

    .chip i {
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .chip i:hover {
        color: #ffcccc;
    }

    .popular-chip {
        padding: 8px 16px;
        border-radius: 30px;
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition-fast);
        font-size: 0.85rem;
    }

    .popular-chip:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Loader */
    .chef-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(var(--bg) === '#0F0F1A' ? '15,15,26' : '255,255,255', 0.9);
        backdrop-filter: blur(10px);
        z-index: 2000;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    /* Output recipe details styling */
    .ai-recipe-card {
        border-top: 5px solid var(--primary);
    }

    @keyframes scaleIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="playfair text-gradient" style="background: linear-gradient(135deg, var(--primary), #FF7A1A); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">ChefAI Recipe Generator</h1>
                <p class="text-muted">Set your ingredients, tastes, dietary restrictions, and watch the magic happen.</p>
            </div>

            <!-- Form multi-step steps indicator -->
            <div class="step-indicator">
                <div class="step-node active" id="step-node-1">1</div>
                <div class="step-node" id="step-node-2">2</div>
                <div class="step-node" id="step-node-3">3</div>
            </div>

            <form id="ai-generator-form">
                <!-- Step 1: Ingredients -->
                <div class="glass-card p-5 mb-4" id="step-content-1">
                    <h4 class="fw-bold mb-3"><i class="fa-solid fa-carrot text-primary me-2"></i>What ingredients do you have?</h4>
                    <p class="text-muted small">Type in any ingredient (e.g. Chicken, Tomato, Rice) and press Enter or select popular ones below.</p>
                    
                    <div class="mb-4">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fa-solid fa-plus text-primary"></i></span>
                            <input type="text" class="form-control border-start-0" id="ingredient-input" placeholder="Type ingredient name and press Enter...">
                        </div>
                        <div class="chip-container" id="added-ingredients-container">
                            <!-- Dynamically added chips -->
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Popular Ingredients:</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($ingredients->take(12) as $ing)
                            <div class="popular-chip" onclick="addIngredientChip('{{ $ing->name }}')">+ {{ $ing->name }}</div>
                        @endforeach
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-custom px-5 py-3" onclick="nextStep(2)">Continue to Diet & Taste <i class="fa-solid fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Diet and Spice -->
                <div class="glass-card p-5 mb-4" id="step-content-2" style="display: none;">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-pepper-hot text-primary me-2"></i>Diet & Taste Preferences</h4>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cuisine Type:</label>
                            <select class="form-select bg-light border-0 py-3" id="ai-cuisine" aria-label="Cuisine select">
                                <option value="">Any Cuisine</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Spice Level Preference:</label>
                            <select class="form-select bg-light border-0 py-3" id="ai-spice" aria-label="Spice level">
                                <option value="medium">Medium</option>
                                <option value="mild">Mild</option>
                                <option value="spicy">Spicy</option>
                                <option value="extra_spicy">Extra Spicy 🌶️</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Skill Level:</label>
                            <select class="form-select bg-light border-0 py-3" id="ai-difficulty" aria-label="Skill level">
                                <option value="easy">Easy</option>
                                <option value="beginner">Beginner</option>
                                <option value="medium">Medium</option>
                                <option value="advanced">Advanced</option>
                                <option value="professional">Professional Chef</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maximum Cook Time (mins):</label>
                            <input type="number" class="form-control bg-light border-0 py-3" id="ai-max-time" placeholder="e.g. 45" min="5" max="300">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">Dietary Restrictions:</label>
                            <div class="row g-2">
                                <div class="col-md-3 col-6">
                                    <div class="form-check card p-3 border-light bg-light" style="border-radius: 12px;">
                                        <input class="form-check-input" type="checkbox" value="halal" id="diet-halal" checked>
                                        <label class="form-check-label fw-semibold" for="diet-halal">Halal</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-check card p-3 border-light bg-light" style="border-radius: 12px;">
                                        <input class="form-check-input" type="checkbox" value="vegetarian" id="diet-veg">
                                        <label class="form-check-label fw-semibold" for="diet-veg">Vegetarian</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-check card p-3 border-light bg-light" style="border-radius: 12px;">
                                        <input class="form-check-input" type="checkbox" value="vegan" id="diet-vegan">
                                        <label class="form-check-label fw-semibold" for="diet-vegan">Vegan</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-check card p-3 border-light bg-light" style="border-radius: 12px;">
                                        <input class="form-check-input" type="checkbox" value="keto" id="diet-keto">
                                        <label class="form-check-label fw-semibold" for="diet-keto">Keto</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-custom px-4 py-3" onclick="nextStep(1)"><i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                        <button type="button" class="btn btn-custom px-5 py-3" onclick="nextStep(3)">Continue to Health Goals <i class="fa-solid fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 3: Health & Mood -->
                <div class="glass-card p-5 mb-4" id="step-content-3" style="display: none;">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-heart-pulse text-primary me-2"></i>Health Goals & Current Mood</h4>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Health/Fitness Goal:</label>
                            <select class="form-select bg-light border-0 py-3" id="ai-health" aria-label="Health goal">
                                <option value="maintenance">Maintenance</option>
                                <option value="weight_loss">Weight Loss</option>
                                <option value="muscle_gain">Muscle Gain (High Protein)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">What is your mood today?</label>
                            <select class="form-select bg-light border-0 py-3" id="ai-mood" aria-label="Mood select">
                                <option value="any">No Mood Match</option>
                                <option value="comforting">Comforting (warm and rich) 🍲</option>
                                <option value="energizing">Energizing (clean and active) ⚡</option>
                                <option value="light">Light & Refreshing 🥗</option>
                                <option value="indulgent">Indulgent (rich treat) 🍫</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-custom px-4 py-3" onclick="nextStep(2)"><i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                        <button type="submit" class="btn btn-custom px-5 py-3 fs-5" id="btn-submit-ai"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Generate Recipe Now!</button>
                    </div>
                </div>
            </form>

            <!-- Results container -->
            <div id="ai-recipes-results" class="mt-5" style="display: none;">
                <h3 class="playfair fw-bold text-center mb-4 text-primary"><i class="fa-solid fa-book-open me-2"></i>Your AI-Generated Recipes</h3>
                <div class="d-flex flex-column gap-4" id="recipes-list-output"></div>
            </div>
        </div>
    </div>
</div>

<!-- Full Screen AI Loader screen -->
<div class="chef-loader-overlay" id="chef-loader">
    <div class="text-center">
        <i class="fa-solid fa-fire fa-bounce text-primary mb-3" style="font-size: 80px;"></i>
        <h3 class="fw-bold mt-2">ChefAI is cooking...</h3>
        <p class="text-muted" style="max-width: 320px;">Analyzing ingredients, formulating nutritional matrix, and building premium custom directions...</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ingredients = [];

    // Chip adder
    const ingredientInput = document.getElementById('ingredient-input');
    const chipContainer = document.getElementById('added-ingredients-container');

    ingredientInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = ingredientInput.value.trim();
            if (val) {
                addIngredientChip(val);
                ingredientInput.value = '';
            }
        }
    });

    function addIngredientChip(name) {
        const lowerName = name.toLowerCase();
        if (ingredients.includes(lowerName)) return;

        ingredients.push(lowerName);

        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.id = `chip-${lowerName}`;
        chip.innerHTML = `${name} <i class="fa-solid fa-xmark" onclick="removeIngredientChip('${lowerName}')"></i>`;
        
        chipContainer.appendChild(chip);
    }

    function removeIngredientChip(name) {
        const index = ingredients.indexOf(name);
        if (index > -1) {
            ingredients.splice(index, 1);
        }
        const chip = document.getElementById(`chip-${name}`);
        if (chip) chip.remove();
    }

    // Step switching logic
    function nextStep(step) {
        if (step === 2 && ingredients.length === 0) {
            alert('Please add at least one ingredient before moving forward!');
            return;
        }

        // Hide all steps
        document.getElementById('step-content-1').style.display = 'none';
        document.getElementById('step-content-2').style.display = 'none';
        document.getElementById('step-content-3').style.display = 'none';

        // Reset step indicators
        document.getElementById('step-node-1').className = 'step-node';
        document.getElementById('step-node-2').className = 'step-node';
        document.getElementById('step-node-3').className = 'step-node';

        // Apply active states
        if (step >= 1) document.getElementById('step-node-1').classList.add('completed');
        if (step >= 2) document.getElementById('step-node-2').classList.add('completed');
        if (step >= 3) document.getElementById('step-node-3').classList.add('completed');

        document.getElementById(`step-node-${step}`).className = 'step-node active';
        document.getElementById(`step-content-${step}`).style.display = 'block';
    }

    // Form submission & AJAX AI calling logic
    const form = document.getElementById('ai-generator-form');
    const loader = document.getElementById('chef-loader');
    const resultsContainer = document.getElementById('ai-recipes-results');
    const outputList = document.getElementById('recipes-list-output');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // Build preferences object
        const selectedDiets = [];
        if (document.getElementById('diet-halal').checked) selectedDiets.push('halal');
        if (document.getElementById('diet-veg').checked) selectedDiets.push('vegetarian');
        if (document.getElementById('diet-vegan').checked) selectedDiets.push('vegan');
        if (document.getElementById('diet-keto').checked) selectedDiets.push('keto');

        const postData = {
            ingredients: ingredients,
            cuisine: document.getElementById('ai-cuisine').value,
            spice_level: document.getElementById('ai-spice').value,
            difficulty: document.getElementById('ai-difficulty').value,
            max_time: document.getElementById('ai-max-time').value,
            dietary: selectedDiets,
            health_goal: document.getElementById('ai-health').value,
            mood: document.getElementById('ai-mood').value
        };

        loader.style.display = 'flex';
        resultsContainer.style.display = 'none';

        fetch('{{ url("/api/ai/generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(postData)
        })
        .then(res => res.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.success && data.recipes) {
                outputList.innerHTML = '';
                
                data.recipes.forEach((recipe, idx) => {
                    const stepsHTML = recipe.steps.map(step => `<li>${step}</li>`).join('');
                    const ingHTML = recipe.ingredients.map(ing => `<li><strong>${ing.name}</strong> — ${ing.quantity}</li>`).join('');
                    
                    const card = document.createElement('div');
                    card.className = 'glass-card p-5 mb-4 ai-recipe-card';
                    card.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start flex-wrap g-3">
                            <div>
                                <h3 class="fw-bold text-primary mb-1">${recipe.name}</h3>
                                <p class="text-muted small mb-3">${recipe.cuisine} Cuisine | Match Mood: <strong>${recipe.mood_match || 'Any'}</strong></p>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge badge-medium px-3 py-2 fs-6">Calories: ${recipe.calories_per_serving} kcal</span>
                                <span class="badge bg-success px-3 py-2 fs-6">Macros: P ${recipe.protein}g | C ${recipe.carbs}g | F ${recipe.fats}g</span>
                            </div>
                        </div>

                        <p class="lead small italic my-3 text-muted">"${recipe.description}"</p>
                        
                        <div class="row g-4 my-3">
                            <div class="col-md-5 border-end border-light">
                                <h5 class="fw-bold mb-3 text-success"><i class="fa-solid fa-basket-shopping me-2"></i>Ingredients</h5>
                                <ul>${ingHTML}</ul>
                            </div>
                            <div class="col-md-7">
                                <h5 class="fw-bold mb-3 text-success"><i class="fa-solid fa-list-ol me-2"></i>Cooking Directions</h5>
                                <ol>${stepsHTML}</ol>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 p-3 mt-4" style="border-radius: 12px;">
                            <h6 class="fw-bold"><i class="fa-solid fa-lightbulb me-2"></i>ChefAI Professional Cooking Tips:</h6>
                            <ul class="mb-0 text-dark">${recipe.cooking_tips.map(tip => `<li>${tip}</li>`).join('')}</ul>
                        </div>
                    `;
                    outputList.appendChild(card);
                });
                
                resultsContainer.style.display = 'block';
                resultsContainer.scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Generation failed: ' + (data.error || 'Server error'));
            }
        })
        .catch((err) => {
            loader.style.display = 'none';
            alert('A network connection error occurred. Make sure your local Express AI service is running on port 3000.');
        });
    });
</script>
@endsection
