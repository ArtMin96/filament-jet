<?php

namespace ArtMin96\FilamentJet\Http\Controllers;

use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CurrentTeamController extends Controller
{
    /**
     * Update the authenticated user's current team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $team = FilamentJet::newTeamModel()->findOrFail($request->team_id);

        if (! $request->user()->switchTeam($team)) {
            abort(403);
        }

        return redirect(config('filament.path'), 303);
    }
}
