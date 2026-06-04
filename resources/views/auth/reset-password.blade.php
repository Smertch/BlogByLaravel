@extends('layouts.auth')
@section('content')
    <div id="resetPasswordForm" class="form-wrapper">
        <x-alert></x-alert>

        <div class="form-title">
            <i class="fas fa-lock"></i> Set new password
        </div>
        <p class="text-muted small mb-4">
            Enter your new password below.
        </p>

        <form action="{{ route('password.update') }}" method="post">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <div class="mb-4 input-icon-wrapper input-password-wrapper">
                <i class="fas fa-lock"></i>
                <input
                    id="reset-password"
                    type="password"
                    class="form-control"
                    name="password"
                    placeholder="New password (min. 8 chars)"
                    autocomplete="new-password"
                    required
                >
                <button
                    type="button"
                    class="password-toggle"
                    data-target="reset-password"
                    aria-label="Show password"
                    aria-controls="reset-password"
                    title="Show password"
                ><i class="fas fa-eye" aria-hidden="true"></i></button>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            <div class="mb-4 input-icon-wrapper input-password-wrapper">
                <i class="fas fa-lock"></i>
                <input
                    id="reset-password_confirmation"
                    type="password"
                    class="form-control"
                    name="password_confirmation"
                    placeholder="Confirm new password"
                    autocomplete="new-password"
                    required
                >
                <button
                    type="button"
                    class="password-toggle"
                    data-target="reset-password_confirmation"
                    aria-label="Show password"
                    aria-controls="reset-password_confirmation"
                    title="Show password"
                ><i class="fas fa-eye" aria-hidden="true"></i></button>
            </div>
            @error('password_confirmation')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-submit">
                Reset password <i class="fas fa-check ms-2"></i>
            </button>
        </form>

        <div class="extra-links d-flex justify-content-between mt-4">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </div>
@endsection
