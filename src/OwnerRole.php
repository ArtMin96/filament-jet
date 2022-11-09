<?php

namespace ArtMin96\FilamentJet;

class OwnerRole extends Role
{
    /**
     * Create a new role instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('owner', __('filament-jet::jet.permissions.owner'), ['*']);
    }
}
