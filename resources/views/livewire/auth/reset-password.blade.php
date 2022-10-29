<x-filament-jet::auth-card action="submit">

    <div class="w-full grid grid-cols-3 gap-4 items-center">
        <x-filament::icon-button color="secondary" icon="heroicon-o-chevron-left" wire:click.prevent="backToLoginForm"></x-filament::icon-button>
        <div class="flex justify-center">
            <x-filament::brand />
        </div>
        <div></div>
    </div>

    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ __('filament-jet::reset-password.heading') }}
        </h2>

        <p class="mt-2 text-sm text-center">
            {{ __('filament-jet::reset-password.or') }}

            <a class="text-primary-600" href="{{ route('filament.auth.login') }}">
                {{ strtolower(__('filament::login.heading')) }}
            </a>
        </p>
    </div>

    @unless($hasBeenSent)
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full">
            {{ __('filament-jet::reset-password.submit.label') }}
        </x-filament::button>
    @else
        <span class="block text-center text-success-600 font-semibold">{{ __('filament-jet::reset-password.notification_success') }}</span>
    @endunless
</x-filament-jet::auth-card>
