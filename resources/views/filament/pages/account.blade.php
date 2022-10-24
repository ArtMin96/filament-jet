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
</x-filament::page>
