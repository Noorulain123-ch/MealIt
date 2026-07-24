@extends('layouts.app')

@section('title', 'Saved Recipes & Bookmarks — MealIt')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h1 class="playfair fw-bold mb-1"><i class="fa-solid fa-bookmark text-primary me-2"></i>My Saved Recipes</h1>
            <p class="text-muted">Manage your favorite dishes and personal collections.</p>
        </div>
    </div>

    <!-- Bookmarks Grid -->
    <div class="row g-4">
        @forelse($favorites as $recipe)
            <div class="col-md-4">
                <div class="glass-card h-100 overflow-hidden d-flex flex-column">
                    <div class="recipe-img-container" style="position: relative; height: 180px; overflow: hidden;">
                        <img src="{{ $recipe->image }}" class="recipe-img" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $recipe->title }}">
                        <span class="recipe-cuisine-badge" style="position: absolute; top: 12px; left: 12px; background: rgba(0, 0, 0, 0.6); color: white; padding: 4px 10px; border-radius: 30px; font-size: 0.75rem;">{{ $recipe->cuisine_type }}</span>
                        <button class="btn btn-sm btn-light border-0 shadow-sm rounded-circle d-flex justify-content-center align-items-center" onclick="removeBookmark({{ $recipe->id }}, this)" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; color: var(--primary);"><i class="fa-solid fa-star"></i></button>
                    </div>
                    
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h5 class="fw-bold mb-2">
                            <a href="{{ route('recipes.show', $recipe->slug) }}" class="text-decoration-none" style="color: var(--text);">{{ $recipe->title }}</a>
                        </h5>
                        <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.85rem;">{{ Str::limit($recipe->description, 80) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3 border-light">
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock me-1"></i>{{ $recipe->cooking_time }} mins</span>
                            <span>{!! $recipe->difficulty_badge !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-bookmark text-primary mb-3" style="font-size: 60px;"></i>
                <h4 class="fw-bold">No saved recipes found</h4>
                <p class="text-muted">Click the favorite star on any recipe to see it here.</p>
                <a href="{{ route('recipes.index') }}" class="btn btn-custom mt-3">Explore Recipes</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    function removeBookmark(recipeId, button) {
        if (!confirm('Are you sure you want to remove this recipe from favorites?')) return;

        fetch(`{{ url("/bookmarks") }}/${recipeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            // Reload page to reflect change
            window.location.reload();
        })
        .catch(() => {});
    }
</script>
@endsection
