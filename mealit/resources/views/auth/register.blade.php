@extends('layouts.app')

@section('title', 'Get Started — MealIt')

@section('content')
<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <div class="text-center mb-4">
                    <h2 class="playfair fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i>Create Account</h2>
                    <p class="text-muted small">Access personalized AI recipe recommendations today</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small py-2 mb-3" style="border-radius: 8px;">
                        @foreach($errors->all() as $err)
                            <div class="fw-semibold">{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Your Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-user text-primary"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-envelope text-primary"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock text-primary"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock text-primary"></i></span>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-custom px-5 py-3 fs-6">Sign Up Free</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">Already registered? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">Log In Here</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
