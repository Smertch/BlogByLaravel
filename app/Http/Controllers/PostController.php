<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use App\Support\SiteUi;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PostController extends Controller
{
    private const FEED_LIMIT = 50;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $posts = Post::query()
            ->with(['user', 'comments.user', 'likes'])
            ->withCount(['comments', 'likes'])
            ->when($search !== '', function ($query) use ($search): void {
                $query
                    ->where(function ($inner) use ($search): void {
                        $inner
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('content', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search): void {
                                $userQuery
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            })
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    public function store(Request $request, SiteUi $siteUi): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ], [
            'title.required' => $siteUi->trans('flash.title_empty', language: $siteUi->language($request)),
        ]);

        $post = Post::query()->create([
            'title' => trim((string) $validated['title']),
            'content' => trim((string) ($validated['content'] ?? '')),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->to(route('home').'#post-'.$post->id)
            ->with('success', $siteUi->trans('flash.post_published', language: $siteUi->language($request)));
    }

    public function update(Request $request, Post $post, SiteUi $siteUi): RedirectResponse
    {
        $this->denyUnlessOwnerOrAdmin($request->user(), $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ], [
            'title.required' => $siteUi->trans('flash.title_empty', language: $siteUi->language($request)),
        ]);

        $post->update([
            'title' => trim((string) $validated['title']),
            'content' => trim((string) ($validated['content'] ?? '')),
        ]);

        return redirect()
            ->to(route('home').'#post-'.$post->id)
            ->with('success', $siteUi->trans('flash.post_updated', language: $siteUi->language($request)));
    }

    public function destroy(Request $request, Post $post, SiteUi $siteUi): RedirectResponse
    {
        $this->denyUnlessOwnerOrAdmin($request->user(), $post);

        $post->delete();

        return redirect()
            ->route('home')
            ->with('success', $siteUi->trans('flash.post_removed', language: $siteUi->language($request)));
    }

    public function toggleLike(Request $request, Post $post): RedirectResponse
    {
        $attributes = [
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
        ];

        $existing = PostLike::query()->where($attributes)->first();
        if ($existing !== null) {
            $existing->delete();

            return redirect()->to(route('home').'#post-'.$post->id);
        }

        try {
            PostLike::query()->create($attributes);
        } catch (QueryException) {
        }

        return redirect()->to(route('home').'#post-'.$post->id);
    }

    public function addComment(Request $request, Post $post, SiteUi $siteUi): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ], [
            'body.required' => $siteUi->trans('flash.comment_empty', language: $siteUi->language($request)),
        ]);

        Comment::query()->create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => trim((string) $validated['body']),
        ]);

        return redirect()->to(route('home').'#post-'.$post->id);
    }

    public function deleteComment(Request $request, Comment $comment, SiteUi $siteUi): RedirectResponse
    {
        $user = $request->user();
        abort_unless($comment->user_id === $user->id || $user->isAdmin(), 403);

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()
            ->to(route('home').'#post-'.$postId)
            ->with('success', $siteUi->trans('flash.comment_removed', language: $siteUi->language($request)));
    }

    private function denyUnlessOwnerOrAdmin(User $user, Post $post): void
    {
        abort_unless($post->user_id === $user->id || $user->isAdmin(), 403);
    }
}
