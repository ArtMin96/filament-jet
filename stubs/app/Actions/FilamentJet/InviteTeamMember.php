<?php

namespace App\Actions\FilamentJet;

use App\Models\Team;
use App\Models\User;
use ArtMin96\FilamentJet\Contracts\InvitesTeamMembers;
use ArtMin96\FilamentJet\Events\InvitingTeamMember;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Mail\TeamInvitation;
use ArtMin96\FilamentJet\Rules\Role;
use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InviteTeamMember implements InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     */
    public function invite(User $user, Team $team, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTeamMember', $team);

        $this->validate($team, $email, $role);

        InvitingTeamMember::dispatch($team, $email, $role);

        $invitation = $team->teamInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new TeamInvitation($invitation));
    }

    /**
     * Validate the invite member operation.
     */
    protected function validate(Team $team, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($team), [
            'email.unique' => __('filament-jet::teams.validations.already_invited_to_team'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTeam($team, $email)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for inviting a team member.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Team $team): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('team_invitations')->where(function ($query) use ($team) {
                    $query->where('team_id', $team->id);
                }),
            ],
            'role' => FilamentJet::hasRoles()
                ? ['required', 'string', new Role]
                : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the team.
     */
    protected function ensureUserIsNotAlreadyOnTeam(Team $team, string $email): Closure
    {
        return function ($validator) use ($team, $email) {
            $validator->errors()->addIf(
                $team->hasUserWithEmail($email),
                'email',
                __('filament-jet::teams.validations.already_belongs_to_team')
            );
        };
    }
}
