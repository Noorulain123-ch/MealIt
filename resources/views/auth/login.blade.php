@extends('layouts.app')

@section('title', 'Log In — MealIt')

@section('content')
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5" data-aos="zoom-in">
            <div class="glass-card p-5">
                <div class="text-center mb-4">
                    <h2 class="playfair fw-bold"><i class="fa-solid fa-right-to-bracket text-primary me-2"></i>Welcome Back</h2>
                    <p class="text-muted small">Log in to manage your AI Meal plans and checklist</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small py-2 mb-3" style="border-radius: 8px;">
                        @foreach($errors->all() as $err)
                            <div class="fw-semibold">{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-envelope text-primary"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock text-primary"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-custom px-5 py-3 fs-6">Log In Account</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">New to MealIt? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Sign Up Here</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
