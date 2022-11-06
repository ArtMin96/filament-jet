<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use ArtMin96\FilamentJet\Contracts\CreatesTeams;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use ArtMin96\FilamentJet\Traits\RedirectsActions;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class CreateTeam extends Page
{
    use RedirectsActions;
    use HasUserProperty;

    protected static string $view = 'filament-jet::filament.pages.create-team';

    public array $createTeamState = [];

    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.create_team');
    }

    protected function getForms(): array
    {
        return array_merge(
            parent::getForms(),
            [
                'createTeamForm' => $this->makeForm()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-jet::teams.create_team.fields.team_name'))
                            ->required()
                            ->maxLength(255),
                    ])
                    ->statePath('createTeamState'),
            ]
        );
    }

    /**
     * Create a new team.
     *
     * @param  \ArtMin96\FilamentJet\Contracts\CreatesTeams  $creator
     * @return \Illuminate\Http\Response
     */
    public function createTeam(CreatesTeams $creator)
    {
        $creator->create($this->user, $this->createTeamState);

        $this->notify('success', __('filament-jet::teams.create_team.created'), true);

        return $this->redirectPath($creator);
    }
}
