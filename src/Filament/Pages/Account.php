<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class Account extends Page
{
    protected string $loginColumn;

    public string $photo = '';

    public array $updateProfileInformationState = [];

    protected static string $view = 'filament-jet::filament.pages.account';

    public function boot()
    {
        $this->loginColumn = FilamentJet::username();
    }

    public function mount()
    {
        $this->updateProfileInformationForm->fill($this->user->withoutRelations()->toArray());
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Filament::auth()->user();
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
                ->statePath('updateProfileInformationState')
        ];
    }

    /**
     * @return array
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
                        !empty(config('filament-jet.profile.login_field.hint_action'))
                            ? Action::make('newEmailVerifyNote')
                                ->tooltip(config('filament-jet.profile.login_field.hint_action.tooltip'))
                                ->icon(config('filament-jet.profile.login_field.hint_action.icon'))
                            : null
                    )
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
}
