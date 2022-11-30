<?php

use ArtMin96\FilamentJet\Jet;
use ArtMin96\FilamentJet\RouteActions;

if (! function_exists('jetRouteActions')) {
    function jetRouteActions(): RouteActions
    {
        return new RouteActions();
    }
}

if (! function_exists('jet')) {
    function jet(): Jet
    {
        return new Jet();
    }
}
