<?php

namespace ArtMin96\FilamentJet\Actions;

use ArtMin96\FilamentJet\Events\TeamMemberUpdated;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Rules\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateTeamMemberRole
{
    /**
     * Update the role for the given team member.
     *
     * @param mixed  $user
     * @param mixed  $team
     * @param int    $teamMemberId
     * @param string $role
     *
     * @return void
     * @throws AuthorizationException
     */
    public function update($user, $team, $teamMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateTeamMember', $team);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $team->users()->updateExistingPivot($teamMemberId, [
            'role' => $role,
        ]);

        TeamMemberUpdated::dispatch($team->fresh(), FilamentJet::findUserByIdOrFail($teamMemberId));
    }
}
