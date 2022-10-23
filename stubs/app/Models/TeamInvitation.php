<?php

namespace App\Models;

use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Models\TeamInvitation as FilamentJetTeamInvitation;

class TeamInvitation extends FilamentJetTeamInvitation
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the team that the invitation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(FilamentJet::teamModel());
    }
}
