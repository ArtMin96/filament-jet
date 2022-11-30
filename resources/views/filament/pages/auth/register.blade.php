<div>
    <x-slot name="subheading">
        {{ __('filament-jet::auth/register.buttons.login.before') }}
        <x-filament::link :href="jetRouteActions()->loginRoute()">
            {{ __('filament-jet::auth/register.buttons.login.label') }}
        </x-filament::link>
    </x-slot>

    <form wire:submit.prevent="register" class="space-y-8">
        {{ $this->form }}

        <x-filament::button type="submit" form="register" class="w-full">
            {{ __('filament-jet::auth/register.buttons.register.label') }}
        </x-filament::button>
    </form>
</div>
