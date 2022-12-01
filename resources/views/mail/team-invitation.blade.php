@component('mail::message')
    {{ __('filament-jet::teams.team_settings.invitations.mail.title', ['team' => $invitation->team->name]) }}

    @if (ArtMin96\FilamentJet\Features::enabled(ArtMin96\FilamentJet\Features::registration()))
        {{ __('filament-jet::teams.team_settings.invitations.mail.registration.title') }}

        @component('mail::button', ['url' => jetRouteActions()->registrationRoute()])
            {{ __('filament-jet::teams.team_settings.invitations.mail.actions.create_account') }}
        @endcomponent

        {{ __('filament-jet::teams.team_settings.invitations.mail.registration.description') }}

    @else
        {{ __('filament-jet::teams.team_settings.invitations.mail.accept_description') }}
    @endif


    @component('mail::button', ['url' => $acceptUrl])
        {{ __('filament-jet::teams.team_settings.invitations.mail.actions.accept') }}
    @endcomponent

    {{ __('filament-jet::teams.team_settings.invitations.mail.warning') }}
@endcomponent
