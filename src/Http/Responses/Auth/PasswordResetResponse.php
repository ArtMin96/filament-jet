<?php

namespace ArtMin96\FilamentJet\Http\Responses\Auth;

use Filament\Facades\Filament;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\PasswordResetResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Redirector;

class PasswordResetResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->to(Filament::getUrl());
    }
}
