<?php

namespace ArtMin96\FilamentJet\Filament\Pages;

use Filament\Pages\Page;

class ApiTokens extends Page
{
    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-jet.should_register_navigation.api_tokens');
    }
}
