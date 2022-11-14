<?php

namespace ArtMin96\FilamentJet\Actions;

use App\Models\Team;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class ValidateTeamDeletion
{
    /**
     * Validate that the team can be deleted by the given user.
     */
    public function validate(Authenticatable $user, Team $team): void
    {
        Gate::forUser($user)->authorize('delete', $team);

        if ($team->personal_team) {
            Notification::make()
                ->title(__('filament-jet::teams.team_settings.delete_team.validation.cannot_delete_personal_team'))
                ->warning()
                ->send();
        }
    }
}
