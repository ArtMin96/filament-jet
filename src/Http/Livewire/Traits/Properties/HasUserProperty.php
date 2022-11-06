<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Traits\Properties;

use Filament\Facades\Filament;

trait HasUserProperty
{
    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Filament::auth()->user();
    }
}
