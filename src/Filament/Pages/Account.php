<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use Filament\Pages\Page;

class Account extends Page
{
    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.account');
    }
}
