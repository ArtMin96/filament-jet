<div>
    <x-slot name="subheading" x-data>
        <x-filament::link :href="jetRouteActions()->loginRoute()">
            <span class="rtl:hidden">&larr;</span>
            <span class="hidden rtl:inline">&rarr;</span>

            <span>
                    {{ __('filament-jet::auth/password-reset/request-password-reset.buttons.login.label') }}
                </span>
        </x-filament::link>
    </x-slot>

    <form wire:submit.prevent="authenticate"
          x-data="{ usingRecoveryCode: @entangle('usingRecoveryCode') }"
          class="space-y-8">

        {{ $this->form }}

        <x-filament::button type="submit" form="authenticate" class="w-full">
            {{ __('filament-jet::auth/login.buttons.authenticate.label') }}
        </x-filament::button>
    </form>
</div>
