<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use App\Models\User;
use ArtMin96\FilamentJet\Actions\UpdateTeamMemberRole;
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
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Role;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Suleymanozev\FilamentRadioButtonField\Forms\Components\RadioButton;

class TeamSettings extends Page
{
    use HasCachedAction;
    use RedirectsActions;
    use HasUserProperty;

    protected static string $view = 'filament-jet::filament.pages.team-settings';

    public $team;

    public array $teamState = [];

    public array $addTeamMemberState = [];

    public $email;

    public $role;

    /**
     * The user that is having their role managed.
     *
     * @var mixed
     */
    public $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     *
     * @var string
     */
    public $currentRole;

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
     * Get the available team member roles.
     */
    public function getRolesProperty(): array
    {
        return collect(FilamentJet::$roles)->transform(function ($role) {
            return with($role->jsonSerialize(), function ($data) {
                return (new Role(
                    $data['key'],
                    $data['name'],
                    $data['permissions']
                ))->description($data['description']);
            });
        })
            ->values()
            ->all();
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
                            ->disabled(! Gate::check('update', $this->team)),
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
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($this->team->hasUserWithEmail($value)) {
                                            $fail(__('filament-jet::validation.teams.user_belongs_to_team'));
                                        }
                                    };
                                },
                            ]),
                        RadioButton::make('role')
                            ->label(__('filament-jet::teams.team_settings.add_team_member.fields.role'))
                            ->options(
                                collect($this->roles)->mapWithKeys(fn ($role): array => [
                                    $role->key => $role->name,
                                ])->toArray()
                            )
                            ->descriptions(
                                collect($this->roles)->mapWithKeys(fn ($role): array => [
                                    $role->key => $role->description,
                                ])->toArray()
                            )
                            ->columns(1)
                            ->rules(FilamentJet::hasRoles()
                                ? ['required', 'string', new \ArtMin96\FilamentJet\Rules\Role]
                                : []
                            ),
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
                ->action('deleteTeam'),
            Action::make('manage_role')
                ->action(function (array $data): void {
                    $this->updateRole(app(UpdateTeamMemberRole::class));
                })
                ->modalWidth('lg')
                ->modalHeading(__('filament-jet::teams.team_settings.team_members.manage.modal_heading'))
                ->modalSubheading(__('filament-jet::teams.team_settings.team_members.manage.modal_subheading'))
                ->modalButton(__('filament-jet::teams.team_settings.team_members.manage.modal_submit'))
                ->form([
                    RadioButton::make('role')
                        ->label(__('filament-jet::teams.team_settings.add_team_member.fields.role'))
                        ->options(
                            collect($this->roles)->mapWithKeys(fn ($role): array => [
                                $role->key => $role->name,
                            ])->toArray()
                        )
                        ->descriptions(
                            collect($this->roles)->mapWithKeys(fn ($role): array => [
                                $role->key => $role->description,
                            ])->toArray()
                        )
                        ->afterStateUpdated(
                            fn ($state) => $this->currentRole = $state
                        )
                        ->columns(1)
                        ->rules(FilamentJet::hasRoles()
                            ? ['required', 'string', new \ArtMin96\FilamentJet\Rules\Role]
                            : []
                        ),
                ]),
        ];
    }

    public function updateTeamName(UpdatesTeamNames $updater): void
    {
        $updater->update($this->user, $this->team, $this->teamState);

        $this->notify('success', __('filament-jet::teams.team_settings.update_name.updated'));
    }

    /**
     * Add a new team member to a team.
     */
    public function addTeamMember(): void
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
            'role' => null,
        ];

        $this->team = $this->team->fresh();

        $this->notify('success', $message);
    }

    /**
     * Cancel a pending team member invitation.
     *
     * @param  int  $invitationId
     */
    public function cancelTeamInvitation(int $invitationId): void
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
     * @param  ValidateTeamDeletion  $validator
     * @param  DeletesTeams  $deleter
     */
    public function deleteTeam(ValidateTeamDeletion $validator, DeletesTeams $deleter): Response
    {
        $validator->validate(Filament::auth()->user(), $this->team);

        $deleter->delete($this->team);

        $this->notify('success', __('filament-jet::teams.team_settings.delete_team.notify'), true);

        return $this->redirectPath($deleter);
    }

    /**
     * Remove a team member from the team.
     *
     * @param    $userId
     * @param  RemovesTeamMembers  $remover
     */
    public function removeTeamMember($userId, RemovesTeamMembers $remover): void
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

    /**
     * Allow the given user's role to be managed.
     *
     * @param  int  $userId
     */
    public function manageRole(int $userId): void
    {
        $this->managingRoleFor = FilamentJet::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->teamRole($this->team)->key;

        $this->mountAction('manage_role');
        $this->getMountedActionForm()->fill(['role' => $this->currentRole]);
    }

    /**
     * Save the role for the user being managed.
     *
     * @param  UpdateTeamMemberRole  $updater
     */
    public function updateRole(UpdateTeamMemberRole $updater): void
    {
        $updater->update(
            $this->user,
            $this->team,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->team = $this->team->fresh();

        $this->notify('success', __('filament-jet::teams.team_settings.team_members.manage.notify.success'));
    }
}
