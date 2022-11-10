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
     * @var array<string, string>
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];
}
