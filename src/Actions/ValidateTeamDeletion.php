<?php

namespace ArtMin96\FilamentJet\Actions;

use App\Models\Team;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class ValidateTeamDeletion
{
    /**
     * Validate that the team can be deleted by the given user.
     */
    public function validate(User $user, Team $team): void
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
