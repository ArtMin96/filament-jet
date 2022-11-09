<?php

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController;
use ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->name(config('filament-jet.route_group_prefix'))
    ->prefix(config('filament.path'))
    ->group(function () {
        $guard = config('filament.auth.guard');
        $authMiddleware = config('filament-jet.auth_middleware', 'auth');

        if (Features::enabled(Features::registration())) {
            Route::middleware(['guest:'.$guard])->group(function () {
                Route::get('/register', FilamentJet::registrationComponent())->name('register');
            });

            if (FilamentJet::hasTermsAndPrivacyPolicyFeature()) {
                Route::get('/terms-of-service', FilamentJet::termsOfServiceComponent())->name('terms');
                Route::get('/privacy-policy', FilamentJet::privacyPolicyComponent())->name('policy');
            }
        }

        // Password Reset...
        if (Features::enabled(Features::resetPasswords())) {
            Route::middleware(['guest:'.$guard])->group(function () {
                Route::get('/password/reset', FilamentJet::resetPasswordsComponent())->name('password.request');
                Route::get('/password/reset/{token}', FilamentJet::resetPasswordsComponent())->name('password.reset');
            });
        }

        // Teams...
        if (Features::hasTeamFeatures()) {
            Route::middleware([
                ...[$authMiddleware.':'.$guard],
                ...Features::getOption(Features::teams(), 'middleware') ?? [],
            ])->group(function () {
                Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                    ->middleware(['signed'])
                    ->name('team-invitations.accept');
            });
        }

        // Email verification...
        if (Features::enabled(Features::emailVerification())) {
            $verificationLimiter = config('filament-jet.limiters.verification', '6,1');

            Route::get('/email/verify', FilamentJet::emailVerificationComponent())
                ->middleware(['throttle:'.$verificationLimiter])
                ->name('verification.notice');

            Route::get('email/verify/{id}/{hash}', [FilamentJet::emailVerificationController() ?? EmailVerificationController::class, '__invoke'])
                ->middleware(['signed', 'throttle:'.$verificationLimiter])
                ->name('verification.verify');
        }
    });
