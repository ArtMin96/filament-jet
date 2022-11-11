<x-filament-jet::auth-card action="authenticate">

    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div>
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{ __('filament::login.heading') }}
        </h2>
        @if(\ArtMin96\FilamentJet\Features::enabled(\ArtMin96\FilamentJet\Features::registration()))
            <p class="mt-2 text-sm text-center">
                {{ __('filament-jet::registration.or') }}
                <a class="text-primary-600" href="{{ route(config('filament-jet.route_group_prefix').'register') }}">
                    {{ strtolower(__('filament-jet::registration.heading')) }}
                </a>
            </p>
        @endif
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full">
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>

    @if(\ArtMin96\FilamentJet\Features::enabled(\ArtMin96\FilamentJet\Features::resetPasswords()))
        <div class="text-center">
            <a class="text-primary-600 hover:text-primary-700" href="{{ route(config('filament-jet.route_group_prefix').'password.request') }}">
                {{ __('filament-jet::login.forgot_password_link') }}
            </a>
        </div>
    @endif

</x-filament-jet::auth-card>
