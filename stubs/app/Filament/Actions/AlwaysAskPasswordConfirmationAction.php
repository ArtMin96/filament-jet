<?php

namespace ArtMin96\FilamentJet\Filament\Actions;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

class AlwaysAskPasswordConfirmationAction extends ButtonAction
{
    protected function setUp(): void
    {
        $this->requiresConfirmation()
            ->modalHeading(__('filament-jet::account.account_page.password_confirmation_modal.heading'))
            ->modalSubheading(
                __('filament-jet::account.account_page.password_confirmation_modal.description')
            )
            ->form([
                Forms\Components\TextInput::make('current_password')
                    ->label(__('filament-jet::account.account_page.password_confirmation_modal.current_password'))
                    ->required()
                    ->password()
                    ->rule('current_password'),
            ]);
    }
}
