<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

final class RssFeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::query()
            ->with('user')
            ->latest()
            ->limit(50)
            ->get();

        $xml = view('rss.feed', [
            'posts' => $posts,
            'siteUrl' => rtrim((string) config('app.url'), '/'),
            'siteName' => config('app.name', 'Blog'),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    }
}
