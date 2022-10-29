<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Contracts\DeletesUsers;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait CanDeleteAccount
{
    /**
     * Delete the current user.
     *
     * @param Request      $request
     * @param DeletesUsers $deleter
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request, DeletesUsers $deleter): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $deleter->delete(Auth::user()->fresh());

        Filament::auth()->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect(config('filament.path'));
    }
}
