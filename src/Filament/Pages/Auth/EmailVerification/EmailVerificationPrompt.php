<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth\EmailVerification;

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Notifications\Auth\VerifyEmail;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EmailVerificationPrompt extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament-jet::filament.pages.auth.email-verification.email-verification-prompt';

    public function mount()
    {
        if (! Filament::auth()->check()) {
            return redirect()->to(jetRouteActions()->loginRoute());
        }

        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(Filament::getUrl());
        }
    }

    protected function getCardWidth(): string
    {
        return Features::getOption(Features::emailVerification(), 'card_width');
    }

    protected function hasBrand(): bool
    {
        return Features::optionEnabled(Features::emailVerification(), 'has_brand');
    }

    public function resendNotification(): void
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::emailVerification(), 'rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::emailVerification(), 'rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/email-verification/email-verification-prompt.messages.notification_resend_throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return;
            }
        }

        $user = Filament::auth()->user();

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = new VerifyEmail();
        $notification->url = FilamentJet::getVerifyEmailUrl($user);

        $user->notify($notification);

        Notification::make()
            ->title(__('filament-jet::auth/email-verification/email-verification-prompt.messages.notification_resent'))
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return __('filament-jet::auth/email-verification/email-verification-prompt.title');
    }

    public function getHeading(): string
    {
        return __('filament-jet::auth/email-verification/email-verification-prompt.heading');
    }
}
