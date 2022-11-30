<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Traits\CanDeleteAccount;
use ArtMin96\FilamentJet\Filament\Traits\CanLogoutOtherBrowserSessions;
use ArtMin96\FilamentJet\Filament\Traits\HasCachedAction;
use ArtMin96\FilamentJet\Filament\Traits\HasHiddenAction;
use ArtMin96\FilamentJet\Filament\Traits\HasTwoFactorAuthentication;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use ArtMin96\FilamentJet\Traits\ProcessesExport;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Redirector;
use Phpsa\FilamentPasswordReveal\Password;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Account extends Page
{
    use PasswordValidationRules;
    use HasHiddenAction;
    use HasCachedAction;
    use HasUserProperty;
    use HasTwoFactorAuthentication;
    use CanLogoutOtherBrowserSessions;
    use CanDeleteAccount;
    use ProcessesExport;

    public array $updateProfileInformationState = [];

    public null|string $currentPassword;

    public null|string $password;

    public null|string $passwordConfirmation;

    protected static string $view = 'filament-jet::filament.pages.account';

    public function mount(): void
    {
        $this->updateProfileInformationForm->fill($this->user->withoutRelations()->toArray());

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm') &&
            is_null($this->user->two_factor_confirmed_at)) {
            app(DisableTwoFactorAuthentication::class)($this->user);
        }
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.account');
    }

    protected function getForms(): array
    {
        return [
            'updateProfileInformationForm' => $this->makeForm()
                ->model(FilamentJet::userModel())
                ->schema($this->updateProfileFormSchema())
                ->statePath('updateProfileInformationState'),
            'updatePasswordForm' => $this->makeForm()
                ->schema($this->updatePasswordFormSchema()),
            'confirmTwoFactorForm' => $this->makeForm()
                ->schema($this->twoFactorFormSchema()),
        ];
    }

    protected function updateProfileFormSchema(): array
    {
        return array_filter([
            Features::managesProfilePhotos()
                ? FileUpload::make('profile_photo_path')
                    ->image()
                    ->avatar()
                    ->disk($this->user->profilePhotoDisk())
                    ->directory($this->user->profilePhotoDirectory())
                    ->visible(Features::managesProfilePhotos())
                    ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024'])
                : null,
            TextInput::make('name')
                ->label(__('filament-jet::account.profile_information.columns.name'))
                ->required()
                ->maxLength(255),
            TextInput::make(FilamentJet::username())
                ->label(__('filament-jet::account.profile_information.columns.email'))
                ->hintAction(
                    ! empty(config('filament-jet.profile.login_field.hint_action')) && Features::enabled(Features::emailVerification())
                        ? Action::make('newEmailVerifyNote')
                        ->tooltip(config('filament-jet.profile.login_field.hint_action.tooltip'))
                        ->icon(config('filament-jet.profile.login_field.hint_action.icon'))
                        : null
                )
                ->email(fn (): bool => FilamentJet::username() === 'email')
                ->unique(
                    table: FilamentJet::userModel(),
                    column: FilamentJet::username(),
                    ignorable: $this->user
                )
                ->required()
                ->maxLength(255),
        ]);
    }

    protected function updatePasswordFormSchema(): array
    {
        $requireCurrentPasswordOnUpdate = Features::optionEnabled(Features::updatePasswords(), 'askCurrentPassword');

        return array_filter([
            $requireCurrentPasswordOnUpdate
                ? Password::make('currentPassword')
                    ->label(__('filament-jet::account.update_password.columns.current_password'))
                    ->autocomplete('currentPassword')
                    ->revealable()
                    ->required()
                    ->rule('current_password')
                : null,
            Password::make('password')
                ->label(__('filament-jet::account.update_password.columns.new_password'))
                ->autocomplete('new_password')
                ->copyable()
                ->revealable()
                ->generatable()
                ->required()
                ->rules(FilamentJet::getPasswordRules())
                ->same('passwordConfirmation'),
            Password::make('passwordConfirmation')
                ->label(__('filament-jet::account.update_password.columns.confirm_password'))
                ->autocomplete('passwordConfirmation')
                ->revealable(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updater): Redirector
    {
        $updater->update(
            $this->user,
            $this->updateProfileInformationForm->getState()
        );

        $this->notify(
            status: 'success',
            message: __('filament-jet::account.profile_information.updated'),
            isAfterRedirect: true
        );

        return redirect()->route('filament.pages.account');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatesUserPasswords $updater): void
    {
        $state = $this->updatePasswordForm->getState();

        $updater->update($this->user, $state);

        $this->notify('success', __('filament-jet::account.update_password.changed'));

        session()->forget('password_hash_'.config('filament.auth.guard'));

        Filament::auth()->login($this->user);

        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function downloadPersonalData(): BinaryFileResponse
    {
        $path = glob(Storage::disk(config('personal-data-export.disk'))->path('')."{$this->user->id}_*.zip");

        $this->exportProgress = 0;
        $this->exportBatch = null;

        return response()->download(end($path))->deleteFileAfterSend();
    }
}
