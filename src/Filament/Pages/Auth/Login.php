<?php

namespace ArtMin96\FilamentJet\Filament\Pages\Auth;

use ArtMin96\FilamentJet\Actions\Auth\AttemptToAuthenticate;
use ArtMin96\FilamentJet\Actions\Auth\PrepareAuthenticatedSession;
use ArtMin96\FilamentJet\Actions\Auth\RedirectIfTwoFactorAuthenticatable;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Pages\CardPage;
use ArtMin96\FilamentJet\FilamentJet;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Redirector;
use Phpsa\FilamentPasswordReveal\Password;

class Login extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament-jet::filament.pages.auth.login';

    public ?string $email = null;

    public ?string $password = null;

    public bool $remember = false;

    public null|Model|Authenticatable $user = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    protected function getCardWidth(): string
    {
        return Features::getOption(Features::login(), 'card_width');
    }

    protected function hasBrand(): bool
    {
        return Features::optionEnabled(Features::login(), 'has_brand');
    }

    public function authenticate(): null|LoginResponse|Redirector
    {
        $rateLimitingOptionEnabled = Features::getOption(Features::login(), 'rate_limiting.enabled');

        if ($rateLimitingOptionEnabled) {
            try {
                $this->rateLimit(Features::getOption(Features::login(), 'rate_limiting.limit'));
            } catch (TooManyRequestsException $exception) {
                Notification::make()
                    ->title(__('filament-jet::auth/login.messages.throttled', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]))
                    ->danger()
                    ->send();

                return null;
            }
        }

        $data = $this->form->getState();

        return $this->loginPipeline($data)->then(function ($data) {
            return app(LoginResponse::class);
        });
    }

    protected function loginPipeline(array $data): Pipeline
    {
        if (FilamentJet::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($data)->through(array_filter(
                call_user_func(FilamentJet::$authenticateThroughCallback, $data)
            ));
        }

        $providedLoginPipeline = Features::getOption(Features::login(), 'pipelines');

        if (is_array($providedLoginPipeline)) {
            return (new Pipeline(app()))->send($data)->through(array_filter(
                $providedLoginPipeline
            ));
        }

        return (new Pipeline(app()))->send($data)->through(array_filter([
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('filament-jet::auth/login.fields.email.label'))
                ->email()
                ->required()
                ->autocomplete(),
            Password::make('password')
                ->label(__('filament-jet::auth/login.fields.password.label'))
                ->required()
                ->revealable()
                ->hint(
                    Features::hasResetPasswordFeature()
                        ? new HtmlString(
                            Blade::render('<x-filament::link :href="jetRouteActions()->getRequestPasswordResetRoute()"> {{ __(\'filament-jet::auth/login.buttons.request_password_reset.label\') }}</x-filament::link>')
                        )
                        : null
                ),
            Checkbox::make('remember')
                ->label(__('filament-jet::auth/login.fields.remember.label')),
        ];
    }
}
