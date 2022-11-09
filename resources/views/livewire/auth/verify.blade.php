<x-filament-jet::auth-card action="logout">
    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div class="space-y-8">
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ __('filament-jet::email-verify.heading') }}
        </h2>

        <div>
            {{ __('filament-jet::email-verify.before_proceeding') }}

            @unless($hasBeenSent)
                {{ __('filament-jet::email-verify.not_receive') }}

                <a class="text-primary-600" href="#" wire:click="resend">
                    {{ __('filament-jet::email-verify.request_another') }}
                </a>

            @else
                <span class="block text-success-600 font-semibold">{{ __('filament-jet::email-verify.notification_success') }}</span>
            @endunless
        </div>
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full">
        {{ __('filament-jet::email-verify.submit.label') }}
    </x-filament::button>
</x-filament-jet::auth-card>
