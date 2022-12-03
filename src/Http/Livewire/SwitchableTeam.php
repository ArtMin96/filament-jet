<?php

namespace ArtMin96\FilamentJet\Http\Livewire;

use ArtMin96\FilamentJet\Events\TeamSwitched;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\Traits\Properties\HasUserProperty;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\View\View;
use Livewire\Component;

class SwitchableTeam extends Component
{
    use HasUserProperty;

    public $teams;

    public function mount(): void
    {
        $this->teams = Filament::auth()->user()->allTeams();
    }

    /**
     * Update the authenticated user's current team.
     *
     * @param $teamId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function switchTeam($teamId)
    {
        $team = FilamentJet::newTeamModel()->findOrFail($teamId);

        if (! $this->user->switchTeam($team)) {
            abort(403);
        }

        TeamSwitched::dispatch($team->fresh(), $this->user);

        Notification::make()
            ->title(__('Team switched'))
            ->success()
            ->send();

        return redirect(config('filament.path'), 303);
    }

    public function render(): View
    {
        return view('filament-jet::components.switchable-team');
    }
}
