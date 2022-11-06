<?php

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Controllers\CurrentTeamController;
use ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config("filament.middleware.base"))
    ->name(config('filament-jet.route_group_prefix'))
    ->prefix(config('filament.path'))
    ->group(function () {
        if (Features::enabled(Features::registration()) && FilamentJet::registrationComponent()) {
            Route::get('/register', FilamentJet::registrationComponent())->name('register');

            if (FilamentJet::hasTermsAndPrivacyPolicyFeature()) {
                Route::get('/terms-of-service', FilamentJet::termsOfServiceComponent())->name('terms');
                Route::get('/privacy-policy', FilamentJet::privacyPolicyComponent())->name('policy');
            }
        }

        // Password Reset...
        if (Features::enabled(Features::resetPasswords())) {
            Route::get('/password/reset', FilamentJet::resetPasswordsComponent())->name('password.request');
            Route::get('/password/reset/{token}', FilamentJet::resetPasswordsComponent())->name('password.reset');
        }

        // Teams...
        if (Features::hasTeamFeatures()) {
            Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');

            Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                ->middleware(['signed'])
                ->name('team-invitations.accept');
        }
    });
