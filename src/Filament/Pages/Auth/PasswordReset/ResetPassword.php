<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset;

use ArtMin96\FilamentJet\Contracts\ResetsUserPasswords;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\PasswordResetResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
use Phpsa\FilamentPasswordReveal\Password as PasswordInput;

class ResetPassword extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament-jet::filament.pages.auth.password-reset.reset-password';

    public ?string $email = null;

    public ?string $password = '';

    public ?string $passwordConfirmation = '';

    public ?string $token = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->token = request()->query('token');

        $this->form->fill([
            'email' => request()->query('email'),
        ]);
    }

    protected function getCardWidth(): string
    {
        return Features::getOption(Features::resetPasswords(), 'reset.card_width');
    }

    protected function hasBrand(): bool
    {
        return Features::optionEnabled(Features::resetPasswords(), 'reset.has_brand');
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::resetPasswords(), 'reset.rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::resetPasswords(), 'reset.rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/password-reset/reset-password.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = $this->broker()->reset(
            $data,
            function (CanResetPassword|Model|Authenticatable $user) use ($data) {
                app(ResetsUserPasswords::class)->reset($user, $data);
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__($status))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        Notification::make()
            ->title(__($status))
            ->danger()
            ->send();

        return null;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.email.label'))
                ->disabled(),
            PasswordInput::make('password')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.password.label'))
                ->required()
                ->same('passwordConfirmation')
                ->revealable()
                ->copyable()
                ->generatable()
                ->rules(FilamentJet::getPasswordRules()),
            PasswordInput::make('passwordConfirmation')
                ->label(__('filament-jet::auth/password-reset/reset-password.fields.passwordConfirmation.label'))
                ->required()
                ->revealable(),
        ];
    }

    /**
     * @param  string  $propertyName
     */
    public function propertyIsPublicAndNotDefinedOnBaseClass($propertyName): bool
    {
        if ((! app()->runningUnitTests()) && in_array($propertyName, [
            'email',
            'token',
        ])) {
            return false;
        }

        return parent::propertyIsPublicAndNotDefinedOnBaseClass($propertyName);
    }

    /**
     * Get the broker to be used during password reset.
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker(config('filament-jet.passwords'));
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        return [
            'password.same' => __('validation.confirmed', ['attribute' => __('filament-jet::auth/password-reset/reset-password.fields.password.validation_attribute')]),
        ];
    }

    public function getTitle(): string
    {
        return __('filament-jet::auth/password-reset/reset-password.title');
    }

    public function getHeading(): string
    {
        return __('filament-jet::auth/password-reset/reset-password.heading');
    }
}
