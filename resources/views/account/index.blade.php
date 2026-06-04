@extends('layouts.default')

@use('Illuminate\Support\Str')

@section('title', $ui('account.page_title') . ' · ' . $ui('nav.brand'))

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-1" style="color: #1e401e;">
                {{ $ui('account.heading') }}
            </h1>
            <p class="text-secondary">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-pill px-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- My Posts Section --}}
        <div class="col-lg-7">
            <div class="post-card p-4 h-100">
                <h2 class="h5 fw-bold mb-3" style="color: #1b4b1b;">
                    <i class="bi bi-journal-text me-2"></i>{{ $ui('account.posts.title') }}
                </h2>

                @if ($posts->isEmpty())
                    <p class="text-muted mb-0">{{ $ui('account.posts.empty') }}</p>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach ($posts as $post)
                            @php
                                $excerpt = Str::limit(strip_tags($post->content), 180);
                            @endphp
                            <li class="border-bottom border-light py-3 {{ $loop->last ? 'border-bottom-0 pb-0' : '' }} {{ !$loop->first ? 'pt-3' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="flex-grow-1 min-w-0">
                                        <h3 class="h6 fw-bold mb-1" style="color: #1b4b1b;">{{ $post->title }}</h3>
                                        <p class="mb-2 text-secondary small">{{ $excerpt }}</p>
                                        <span class="badge-date">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $ui('account.posts.meta') }} {{ $post->created_at->format('M j, Y H:i') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('home') }}#post-{{ $post->id }}" class="btn btn-outline-green btn-sm flex-shrink-0">
                                        {{ $ui('account.posts.open_feed') }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Profile Information --}}
            <div class="post-card p-4 mb-4">
                <h2 class="h5 fw-bold mb-3" style="color: #1b4b1b;">
                    <i class="bi bi-person-lines-fill me-2"></i>{{ $ui('account.profile.title') }}
                </h2>
                <p class="small text-muted mb-3">{{ $ui('account.profile.email_note') }}</p>

                <form method="POST" action="{{ route('account.profile.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ $ui('account.field.display_name') }}</label>
                        <input type="text" name="name" class="form-control form-control-custom @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ $ui('account.field.email') }}</label>
                        <input type="email" class="form-control form-control-custom bg-light" value="{{ $user->email }}" readonly tabindex="-1">
                    </div>
                    <button type="submit" class="btn btn-primary-green">{{ $ui('account.btn.save_profile') }}</button>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="post-card p-4">
                <h2 class="h5 fw-bold mb-3" style="color: #1b4b1b;">
                    <i class="bi bi-key me-2"></i>{{ $ui('account.password.title') }}
                </h2>

                <form method="POST" action="{{ route('account.password.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ $ui('account.field.current_password') }}</label>
                        <input type="password" name="current_password" class="form-control form-control-custom @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ $ui('account.field.new_password') }}</label>
                        <input type="password" name="new_password" class="form-control form-control-custom @error('new_password') is-invalid @enderror" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ $ui('account.field.repeat_password') }}</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-custom" required>
                    </div>
                    <button type="submit" class="btn btn-primary-green">{{ $ui('account.btn.change_password') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
