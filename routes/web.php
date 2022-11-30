<?php

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->name(config('filament-jet.route_group_prefix'))
    ->prefix(config('filament.path'))
    ->group(function () {
        $guard = config('filament.auth.guard');
        $authMiddleware = config('filament-jet.auth_middleware', 'auth');

        Route::name('auth.')
            ->middleware(['guest:'.$guard])
            ->group(function () use ($guard) {

                // Two Factor Authentication...
                if (Features::enabled(Features::twoFactorAuthentication())) {
                    Route::get('/two-factor-login', \ArtMin96\FilamentJet\Filament\Pages\Auth\TwoFactorLogin::class)->name('two-factor.login');
                }

                // Registration...
                if (Features::hasRegistrationFeature()) {
                    Route::get('/register', FilamentJet::registrationPage())->name('register');
                }

                // Password Reset...
                if (Features::hasResetPasswordFeature()) {
                    Route::name('password-reset.')
                        ->prefix('/password-reset')
                        ->group(function () {
                            Route::get('/request', Features::getOption(Features::resetPasswords(), 'request.page'))->name('request');
                            Route::get('/reset', Features::getOption(Features::resetPasswords(), 'reset.page'))
                                ->middleware(['signed'])
                                ->name('reset');
                        });
                }
            });

        if (Features::enabled(Features::registration())) {
            if (FilamentJet::hasTermsAndPrivacyPolicyFeature()) {
                Route::get('/terms-of-service', FilamentJet::termsOfServiceComponent())->name('terms');
                Route::get('/privacy-policy', FilamentJet::privacyPolicyComponent())->name('policy');
            }
        }

        // Teams...
        if (Features::hasTeamFeatures()) {
            Route::middleware(
                Features::getOption(Features::teams(), 'middleware') ?? []
            )->group(function () {
                Route::get('/team-invitations/{invitation}', [FilamentJet::teamInvitationController(), FilamentJet::teamInvitationAcceptAction()])
                    ->middleware(['signed'])
                    ->name('team-invitations.accept');
            });
        }

        // Email verification...
        if (Features::enabled(Features::emailVerification())) {

            Route::name('auth.email-verification.')
                ->prefix('/email-verification')
                ->group(function () {
                    Route::get('/prompt', Features::getOption(Features::emailVerification(), 'page'))->name('prompt');

                    Route::get('/verify', [
                        Features::getOption(Features::emailVerification(), 'controller') ?? EmailVerificationController::class,
                        '__invoke'
                    ])
                        ->name('verify');
                });
        }
    });
