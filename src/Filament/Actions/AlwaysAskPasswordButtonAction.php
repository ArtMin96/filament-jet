<?php

namespace ArtMin96\FilamentJet\Filament\Actions;

use Filament\Forms;
use Filament\Pages\Actions\ButtonAction;

class AlwaysAskPasswordButtonAction extends ButtonAction
{
    protected function setUp(): void
    {
        $this->requiresConfirmation()
            ->modalHeading(__('filament-jet::jet.password_confirmation_modal.heading'))
            ->modalSubheading(
                __('filament-jet::jet.password_confirmation_modal.description')
            )
            ->form([
                Forms\Components\TextInput::make("current_password")
                    ->label(__('filament-jet::jet.password_confirmation_modal.current_password'))
                    ->required()
                    ->password()
                    ->rule("current_password"),
            ]);
    }
}
