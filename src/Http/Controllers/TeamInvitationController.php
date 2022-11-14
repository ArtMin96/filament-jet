<?php

namespace ArtMin96\FilamentJet\Http\Controllers;

use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Models\TeamInvitation;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  Request  $request
     * @param  TeamInvitation  $invitation
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function accept(Request $request, TeamInvitation $invitation)
    {
        if (! Features::hasTeamFeatures()) {
            $invitation->delete();

            Notification::make()
                ->title(__('filament-jet::teams.team_settings.invitations.notifications.feature_disabled'))
                ->success()
                ->send();

            return redirect(config('filament.path'));
        }

        app(AddsTeamMembers::class)->add(
            $invitation->team->owner,
            $invitation->team,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        $newTeamMember = FilamentJet::findUserByEmailOrFail($invitation->email);

        if (! $newTeamMember->switchTeam($invitation->team)) {
            abort(403);
        }

        Notification::make()
            ->title(__('filament-jet::teams.team_settings.invitations.notifications.invited', ['team' => $invitation->team->name]))
            ->success()
            ->send();

        return redirect(config('filament.path'));
    }

    /**
     * Cancel the given team invitation.
     *
     * @param  Request  $request
     * @param  TeamInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Request $request, TeamInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
