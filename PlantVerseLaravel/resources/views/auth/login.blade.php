@extends('layouts.app')

@section('content')
<style>
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        background-color: #f0fdf4;
        /* Soft green background */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .auth-card {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 400px;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-header h2 {
        color: #065f46;
        /* Dark emerald */
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
        /* Emerald 500 */
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
        /* Emerald 600 */
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
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back! 🌿</h2>
            <p>Log in to tend to your PlantVerse</p>
        </div>

        @if($errors->any())
        <div class="error-box">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="planter@example.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Join PlantVerse</a>
        </div>
    </div>
</div>
@endsection