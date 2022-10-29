<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Auth;

use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Phpsa\FilamentPasswordReveal\Password as PasswordInput;

class ResetPassword extends Component implements HasForms
{
    use InteractsWithForms;
    use PasswordValidationRules;

    public $email;

    public $token;

    public $password;

    public $password_confirmation;

    public bool $isResetting = false;

    public bool $hasBeenSent = false;

    public function mount($token = null): void
    {
        if (! is_null($token)) {
            // Verify that the token is valid before moving further.
            $this->email = request()->query('email', '');
            $this->token = $token;
            $this->isResetting = true;
        }
    }

    protected function getFormSchema(): array
    {
        if ($this->isResetting) {
            return [
                PasswordInput::make('password')
                    ->label(__('filament-jet::jet.fields.password'))
                    ->revealable()
                    ->generatable()
                    ->copyable()
                    ->required()
                    ->rules($this->passwordRules()),
                PasswordInput::make('password_confirmation')
                    ->label(__('filament-jet::jet.fields.password_confirm'))
                    ->revealable()
                    ->required()
                    ->same('password'),
            ];
        } else {
            return [
                TextInput::make('email')
                    ->label(__('filament-jet::jet.fields.email'))
                    ->required()
                    ->email()
                    ->exists(table: FilamentJet::userModel()),
            ];
        }
    }

    public function submit()
    {
        $data = $this->form->getState();

        if ($this->isResetting) {
            $response = Password::broker(config('filament-jet.reset_broker', config('auth.defaults.passwords')))->reset([
                'token' => $this->token,
                'email' => $this->email,
                'password' => $data['password'],
            ], function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            });

            if ($response == Password::PASSWORD_RESET) {
                return redirect(route('filament.auth.login', ['email' => $this->email, 'reset' => true]));
            } else {
                Notification::make()
                    ->title(__('filament-jet::reset-password.notification_error'))
                    ->persistent()
                    ->actions([
                        NotificationAction::make('resetAgain')
                            ->label(__('filament-jet::reset-password.notification_error_link_text'))
                            ->url(route(config('filament-jet.route_group_prefix').'password.request')),
                    ])
                    ->danger()
                    ->send();
            }
        } else {
            $response = Password::broker(config('filament-jet.reset_broker', config('auth.defaults.passwords')))
                ->sendResetLink(['email' => $this->email]);

            if ($response == Password::RESET_LINK_SENT) {
                Notification::make()
                    ->title(__('filament-jet::reset-password.notification_success'))
                    ->success()
                    ->send();

                $this->hasBeenSent = true;
            } else {
                Notification::make()
                    ->title(match ($response) {
                        'passwords.throttled' => __('passwords.throttled'),
                        'passwords.user' => __('passwords.user')
                    })
                    ->danger()
                    ->send();
            }
        }
    }

    public function backToLoginForm()
    {
        return redirect()->to(route('filament.auth.login'));
    }

    public function render(): View
    {
        $view = view('filament-jet::livewire.auth.reset-password');

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-jet::reset-password.title'),
        ]);

        return $view;
    }
}
