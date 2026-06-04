<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SiteLanguage;
use App\Support\SiteUi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController extends Controller
{
    public function __invoke(Request $request, SiteUi $siteUi): RedirectResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'string'],
            'redirect' => ['nullable', 'string'],
        ]);

        $language = SiteLanguage::tryFrom((string) $validated['language']);
        if ($language !== null) {
            $siteUi->setLanguage($request, $language);
        }

        $redirect = (string) ($validated['redirect'] ?? route('home'));

        return redirect()->to(str_starts_with($redirect, '/') ? $redirect : route('home'));
    }
}
