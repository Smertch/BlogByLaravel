@extends('layouts.default')

@section('title', $ui('feed.title'))

@section('content')
    @php($currentUser = auth()->user())

    <div class="container py-4 py-md-5">
        @foreach (['success' => 'success', 'status' => 'success', 'error' => 'danger'] as $key => $type)
            @if (session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    {{ session($key) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="alert alert-danger rounded-4 border-0 shadow-sm" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1 text-success">
                    <i class="bi bi-chat-left-text-fill me-2"></i>{{ $ui('feed.title') }}
                </h2>
                <p class="text-muted small mb-0">{{ $ui('feed.subtitle') }}</p>
            </div>
            <form method="get" action="{{ route('home') }}" class="mt-2 mt-sm-0">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-pill">
                        <i class="bi bi-search text-success"></i>
                    </span>
                    <input type="text"
                           name="q"
                           value="{{ $search }}"
                           class="form-control border-start-0 rounded-pill"
                           placeholder="{{ $ui('feed.search_placeholder') }}">
                </div>
            </form>
        </div>

        @forelse ($posts as $post)
            @php($canEdit = $post->user_id === $currentUser->id || $currentUser->isAdmin())
            @php($liked = $post->isLikedBy($currentUser))

            <article class="post-card mb-4" id="post-{{ $post->id }}">
                <div class="post-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $post->title }}</h4>
                        <div class="d-flex gap-2 flex-wrap mt-1">
                            <span class="badge-date">
                                <i class="bi bi-person me-1"></i>{{ $post->user?->name ?? $post->user?->email ?? 'User' }}
                            </span>
                            <span class="badge-date">
                                <i class="bi bi-clock me-1"></i>{{ $post->created_at?->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    </div>

                    @if ($canEdit)
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-green btn-sm" data-bs-toggle="modal" data-bs-target="#editPostModal{{ $post->id }}">
                                <i class="bi bi-pencil me-1"></i>{{ $ui('post.edit') }}
                            </button>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i>{{ $ui('post.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="post-body">
                    <p class="mb-4">{{ $post->content }}</p>

                    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
                        <form method="POST" action="{{ route('posts.like', $post) }}" class="m-0">
                            @csrf
                            <button type="submit" class="reaction-btn {{ $liked ? 'active' : '' }}">
                                <i class="bi {{ $liked ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                {{ $post->likes_count }} {{ $ui('post.likes') }}
                            </button>
                        </form>
                        <span class="reaction-btn">
                            <i class="bi bi-chat-dots me-1"></i>{{ $post->comments_count }} {{ $ui('post.comments') }}
                        </span>
                    </div>

                    <div class="comments">
                        @forelse ($post->comments as $comment)
                            <div class="comment-item">
                                <div class="d-flex justify-content-between gap-3">
                                    <div class="d-flex gap-3">
                                        <span class="comment-avatar">{{ strtoupper(substr($comment->user?->name ?? 'U', 0, 1)) }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $comment->user?->name ?? $comment->user?->email ?? 'User' }}</div>
                                            <div class="text-muted small">{{ $comment->created_at?->format('Y-m-d H:i') }}</div>
                                            <div class="mt-2">{{ $comment->body }}</div>
                                        </div>
                                    </div>

                                    @if ($comment->user_id === $currentUser->id || $currentUser->isAdmin())
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none">
                                                {{ $ui('comment.delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-comments small">{{ $ui('post.comments') }}: 0</div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('posts.comments.store', $post) }}" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="body" class="form-control form-control-custom" placeholder="{{ $ui('comment.placeholder') }}" required>
                            <button type="submit" class="btn btn-primary-green">{{ $ui('comment.add') }}</button>
                        </div>
                    </form>
                </div>
            </article>

            @if ($canEdit)
                <div class="modal fade" id="editPostModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-content-green">
                            <form method="POST" action="{{ route('posts.update', $post) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">{{ $ui('post.edit') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $ui('post.title') }}</label>
                                        <input type="text" name="title" value="{{ $post->title }}" class="form-control form-control-custom" required maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ $ui('post.content') }}</label>
                                        <textarea name="content" rows="6" class="form-control form-control-custom">{{ $post->content }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-primary-green">{{ $ui('post.update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="empty-feed">
                <h4 class="fw-bold mb-2">{{ $search !== '' ? $ui('feed.empty_search') : $ui('feed.empty') }}</h4>
            </div>
        @endforelse
    </div>

    <div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-green">
                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title">{{ $ui('nav.new_post') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ $ui('post.title') }}</label>
                            <input type="text" name="title" class="form-control form-control-custom" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ $ui('post.content') }}</label>
                            <textarea name="content" rows="6" class="form-control form-control-custom"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary-green">{{ $ui('post.publish') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
