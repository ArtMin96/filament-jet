<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use App\Models\User;
use ArtMin96\FilamentJet\Actions\ValidateTeamDeletion;
use ArtMin96\FilamentJet\Contracts\AddsTeamMembers;
use ArtMin96\FilamentJet\Contracts\DeletesTeams;
use ArtMin96\FilamentJet\Contracts\InvitesTeamMembers;
use ArtMin96\FilamentJet\Contracts\RemovesTeamMembers;
use ArtMin96\FilamentJet\Contracts\UpdatesTeamNames;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\Filament\Actions\AlwaysAskPasswordButtonAction;
use ArtMin96\FilamentJet\Filament\Traits\HasCachedAction;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Role;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Suleymanozev\FilamentRadioButtonField\Forms\Components\RadioButton;

class TeamSettings extends Page
{
    use HasCachedAction;
    use RedirectsActions;

    protected static string $view = 'filament-jet::filament.pages.team-settings';

    public $team;

    public array $teamState = [];

    public array $addTeamMemberState = [];

    public $email;

    public $role;

    public function mount()
    {
        $this->team = $this->user->currentTeam;

        if (! $this->team) {
            $this->notify('success', __('filament-jet::teams.team_settings.current_team_not_exists'), true);

            return redirect(config('filament.path'));
        }

        $this->updateTeamNameForm->fill($this->team->withoutRelations()->toArray());
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.team_settings');
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Filament::auth()->user();
    }

    /**
     * Get the available team member roles.
     *
     * @return array
     */
    public function getRolesProperty()
    {
        return collect(FilamentJet::$roles)->transform(function ($role) {
            return with($role->jsonSerialize(), function ($data) {
                return (new Role(
                    $data['key'],
                    $data['name'],
                    $data['permissions']
                ))->description($data['description']);
            });
        })->values()->all();
    }

    protected function getForms(): array
    {
        return array_merge(
            parent::getForms(),
            [
                'updateTeamNameForm' => $this->makeForm()
                    ->model(FilamentJet::teamModel())
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-jet::teams.team_settings.update_name.fields.team_name'))
                            ->required()
                            ->maxLength(255)
                    ])
                    ->statePath('teamState'),
                'addTeamMemberForm' => $this->makeForm()
                    ->model(FilamentJet::teamModel())
                    ->schema([
                        TextInput::make('email')
                            ->label(__('filament-jet::teams.team_settings.add_team_member.fields.email'))
                            ->required()
                            ->maxLength(255)
                            ->rule('email')
                            ->exists(table: User::class, column: 'email')
                            ->rules([
                                function() {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($this->team->hasUserWithEmail($value)) {
                                            $fail(__('filament-jet::validation.teams.user_belongs_to_team'));
                                        }
                                    };
                                }
                            ]),
                        RadioButton::make('role')
                            ->label(__('filament-jet::teams.team_settings.add_team_member.fields.role'))
                            ->options(
                                collect($this->roles)->mapWithKeys(fn($role): array => [
                                    $role->key => $role->name
                                ])->toArray()
                            )
                            ->descriptions(
                                collect($this->roles)->mapWithKeys(fn($role): array => [
                                    $role->key => $role->description
                                ])->toArray()
                            )
                            ->columns(1)
                            ->rules(FilamentJet::hasRoles()
                                ? ['required', 'string', new \ArtMin96\FilamentJet\Rules\Role]
                                : []
                            )
                    ])
                    ->statePath('addTeamMemberState'),
            ]
        );
    }

    protected function getHiddenActions(): array
    {
        return [
            AlwaysAskPasswordButtonAction::make('delete_team')
                ->label(__('filament-jet::teams.team_settings.delete_team.actions.delete'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('deleteTeam')
        ];
    }

    public function updateTeamName(UpdatesTeamNames $updater)
    {
        $updater->update($this->user, $this->team, $this->teamState);

        $this->notify("success", __('filament-jet::teams.team_settings.update_name.updated'));
    }

    /**
     * Add a new team member to a team.
     *
     * @return void
     */
    public function addTeamMember()
    {
        $this->addTeamMemberForm->getState();

        if (Features::sendsTeamInvitations()) {
            app(InvitesTeamMembers::class)->invite(
                $this->user,
                $this->team,
                $this->addTeamMemberState['email'],
                $this->addTeamMemberState['role']
            );

            $message = __('filament-jet::teams.team_settings.add_team_member.notify.invited');
        } else {
            app(AddsTeamMembers::class)->add(
                $this->user,
                $this->team,
                $this->addTeamMemberState['email'],
                $this->addTeamMemberState['role']
            );

            $message = __('filament-jet::teams.team_settings.add_team_member.notify.added');
        }

        $this->addTeamMemberState = [
            'email' => '',
            'role' => null
        ];

        $this->team = $this->team->fresh();

        $this->notify("success", $message);
    }

    /**
     * Cancel a pending team member invitation.
     *
     * @param  int  $invitationId
     * @return void
     */
    public function cancelTeamInvitation($invitationId)
    {
        if (! empty($invitationId)) {
            $model = FilamentJet::teamInvitationModel();

            $model::whereKey($invitationId)->delete();
        }

        $this->team = $this->team->fresh();

        $this->notify('success', __('filament-jet::teams.team_settings.add_team_member.notify.invitation_canceled'));
    }

    /**
     * Delete the team.
     *
     * @param ValidateTeamDeletion $validator
     * @param DeletesTeams         $deleter
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteTeam(ValidateTeamDeletion $validator, DeletesTeams $deleter)
    {
        $validator->validate(Filament::auth()->user(), $this->team);

        $deleter->delete($this->team);

        $this->notify('success', __('filament-jet::teams.team_settings.delete_team.notify'), true);

        return $this->redirectPath($deleter);
    }

    /**
     * Remove a team member from the team.
     *
     * @param                    $userId
     * @param RemovesTeamMembers $remover
     *
     * @return void
     */
    public function removeTeamMember($userId, RemovesTeamMembers $remover)
    {
        $remover->remove(
            $this->user,
            $this->team,
            $user = FilamentJet::findUserByIdOrFail($userId)
        );

        $this->team = $this->team->fresh();

        $this->notify('success', __('filament-jet::teams.team_settings.team_members.notify.removed'));
    }

    /**
     * Remove the currently authenticated user from the team.
     *
     * @param  \ArtMin96\FilamentJet\Contracts\RemovesTeamMembers  $remover
     * @return void
     */
    public function leaveTeam(RemovesTeamMembers $remover)
    {
        $this->errorBagExcept('team');

        $remover->remove(
            $this->user,
            $this->team,
            $this->user
        );

        $this->team = $this->team->fresh();

        $this->notify('success', __('filament-jet::teams.team_settings.team_members.notify.leave'), true);

        return redirect(config('filament.path'));
    }
}
