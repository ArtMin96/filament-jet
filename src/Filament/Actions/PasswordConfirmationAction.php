<?php

namespace ArtMin96\FilamentJet\Filament\Actions;

use Filament\Forms;
use Filament\Pages\Actions\Action;

class PasswordConfirmationAction extends Action
{
    protected function isPasswordSessionValid()
    {
        return session()->has('auth.password_confirmed_at') && (time() - session('auth.password_confirmed_at', 0)) < config('filament-jet.password_confirmation_seconds');
    }

    protected function setUp(): void
    {
        if ($this->isPasswordSessionValid()) {
            // Password confirmation is still valid
            //
        } else {
            $this->requiresConfirmation()
                ->modalHeading(__('filament-jet::jet.password_confirmation_modal.heading'))
                ->modalSubheading(
                    __('filament-jet::jet.password_confirmation_modal.description')
                )
                ->form([
                    Forms\Components\TextInput::make('current_password')
                        ->label(__('filament-jet::jet.password_confirmation_modal.current_password'))
                        ->required()
                        ->password()
                        ->rule('current_password'),
                ]);
        }
    }

    public function call(array $data = [])
    {
        // If the session already has a cookie and it's still valid, we don't want to reset the time on it.
        if ($this->isPasswordSessionValid()) {
        } else {
            session(['auth.password_confirmed_at' => time()]);
        }

        parent::call($data);
    }
}
