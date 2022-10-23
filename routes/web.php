<?php

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Http\Controllers\CurrentTeamController;
use ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

Route::domain(config("filament.domain"))
    ->middleware(
        array_merge(config("filament.middleware.base"), [
//            'auth:sanctum',
//            'verified'
        ])
    )
    ->name(config('filament-jet.route_group_prefix'))
    ->prefix(config("filament.path"))
    ->group(function () {

//        if (Features::enabled(Features::registration())) {
//            Route::get("/register", config('filament-jet.registration.component'))->name("register");
//        }

        // Teams...
        if (Features::hasTeamFeatures()) {
            Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');

            Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                ->middleware(['signed'])
                ->name('team-invitations.accept');
        }
    });
