<?php

namespace App\Providers;

use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Support\ServiceProvider;

class FilamentJetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Features::hasTeamFeatures()) {
            $this->configurePermissions();
        }

        if (Features::hasApiFeatures()) {
            $this->configureApiPermissions();
        }
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        FilamentJet::role('admin', __('filament-jet::jet.permissions.admin.label'), [
            'create',
            'read',
            'update',
            'delete',
        ])->description(__('filament-jet::jet.permissions.admin.description'));

        FilamentJet::role('editor', __('filament-jet::jet.permissions.editor.label'), [
            'read',
            'create',
            'update',
        ])->description(__('filament-jet::jet.permissions.editor.description'));
    }

    /**
     * Configure the sanctum permissions that are available within the application.
     */
    protected function configureApiPermissions(): void
    {
        FilamentJet::defaultApiTokenPermissions(['read']);
    }
}
