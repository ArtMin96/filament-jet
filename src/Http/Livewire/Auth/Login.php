<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Http\Livewire\Auth\Login as FilamentLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class Login extends FilamentLogin
{
    public $loginColumn;

    public $showCodeForm = false;

    public $usingRecoveryCode = false;

    public $code;

    public $user;

    public function boot(): void
    {
        // user column
        $this->loginColumn = FilamentJet::username();
    }

    public function mount(): void
    {
        parent::mount();

        if ($login = request()->query($this->loginColumn, "")) {
            $this->form->fill([$this->loginColumn => $login]);
        }
        if (request()->query("reset")) {
            Notification::make()->title(__("passwords.reset"))->success()->send();
        }
    }

    public function toggleRecoveryCode()
    {
        $this->resetErrorBag('code');
        $this->code = null;
        $this->usingRecoveryCode = ! $this->usingRecoveryCode;
    }

    public function hasValidCode()
    {
        if ($this->usingRecoveryCode) {
            return $this->code && collect($this->user->recoveryCodes())->first(function ($code) {
                    return hash_equals($this->code, $code) ? $code : false;
                });
        } else {
            return $this->code && app(FilamentBreezy::class)->verify(decrypt($this->user->two_factor_secret), $this->code);
        }
    }

    public function doRateLimit()
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError($this->loginColumn, __('filament::login.messages.throttled', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return null;
        }
    }

    protected function attemptAuth($data)
    {
        // ->attempt will actually log the person in, then the response sends them to the dashboard. We need to catch the auth, show the code prompt, then log them in.
        if (! Filament::auth()->attempt([
            $this->loginColumn => $data[$this->loginColumn],
            'password' => $data['password'],
        ], $data['remember'])) {
            $this->addError($this->loginColumn, __('filament::login.messages.failed'));

            return null;
        }

        return app(LoginResponse::class);
    }

    public function authenticate(): ?LoginResponse
    {
        // Form data
        $data = $this->showCodeForm ? $this->twoFactorForm->getState() : $this->form->getState();

        if (config('filament-breezy.enable_2fa')) {
            if ($this->showCodeForm) {
                // Verify the code, then attempt to log them in now.
                if (! $this->hasValidCode()) {
                    $this->addError('code', __('filament-jet::account.2fa.confirmation.invalid_code'));

                    return null;
                }
                Filament::auth()->login($this->user, $this->remember);

                return app(LoginResponse::class);
            } else {
                // Validate the user's login details in order to show them the code challenge.
                $this->doRateLimit();

                $model = Filament::auth()->getProvider()->getModel();
                $this->user = $model::where($this->loginColumn, $data[$this->loginColumn])->first();

                // If the user hasn't setup 2FA, authenticate and exit early.
                if ($this->user && ! $this->user->has_confirmed_two_factor) {
                    // THIS is where we can force 2fa...
                    return $this->attemptAuth($data);
                }

                if (! $this->user || ! Filament::auth()->getProvider()->validateCredentials($this->user, ['password' => $data['password']])) {
                    $this->addError($this->loginColumn, __('filament::login.messages.failed'));

                    return null;
                }

                $this->password = null;
                $this->showCodeForm = true;

                return null;
            }
        } else {
            $this->doRateLimit();

            return $this->attemptAuth($data);
        }
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            "twoFactorForm" => $this->makeForm()->schema(
                $this->getTwoFactorFormSchema()
            ),
        ]);
    }

    protected function getTwoFactorFormSchema(): array
    {
        return [
            TextInput::make('code')
                ->label($this->usingRecoveryCode ? __('filament-jet::jet.fields.2fa_recovery_code') : __('filament-jet::jet.fields.2fa_code'))
                ->placeholder($this->usingRecoveryCode ? __('filament-jet::login.two_factor.recovery_code_placeholder') : __('filament-jet::login.two_factor.code_placeholder'))->required(),
        ];
    }

    protected function getFormSchema(): array
    {
        $parentSchema = parent::getFormSchema();
        if ($this->loginColumn !== 'email') {
            // Pop off the email field and replace it with loginColumn
            unset($parentSchema[0]);
            $parentSchema = Arr::prepend(
                $parentSchema,
                TextInput::make($this->loginColumn)
                    ->label(__('filament-jet::jet.fields.login'))
                    ->required()
                    ->autocomplete()
            );
        }

        return $parentSchema;
    }

    public function render(): View
    {
        $view = view($this->showCodeForm ? "filament-jet::livewire.auth.two-factor" : "filament-jet::livewire.auth.login");

        $view->layout("filament::components.layouts.base", [
            "title" => __("filament::login.title"),
        ]);

        return $view;
    }
}
