<x-filament-jet::auth-card action="authenticate">

    <div class="w-full grid grid-cols-3 gap-4 items-center">
        <x-filament::icon-button color="secondary" icon="heroicon-o-chevron-left" wire:click.prevent="backToLoginForm"></x-filament::icon-button>
        <div class="flex justify-center">
            <x-filament::brand />
        </div>
        <div></div>
    </div>


    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ $this->usingRecoveryCode ? __('filament-jet::login.two_factor.recovery.heading') : __('filament-jet::login.two_factor.heading') }}
        </h2>
        <p class="mt-2 text-sm text-center">
            {{ $this->usingRecoveryCode ? __('filament-jet::login.two_factor.recovery.description') : __('filament-jet::login.two_factor.description') }}
        </p>
    </div>

    {{ $this->twoFactorForm }}

    <x-filament::button type="submit" class="w-full">
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>

    <div class="text-center">
        {{ $this->usingRecoveryCode ? '' : __('filament-jet::login.two_factor.recovery_code_text') }}
        <a x-data @click="$wire.toggleRecoveryCode()" class="text-primary-600 hover:text-primary-700" href="#">{{$this->usingRecoveryCode ? __('filament-jet::login.cancel') : __('filament-jet::login.two_factor.recovery_code_link') }}</a>
    </div>

</x-filament-jet::auth-card>
