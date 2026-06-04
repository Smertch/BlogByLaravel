@extends('layouts.auth')
@section('content')
    <div id="loginForm" class="form-wrapper">
        <div class="form-title">
            <i class="fas fa-leaf"></i> Welcome back!
        </div>
        <form action="{{ route('login.store') }}" method="post">
            @csrf

            <div class="mb-4 input-icon-wrapper">
                <i class="fas fa-envelope"></i>
                <input
                    type="email"
                    class="form-control"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            <div class="mb-4 input-icon-wrapper input-password-wrapper">
                <i class="fas fa-lock"></i>
                <input
                    id="login-password"
                    type="password"
                    class="form-control"
                    name="password"
                    placeholder="Password"
                    autocomplete="current-password"
                    required
                >
                <button
                    type="button"
                    class="password-toggle"
                    data-target="login-password"
                    aria-label="Show password"
                    aria-controls="login-password"
                    title="Show password"
                ><i class="fas fa-eye" aria-hidden="true"></i></button>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-submit">
                Log in <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <!-- Social login icons -->
        <div class="social-login text-center">
            <p>or continue with</p>
            <div class="social-icons">
                <a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="extra-links d-flex justify-content-between mt-4">
            <a href="{{ route('password.request') }}">Forgot password?</a>
            <a href="{{ route('register') }}">No account?</a>
        </div>
    </div>
@endsection
