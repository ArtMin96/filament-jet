<?php

namespace ArtMin96\FilamentJet\Http\Livewire\Traits\Properties;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasUserProperty
{
    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Filament::auth()->user();
    }
}
