<?php

namespace ArtMin96\FilamentJet\Contracts;

interface DeletesTeams
{
    /**
     * Delete the given team.
     *
     * @param  mixed  $team
     * @return void
     */
    public function delete($team);
}
