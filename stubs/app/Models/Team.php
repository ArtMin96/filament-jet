<?php

namespace App\Models;

use ArtMin96\FilamentJet\Events\TeamCreated;
use ArtMin96\FilamentJet\Events\TeamDeleted;
use ArtMin96\FilamentJet\Events\TeamUpdated;
use ArtMin96\FilamentJet\Models\Team as FilamentJetTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends FilamentJetTeam
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];
}
