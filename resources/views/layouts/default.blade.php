<!doctype html>
<html lang="{{ strtolower($siteLanguage->value ?? 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --primary-green: #2c6e2f;
            --primary-light: #e9f5e9;
            --border-green: #c2dbc2;
        }
        * { font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif; }
        body { background: linear-gradient(135deg, #f0f7f0 0%, #e8f3e8 100%); }
        .navbar-custom { background: #fff; border-bottom: 1px solid var(--border-green); box-shadow: 0 2px 12px rgba(0,0,0,.04); padding: 12px 0; }
        .logo-icon { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; background: var(--primary-light); color: var(--primary-green); }
        .btn-primary-green { background: var(--primary-green); border: 0; border-radius: 40px; padding: 8px 22px; font-weight: 600; color: #fff; }
        .btn-primary-green:hover { background: #236b26; color: #fff; }
        .btn-outline-green { border: 1.5px solid var(--primary-green); color: var(--primary-green); background: #fff; border-radius: 40px; padding: 6px 18px; font-weight: 500; }
        .btn-outline-green:hover { background: var(--primary-green); color: #fff; }
        .btn-logout { background: transparent; border: 1px solid #d6d6d6; border-radius: 40px; padding: 6px 16px; font-weight: 500; color: #555; }
        .post-card { background: #fff; border-radius: 28px; border: 1px solid rgba(70,120,70,.12); box-shadow: 0 8px 24px rgba(0,20,0,.04); overflow: hidden; }
        .post-header { border-bottom: 1px solid #edf3ed; padding: 1.2rem 1.5rem; background: #fefef8; }
        .post-body { padding: 1.5rem; }
        .post-body p { white-space: pre-wrap; line-height: 1.5; color: #2f3e2f; }
        .reaction-btn { background: #f8faf8; border: 1px solid #e2efe2; border-radius: 60px; padding: 8px 20px; color: #446d46; font-weight: 500; }
        .reaction-btn:hover { background: var(--primary-light); border-color: var(--primary-green); color: var(--primary-green); }
        .reaction-btn.active { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
        .comment-item { background: #fbfefb; border-radius: 20px; padding: 14px 18px; margin-bottom: 12px; border: 1px solid #e3f0e3; }
        .comment-avatar { width: 36px; height: 36px; background: #e0f0e0; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: var(--primary-green); font-weight: 700; flex-shrink: 0; }
        .form-control-custom { border-radius: 24px; border: 1px solid #cddfcd; padding: 12px 18px; }
        .badge-date { background: #f0f5f0; color: #517d54; font-size: .75rem; padding: 5px 12px; border-radius: 30px; }
        .empty-comments { background: #fafdfa; border-radius: 24px; padding: 20px; text-align: center; color: #789a78; }
        .modal-content-green { border-radius: 28px; border-top: 4px solid var(--primary-green); }
        .empty-feed { background: #fff; border-radius: 28px; border: 1px dashed var(--border-green); padding: 3rem 1.5rem; text-align: center; }
        @media (max-width: 768px) {
            .post-header, .post-body { padding: 1rem; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}" style="color: #1c4d1c;">
            <span class="logo-icon"><i class="bi bi-journal-bookmark-fill fs-5"></i></span>
            <span>{{ $ui('nav.brand') }}</span>
        </a>
        <div class="d-flex gap-3 align-items-center flex-wrap justify-content-end">
            @auth
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $siteLanguage->value }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($siteLanguages as $language)
                            <li>
                                <form method="POST" action="{{ route('locale.switch') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="language" value="{{ $language->value }}">
                                    <input type="hidden" name="redirect" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="dropdown-item {{ $siteLanguage === $language ? 'active' : '' }}">
                                        {{ $language->value }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <span class="d-none d-md-block text-secondary small">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->email }}
                </span>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-outline-green">
                        <i class="bi bi-speedometer2 me-1"></i>{{ $ui('nav.admin') }}
                    </a>
                @endif
                <a href="{{ route('index') }}" class="btn btn-outline-green">
                    <i class="bi bi-person-badge me-1"></i>{{ $ui('nav.account') }}
                </a>
                <button class="btn btn-primary-green" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="bi bi-pencil-square me-1"></i>{{ $ui('nav.new_post') }}
                </button>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-logout">{{ $ui('nav.logout') }}</button>
                </form>
            @else
                <a class="btn btn-outline-green" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary-green" href="{{ route('register') }}">Registration</a>
            @endauth
        </div>
    </div>
</nav>

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
