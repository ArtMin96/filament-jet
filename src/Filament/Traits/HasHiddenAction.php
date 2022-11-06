<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Actions\AlwaysAskPasswordButtonAction;
use ArtMin96\FilamentJet\Filament\Actions\PasswordButtonAction;
use Filament\Pages\Actions\Action;

trait HasHiddenAction
{
    protected function getHiddenActions(): array
    {
        $actions = [];

        if (Features::canManageTwoFactorAuthentication()) {
            if (config('filament-jet.password_confirmation.enable_two_factor_authentication', true)) {
                $enable2fa = PasswordButtonAction::make('enable2fa')
                    ->label(__('filament-jet::account.2fa.actions.enable'))
                    ->icon('heroicon-s-shield-check')
                    ->action('enableTwoFactorAuthentication');
            } else {
                $enable2fa = Action::make('enable2fa')
                    ->label(__('filament-jet::account.2fa.actions.enable'))
                    ->icon('heroicon-s-shield-check')
                    ->action('enableTwoFactorAuthentication');
            }

            if (config('filament-jet.password_confirmation.disable_two_factor_authentication', true)) {
                $disable2fa = PasswordButtonAction::make('disable2fa')
                    ->label(__('filament-jet::account.2fa.actions.disable'))
                    ->color('danger')
                    ->action('disableTwoFactorAuthentication');
            } else {
                $disable2fa = Action::make('disable2fa')
                    ->label(__('filament-jet::account.2fa.actions.disable'))
                    ->color('danger')
                    ->action('disableTwoFactorAuthentication');
            }

            if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
                $showRecoveryCodes = PasswordButtonAction::make('showing_recovery_codes')
                    ->label(__('filament-jet::account.2fa.enabled.show_codes'))
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->visible(! $this->showingRecoveryCodes)
                    ->action('showRecoveryCodes');

                $regenerateRecoveryCodes = PasswordButtonAction::make('regenerate_recovery_codes')
                    ->label(__('filament-jet::account.2fa.actions.regenerate_codes'))
                    ->icon('heroicon-o-refresh')
                    ->action('regenerateRecoveryCodes');
            } else {
                $showRecoveryCodes = Action::make('showing_recovery_codes')
                    ->label(__('filament-jet::account.2fa.enabled.show_codes'))
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->visible(! $this->showingRecoveryCodes)
                    ->action('showRecoveryCodes');

                $regenerateRecoveryCodes = Action::make('regenerate_recovery_codes')
                    ->label(__('filament-jet::account.2fa.actions.regenerate_codes'))
                    ->icon('heroicon-o-refresh')
                    ->action('regenerateRecoveryCodes');
            }

            $actions = array_merge($actions, [
                $enable2fa,
                $disable2fa,
                $regenerateRecoveryCodes,
                $showRecoveryCodes,
                Action::make('hide_recovery_codes')
                    ->label(__('filament-jet::account.2fa.enabled.hide_codes'))
                    ->icon('heroicon-o-eye-off')
                    ->color('secondary')
                    ->visible($this->showingRecoveryCodes)
                    ->action('hideRecoveryCodes'),
            ]);
        }

        if (Features::canLogoutOtherBrowserSessions()) {
            $actions = array_merge($actions, [
                PasswordButtonAction::make('logout_other_browser_sessions')
                    ->label(__('filament-jet::account.other_browser_sessions.actions.confirm'))
                    ->icon('heroicon-o-globe-alt')
                    ->action('logoutOtherBrowserSessions'),
            ]);
        }

        if (Features::hasAccountDeletionFeatures()) {
            $actions = array_merge($actions, [
                AlwaysAskPasswordButtonAction::make('delete_account')
                    ->label(__('filament-jet::account.delete_account.actions.confirm'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action('deleteAccount'),
            ]);
        }

        if (Features::canExportPersonalData()) {
            $actions = array_merge($actions, [
                Action::make('export_personal_data')
                    ->label(__('filament-jet::account.export_personal_data.actions.confirm'))
                    ->icon('heroicon-o-download')
                    ->action('exportPersonalData'),
                Action::make('download_personal_data')
                    ->label(__('filament-jet::account.export_personal_data.actions.download'))
                    ->icon('heroicon-o-download')
                    ->action('downloadPersonalData'),
            ]);
        }

        return $actions;
    }
}
