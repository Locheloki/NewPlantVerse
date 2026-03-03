@extends('layouts.app')

@section('content')
<style>
    /* We reuse the exact same styles to keep the theme consistent */
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        background-color: #f0fdf4;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 2rem 0;
    }

    .auth-card {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 450px;
        /* Slightly wider for register form */
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-header h2 {
        color: #065f46;
        margin: 0 0 0.5rem 0;
        font-size: 1.75rem;
    }

    .auth-header p {
        color: #6b7280;
        margin: 0;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #374151;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .btn-primary {
        width: 100%;
        padding: 0.875rem;
        background-color: #10b981;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background-color: #059669;
    }

    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.9rem;
        color: #4b5563;
    }

    .auth-footer a {
        color: #10b981;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .error-box {
        background-color: #fef2f2;
        border-left: 4px solid #ef4444;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .error-box p {
        margin: 0 0 0.25rem 0;
    }

    .error-box p:last-child {
        margin-bottom: 0;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Start Growing 🌱</h2>
            <p>Create your PlantVerse account</p>
        </div>

        @if($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
            <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="planter@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn-primary">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Log in here</a>
        </div>
    </div>
</div>
@endsection