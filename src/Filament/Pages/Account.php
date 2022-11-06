<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Actions\DisableTwoFactorAuthentication;
use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Traits\CanDeleteAccount;
use ArtMin96\FilamentJet\Filament\Traits\CanLogoutOtherBrowserSessions;
use ArtMin96\FilamentJet\Filament\Traits\HasCachedAction;
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
use Phpsa\FilamentPasswordReveal\Password;

class Account extends Page
{
    use PasswordValidationRules;
    use HasTwoFactorAuthentication;
    use CanLogoutOtherBrowserSessions;
    use CanDeleteAccount;
    use HasCachedAction;
    use HasUserProperty;
    use ProcessesExport;

    protected string $loginColumn;

    public array $updateProfileInformationState = [];

    public $current_password;

    public $password;

    public $password_confirmation;

    protected static string $view = 'filament-jet::filament.pages.account';

    public function boot()
    {
        $this->loginColumn = FilamentJet::username();
    }

    public function mount()
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

    /**
     * @return array
     *
     * @throws \Exception
     */
    protected function updateProfileFormSchema(): array
    {
        $profilePhotoField = [];

        if (Features::managesProfilePhotos()) {
            $profilePhotoField[] = FileUpload::make('profile_photo_path')
                ->image()
                ->avatar()
                ->disk($this->user->profilePhotoDisk())
                ->directory($this->user->profilePhotoDirectory())
                ->visible(Features::managesProfilePhotos())
                ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024']);
        }

        return array_merge(
            $profilePhotoField,
            [
                TextInput::make('name')
                    ->label(__('filament-jet::account.profile_information.columns.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make($this->loginColumn)
                    ->label(__('filament-jet::account.profile_information.columns.email'))
                    ->hintAction(
                        ! empty(config('filament-jet.profile.login_field.hint_action')) && Features::enabled(Features::emailVerification())
                            ? Action::make('newEmailVerifyNote')
                                ->tooltip(config('filament-jet.profile.login_field.hint_action.tooltip'))
                                ->icon(config('filament-jet.profile.login_field.hint_action.icon'))
                            : null
                    )
                    ->email(fn (): bool => $this->loginColumn === 'email')
                    ->unique(
                        table: FilamentJet::userModel(),
                        column: $this->loginColumn,
                        ignorable: $this->user
                    )
                    ->required()
                    ->maxLength(255),
            ]
        );
    }

    protected function updatePasswordFormSchema(): array
    {
        $currentPasswordField = [];

        $requireCurrentPasswordOnUpdate = config('filament-jet.profile.require_current_password_on_change_password');

        if ($requireCurrentPasswordOnUpdate) {
            $currentPasswordField[] = Password::make('current_password')
                ->label(__('filament-jet::account.update_password.columns.current_password'))
                ->autocomplete('current_password')
                ->revealable()
                ->required()
                ->rule('current_password');
        }

        return array_merge(
            $currentPasswordField,
            [
                Password::make('password')
                    ->label(__('filament-jet::account.update_password.columns.new_password'))
                    ->autocomplete('new_password')
                    ->copyable()
                    ->revealable()
                    ->generatable()
                    ->rules($this->passwordRules()),
                Password::make('password_confirmation')
                    ->label(__('filament-jet::account.update_password.columns.confirm_password'))
                    ->autocomplete('password_confirmation')
                    ->revealable(),
            ]
        );
    }

    /**
     * Update the user's profile information.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
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
     *
     * @return void
     */
    public function updatePassword(UpdatesUserPasswords $updater)
    {
        $state = $this->updatePasswordForm->getState();

        $updater->update($this->user, $state);

        $this->notify('success', __('filament-jet::account.update_password.changed'));

        session()->forget('password_hash_'.config('filament.auth.guard'));

        Filament::auth()->login($this->user);

        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function downloadPersonalData()
    {
        $path = glob(Storage::disk(config('personal-data-export.disk'))->path('')."{$this->user->id}_*.zip");

        $this->exportProgress = 0;
        $this->exportBatch = null;

        return response()->download(end($path))->deleteFileAfterSend();
    }
}
