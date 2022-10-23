<?php

namespace App\Actions\FilamentJet;

use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateTeamName implements UpdatesTeamNames
{
    /**
     * Validate and update the given team's name.
     *
     * @param mixed $user
     * @param mixed $team
     * @param array $input
     *
     * @return void
     * @throws AuthorizationException
     */
    public function update($user, $team, array $input)
    {
        Gate::forUser($user)->authorize('update', $team);

        $team->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
