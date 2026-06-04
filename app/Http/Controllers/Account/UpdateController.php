<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Support\SiteUi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class UpdateController extends Controller
{
    public function __construct(
        private readonly SiteUi $siteUi
    ) {}

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error', $this->siteUi->trans('account.flash.profile_invalid'));
        }

        $user->name = $validated['name'];
        $user->save();

        return back()->with('success', $this->siteUi->trans('account.flash.profile_saved'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        try {
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->with('error', $this->siteUi->trans('account.flash.password_invalid'));
        }

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', $this->siteUi->trans('account.flash.wrong_password'));
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('success', $this->siteUi->trans('account.flash.password_changed'));
    }
}
