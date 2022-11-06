<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Traits\Properties;

use ArtMin96\FilamentJet\FilamentJet;

trait HasSanctumPermissionsProperty
{
    public function getSanctumPermissionsProperty()
    {
        return collect(FilamentJet::$permissions)
            ->mapWithKeys(function ($permission) {
                return [$permission => $permission];
            });
    }
}
