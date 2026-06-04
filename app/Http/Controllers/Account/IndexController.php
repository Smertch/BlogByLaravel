<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $posts = $user->posts()->latest()->get();

        return view('account.index', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
