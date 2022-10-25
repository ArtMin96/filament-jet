<x-filament::page>
    @if(\ArtMin96\FilamentJet\Features::canUpdateProfileInformation())
        <x-filament-jet-form-section submit="updateProfileInformation">
            <x-slot name="title">
                {{ __('filament-jet::account.profile_information.title') }}
            </x-slot>

            <x-slot name="description">
                {{ __('filament-jet::account.profile_information.description') }}
            </x-slot>

            <x-slot name="form">
                {{ $this->updateProfileInformationForm }}
            </x-slot>

            <x-slot name="actions">
                <x-filament::button type="submit" icon="heroicon-o-identification">
                    {{ __('filament-jet::account.profile_information.submit') }}
                </x-filament::button>
            </x-slot>
        </x-filament-jet-form-section>
    @endif

    @if(\ArtMin96\FilamentJet\Features::enabled(\ArtMin96\FilamentJet\Features::updatePasswords()))
        <x-filament::hr />

        <x-filament-jet-form-section submit="updatePassword">
            <x-slot name="title">
                {{ __('filament-jet::account.update_password.title') }}
            </x-slot>

            <x-slot name="description">
                {{ __('filament-jet::account.update_password.description') }}
            </x-slot>

            <x-slot name="form">
                {{ $this->updatePasswordForm }}
            </x-slot>

            <x-slot name="actions">
                <x-filament::button type="submit" icon="heroicon-o-lock-closed">
                    {{ __('filament-jet::account.update_password.submit') }}
                </x-filament::button>
            </x-slot>
        </x-filament-jet-form-section>
    @endif
</x-filament::page>
