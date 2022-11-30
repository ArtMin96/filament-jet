<?php

namespace ArtMin96\FilamentJet\Http\Responses\Auth;

use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended(Filament::getUrl());
    }
}
