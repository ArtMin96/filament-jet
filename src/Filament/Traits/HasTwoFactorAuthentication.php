<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use ArtMin96\FilamentJet\Actions\ConfirmTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\EnableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Actions\GenerateNewRecoveryCodes;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Actions\AlwaysAskPasswordButtonAction;
use ArtMin96\FilamentJet\Filament\Actions\PasswordButtonAction;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;

trait HasTwoFactorAuthentication
{
    /**
     * Indicates if two factor authentication QR code is being displayed.
     *
     * @var bool
     */
    public $showingQrCode = false;

    /**
     * Indicates if the two factor authentication confirmation input and button are being displayed.
     *
     * @var bool
     */
    public $showingConfirmation = false;

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming two factor authentication.
     *
     * @var string|null
     */
    public $two_factor_code;

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

    protected function twoFactorFormSchema(): array
    {
        return [
            TextInput::make('two_factor_code')
                ->label(__('filament-jet::account.2fa.columns.2fa_code'))
                ->disableLabel()
                ->placeholder(__('filament-jet::account.2fa.columns.2fa_code'))
                ->rules('nullable|string'),
        ];
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param  EnableTwoFactorAuthentication  $enable
     * @return void
     */
    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable)
    {
        $enable($this->user);

        $this->showingQrCode = true;

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm')) {
            $this->showingConfirmation = true;
        } else {
            $this->showingRecoveryCodes = true;
        }
    }

    /**
     * Confirm two factor authentication for the user.
     *
     * @param  ConfirmTwoFactorAuthentication  $confirm
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm)
    {
        $confirm($this->user, $this->two_factor_code);

        $this->notify('success', __('filament-jet::account.2fa.confirmation.success_notification'));

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;

        $this->two_factor_code = null;
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @param  \ArtMin96\FilamentJet\Actions\GenerateNewRecoveryCodes  $generate
     * @return void
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate)
    {
        $generate($this->user);

        $this->showingRecoveryCodes = true;
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication  $disable
     * @return void
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable)
    {
        $disable($this->user);

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;

        $this->notify('warning', __('filament-jet::account.2fa.notify.disabled'));
    }

    public function exportPersonalData()
    {
        $this->export();
    }

    /**
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes()
    {
        $this->showingRecoveryCodes = true;
    }

    /**
     * Hide the user's recovery codes.
     *
     * @return void
     */
    public function hideRecoveryCodes()
    {
        $this->showingRecoveryCodes = false;
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty()
    {
        return ! empty($this->user->two_factor_secret);
    }
}
