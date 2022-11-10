<?php

namespace App\Actions\FilamentJet;

use App\Models\Team;
use App\Models\User;
use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UpdateTeamName implements UpdatesTeamNames
{
    /**
     * Validate and update the given team's name.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, Team $team, array $input): void
    {
        Gate::forUser($user)->authorize('update', $team);

        $team->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
