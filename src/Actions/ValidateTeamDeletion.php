<?php

namespace ArtMin96\FilamentJet\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidateTeamDeletion
{
    /**
     * Validate that the team can be deleted by the given user.
     *
     * @param $user
     * @param $team
     *
     * @return void
     * @throws ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function validate($user, $team)
    {
        Gate::forUser($user)->authorize('delete', $team);

        if ($team->personal_team) {
            throw ValidationException::withMessages([
                'team' => __('You may not delete your personal team.'),
            ])->errorBag('deleteTeam');
        }
    }
}
