<div>
    @if(\ArtMin96\FilamentJet\Features::hasRegistrationFeature())
        <x-slot name="subheading">
            {{ __('filament-jet::auth/login.buttons.register.before') }}
            <x-filament::link :href="jetRouteActions()->registrationRoute()">
                {{ __('filament-jet::auth/login.buttons.register.label') }}
            </x-filament::link>
        </x-slot>
    @endif

    <form wire:submit.prevent="authenticate" class="space-y-8">
        {{ $this->form }}

        <x-filament::button type="submit" form="authenticate" class="w-full">
            {{ __('filament-jet::auth/login.buttons.authenticate.label') }}
        </x-filament::button>
    </form>
</div>
