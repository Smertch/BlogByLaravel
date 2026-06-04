@extends('layouts.auth')
@section('content')
    <!-- Registration Form (hidden initially) -->
    <div id="registerForm" class="form-wrapper">
        <x-alert></x-alert>

        <form action="{{ route('register.store') }}" method="post">
            @csrf

            <div class="form-title">
                <i class="fas fa-seedling"></i> Create account
            </div>
            <div class="mb-4 input-icon-wrapper">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="name" placeholder="Choose username" required>
            </div>
            <div class="mb-4 input-icon-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email address" required>
            </div>
            <div class="mb-4 input-icon-wrapper input-password-wrapper">
                <i class="fas fa-lock"></i>
                <input
                    id="password"
                    type="password"
                    class="form-control"
                    name="password"
                    placeholder="Create password (min. 8 chars)"
                    autocomplete="new-password"
                    required
                >
                <button
                    type="button"
                    class="password-toggle"
                    data-target="password"
                    aria-label="Show password"
                    aria-controls="password"
                    title="Show password"
                ><i class="fas fa-eye" aria-hidden="true"></i></button>
            </div>
            <div id="password-length-error" class="text-danger small mt-1 d-none"></div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="mb-4 input-icon-wrapper input-password-wrapper">
                <i class="fas fa-lock"></i>
                <input
                    id="password_confirmation"
                    type="password"
                    class="form-control"
                    name="password_confirmation"
                    placeholder="Confirmation password"
                    autocomplete="new-password"
                    required
                >
                <button
                    type="button"
                    class="password-toggle"
                    data-target="password_confirmation"
                    aria-label="Show password"
                    aria-controls="password_confirmation"
                    title="Show password"
                ><i class="fas fa-eye" aria-hidden="true"></i></button>
            </div>
            <div id="password-match-error" class="text-danger small mt-1 d-none"></div>
            @error('password_confirmation')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            <div class="checkbox-row">
                <input type="checkbox" id="agree">
                <label for="agree">I agree to the terms and conditions</label>
            </div>

            <button type="submit" class="btn-submit">Sign up <i class="fas fa-user-plus ms-2"></i></button>
        </form>

        <div class="social-login text-center">
            <p>or sign up with</p>
            <div class="social-icons">
                <a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="extra-links d-flex justify-content-between mt-4">
            <a href="{{ route('login') }}">Already have an account?</a>
            <a href="#">Privacy policy</a>
        </div>
    </div>
@endsection
