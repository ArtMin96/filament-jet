<?php

namespace ArtMin96\FilamentJet\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

abstract class Membership extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'team_user';
}
