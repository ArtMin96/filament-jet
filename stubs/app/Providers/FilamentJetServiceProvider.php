<?php

namespace App\Providers;

use App\Actions\FilamentJet\DeleteUser;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\FilamentJetServiceProvider as BaseFilamentJetServiceProvider;

class FilamentJetServiceProvider extends BaseFilamentJetServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        FilamentJet::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        FilamentJet::defaultApiTokenPermissions(['read']);

        FilamentJet::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
