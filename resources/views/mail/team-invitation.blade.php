@component('mail::message')
    {{ __('filament-jet::teams/invitation-mail.title', ['team' => $invitation->team->name]) }}

    @if (ArtMin96\FilamentJet\Features::enabled(ArtMin96\FilamentJet\Features::registration()))
        {{ __('filament-jet::teams/invitation-mail.registration.title') }}

        @component('mail::button', ['url' => jetRouteActions()->registrationRoute()])
            {{ __('filament-jet::teams/invitation-mail.buttons.create_account') }}
        @endcomponent

        {{ __('filament-jet::teams/invitation-mail.registration.description') }}

    @else
        {{ __('filament-jet::teams/invitation-mail.accept_description') }}
    @endif


    @component('mail::button', ['url' => $acceptUrl])
        {{ __('filament-jet::teams/invitation-mail.buttons.accept') }}
    @endcomponent

    {{ __('filament-jet::teams/invitation-mail.warning') }}
@endcomponent
