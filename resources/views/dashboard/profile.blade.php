@extends('layouts.app')

@section('title', 'Profile & Dietary Settings — MealIt')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h1 class="playfair fw-bold mb-1"><i class="fa-solid fa-user-gear text-primary me-2"></i>My Profile Settings</h1>
            <p class="text-muted">Optimize your ChefAI calibration by updating your bio and nutritional metrics.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 mb-4 p-3" style="border-radius: 12px;">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="glass-card p-5">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h4 class="fw-bold mb-4 text-primary"><i class="fa-regular fa-id-card me-2"></i>Personal Details</h4>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" name="name" class="form-control bg-light border-0 py-3" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control bg-light border-0 py-3" value="{{ $user->email }}" disabled>
                    <small class="text-muted">Email address changes require contacting support.</small>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Bio</label>
                    <textarea name="bio" class="form-control bg-light border-0 py-3" rows="3">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>

            <h4 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-heart-pulse me-2"></i>Nutritional & Caloric Calibration</h4>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Daily Calorie Target (kcal)</label>
                    <input type="number" name="calorie_target" class="form-control bg-light border-0 py-3" value="{{ old('calorie_target', $prefs->calorie_target) }}" placeholder="e.g. 2000">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Spice Level</label>
                    <select name="spice_level" class="form-select bg-light border-0 py-3" aria-label="Spice level select">
                        <option value="mild" {{ $prefs->spice_level == 'mild' ? 'selected' : '' }}>Mild</option>
                        <option value="medium" {{ $prefs->spice_level == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="spicy" {{ $prefs->spice_level == 'spicy' ? 'selected' : '' }}>Spicy</option>
                        <option value="extra_spicy" {{ $prefs->spice_level == 'extra_spicy' ? 'selected' : '' }}>Extra Spicy</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Cooking Skill</label>
                    <select name="skill_level" class="form-select bg-light border-0 py-3" aria-label="Skill level select">
                        <option value="beginner" {{ $prefs->skill_level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="easy" {{ $prefs->skill_level == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ $prefs->skill_level == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="advanced" {{ $prefs->skill_level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="professional" {{ $prefs->skill_level == 'professional' ? 'selected' : '' }}>Professional Chef</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-custom px-5 py-3 fs-5">Save Configuration Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
