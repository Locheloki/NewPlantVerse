@extends('layouts.app')

@section('content')
<style>
    /* Reuse the core layout and animations from the login page */
    .auth-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #0f766e 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        z-index: 50;
    }

    .jungle-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 1;
        animation: float 10s infinite ease-in-out;
    }

    .orb-1 {
        top: -10%;
        left: -10%;
        width: 500px;
        height: 500px;
        background: rgba(52, 211, 153, 0.4);
    }

    .orb-2 {
        bottom: -20%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: rgba(251, 191, 36, 0.3);
        animation-delay: -5s;
    }

    .orb-3 {
        top: 40%;
        left: 60%;
        width: 300px;
        height: 300px;
        background: rgba(16, 185, 129, 0.4);
        animation-duration: 15s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) scale(1);
        }

        50% {
            transform: translateY(-30px) scale(1.05);
        }
    }

    /* Slightly wider card for registration */
    .glass-card {
        position: relative;
        z-index: 10;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 24px;
        padding: 2.5rem 2.5rem;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        color: #ffffff;
        max-height: 95vh;
        overflow-y: auto;
    }

    /* Custom scrollbar for the card if screen is tiny */
    .glass-card::-webkit-scrollbar {
        width: 6px;
    }

    .glass-card::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }

    .glass-card::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        letter-spacing: 1px;
    }

    .auth-header p {
        color: #a7f3d0;
        margin: 0;
        font-size: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #d1fae5;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-input {
        width: 100%;
        padding: 0.875rem 1rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        box-sizing: border-box;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .glass-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .glass-input:focus {
        outline: none;
        border-color: #34d399;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 15px rgba(52, 211, 153, 0.3);
    }

    .btn-glow {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 20px -10px rgba(16, 185, 129, 0.5);
    }

    .btn-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -10px rgba(16, 185, 129, 0.7);
        filter: brightness(1.1);
    }

    .auth-footer {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
        color: #a7f3d0;
    }

    .auth-footer a {
        color: #34d399;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.2s;
    }

    .auth-footer a:hover {
        color: #ffffff;
        text-shadow: 0 0 8px rgba(52, 211, 153, 0.8);
    }

    .glass-error {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        backdrop-filter: blur(10px);
    }

    .glass-error p {
        margin: 0 0 0.25rem 0;
    }

    .glass-error p:last-child {
        margin-bottom: 0;
    }
</style>

<div class="auth-wrapper">
    <div class="jungle-orb orb-1"></div>
    <div class="jungle-orb orb-2"></div>
    <div class="jungle-orb orb-3"></div>

    <div class="glass-card">
        <div class="auth-header">
            <h2>PlantVerse 🌱</h2>
            <p>Sow the seeds of your new account</p>
        </div>

        @if($errors->any())
        <div class="glass-error">
            @foreach ($errors->all() as $error)
            <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="glass-input" placeholder="Botanist Bob" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="glass-input" placeholder="planter@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="glass-input" placeholder="Min. 8 characters" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="glass-input" placeholder="Repeat password" required>
            </div>

            <button type="submit" class="btn-glow">Plant My Account</button>
        </form>

        <div class="auth-footer">
            Already in the ecosystem? <a href="{{ route('login') }}">Log in here</a>
        </div>
    </div>
</div>
@endsection