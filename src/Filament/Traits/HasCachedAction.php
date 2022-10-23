<?php

namespace ArtMin96\FilamentJet\Filament\Traits;

use Filament\Pages\Actions\Action;

trait HasCachedAction
{
    public function getCachedAction(string $name): ?Action
    {
        if ($action = parent::getCachedAction($name)) {
            return $action;
        }

        foreach ($this->getHiddenActions() as $action) {
            if ($name === $action->getName()) {
                return $action->livewire($this);
            }
        }

        return null;
    }
}
