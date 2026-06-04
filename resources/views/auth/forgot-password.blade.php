@extends('layouts.auth')
@section('content')
    <div id="forgotPasswordForm" class="form-wrapper">
        <x-alert></x-alert>

        <div class="form-title">
            <i class="fas fa-key"></i> Reset password
        </div>
        <p class="text-muted small mb-4">
            Enter your email and we'll send you a link to reset your password.
        </p>

        <form action="{{ route('password.email') }}" method="post">
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

            @if (session('status'))
                <div class="text-success small mt-1 mb-2">{{ session('status') }}</div>
            @endif

            <button type="submit" class="btn-submit">
                Send reset link <i class="fas fa-paper-plane ms-2"></i>
            </button>
        </form>

        <div class="extra-links d-flex justify-content-between mt-4">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </div>
@endsection
