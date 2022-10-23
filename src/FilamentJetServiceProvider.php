<?php

namespace ArtMin96\FilamentJet;

use ArtMin96\FilamentJet\Console\InstallCommand;
use ArtMin96\FilamentJet\Filament\Pages\Account;
use ArtMin96\FilamentJet\Filament\Pages\ApiTokens;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Spatie\LaravelPackageTools\Package;

class FilamentJetServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-jet';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasViews()
            ->hasMigrations([
                '2014_10_12_000000_create_users_table',
                '2014_10_12_200000_add_two_factor_columns_to_users_table',
            ])
            ->runsMigrations()
            ->hasTranslations()
            ->hasCommand(InstallCommand::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (config('filament-jet.user_menu.account') || config('filament-jet.user_menu.api_tokens.show')) {
            Filament::serving(function () {
                $userMenuItems = [];

                if (config('filament-jet.user_menu.account')) {
                    $userMenuItems['account'] = UserMenuItem::make()
                        ->url(Account::getUrl());
                }

                if (config('filament-jet.user_menu.api_tokens.show')) {
                    $userMenuItems['api-tokens'] = UserMenuItem::make()
                        ->label(__('filament-jet::jet.user_menu.api_tokens'))
                        ->icon(config('filament-jet.user_menu.api_tokens.icon', 'heroicon-o-key'))
                        ->sort(config('filament-jet.user_menu.api_tokens.sort'))
                        ->url(ApiTokens::getUrl());
                }

                Filament::registerUserMenuItems($userMenuItems);
            });
        }

        $this->configureComponents();
    }

    /**
     * Configure the Filament Account Blade components.
     *
     * @return void
     */
    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('action-section');
            $this->registerComponent('form-section');
            $this->registerComponent('section-title');
            $this->registerComponent('section-border');
        });
    }

    /**
     * Register the given component.
     *
     * @param string $component
     *
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('filament-jet::components.'.$component, 'filament-jet-'.$component);
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../database/migrations/2022_10_21_100000_create_teams_table.php' => database_path('migrations/2022_10_21_100000_create_teams_table.php'),
            __DIR__.'/../database/migrations/2022_10_21_200000_create_team_user_table.php' => database_path('migrations/2022_10_21_200000_create_team_user_table.php'),
            __DIR__.'/../database/migrations/2022_10_21_300000_create_team_invitations_table.php' => database_path('migrations/2022_10_21_300000_create_team_invitations_table.php'),
        ], 'filament-jet-team-migrations');
    }
}
