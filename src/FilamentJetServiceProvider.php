<?php

namespace ArtMin96\FilamentJet;

use App\Actions\FilamentJet\UpdateUserPassword;
use App\Actions\FilamentJet\UpdateUserProfileInformation;
use ArtMin96\FilamentJet\Console\InstallCommand;
use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use ArtMin96\FilamentJet\Filament\Pages\Account;
use ArtMin96\FilamentJet\Filament\Pages\ApiTokens;
use ArtMin96\FilamentJet\Http\Livewire\ApiTokensTable;
use ArtMin96\FilamentJet\Http\Livewire\Auth\Login;
use ArtMin96\FilamentJet\Http\Livewire\Auth\Register;
use ArtMin96\FilamentJet\Http\Livewire\Auth\ResetPassword;
use ArtMin96\FilamentJet\Http\Livewire\Auth\Verify;
use ArtMin96\FilamentJet\Http\Livewire\LogoutOtherBrowserSessions;
use ArtMin96\FilamentJet\Http\Livewire\PersonalDataExport;
use ArtMin96\FilamentJet\Http\Livewire\PrivacyPolicy;
use ArtMin96\FilamentJet\Http\Livewire\TermsOfService;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\PluginServiceProvider;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Spatie\LaravelPackageTools\Package;

class FilamentJetServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-jet';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasRoute('web')
            ->hasViews()
            ->hasMigrations([
                '2014_10_12_000000_create_users_table',
                '2014_10_12_200000_add_two_factor_columns_to_users_table',
            ])
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

                if (Features::hasApiFeatures() && config('filament-jet.user_menu.api_tokens.show')) {
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
        $this->configurePublishing();

        Livewire::component(Login::getName(), Login::class);
        Livewire::component(ResetPassword::getName(), ResetPassword::class);
        Livewire::component(Verify::getName(), Verify::class);
        Livewire::component(LogoutOtherBrowserSessions::getName(), LogoutOtherBrowserSessions::class);
        Livewire::component(PersonalDataExport::getName(), PersonalDataExport::class);
        Livewire::component(ApiTokensTable::getName(), ApiTokensTable::class);

        if (Features::enabled(Features::registration())) {
            Livewire::component(Register::getName(), Register::class);
        }

        if (Features::hasTermsAndPrivacyPolicyFeature()) {
            Livewire::component(TermsOfService::getName(), TermsOfService::class);
            Livewire::component(PrivacyPolicy::getName(), PrivacyPolicy::class);
        }

        FilamentJet::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        FilamentJet::updateUserPasswordsUsing(UpdateUserPassword::class);
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(TwoFactorAuthenticationProviderContract::class, function ($app) {
            return new TwoFactorAuthenticationProvider(
                $app->make(Google2FA::class),
                $app->make(Repository::class)
            );
        });
    }

    /**
     * Configure the Filament Account Blade components.
     *
     * @return void
     */
    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('auth-card');
            $this->registerComponent('action-section');
            $this->registerComponent('form-section');
            $this->registerComponent('section-title');
            $this->registerComponent('section-border');
            $this->registerComponent('token-field');
            $this->registerComponent('progress-bar');
            $this->registerComponent('two-factor-security-code');
        });
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
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
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../stubs/config/filament-jet.php' => config_path('filament-jet.php'),
        ], 'filament-jet-config');

        $this->publishes([
            __DIR__.'/../database/migrations/2022_10_21_100000_create_teams_table.php' => database_path('migrations/2022_10_21_100000_create_teams_table.php'),
            __DIR__.'/../database/migrations/2022_10_21_200000_create_team_user_table.php' => database_path('migrations/2022_10_21_200000_create_team_user_table.php'),
            __DIR__.'/../database/migrations/2022_10_21_300000_create_team_invitations_table.php' => database_path('migrations/2022_10_21_300000_create_team_invitations_table.php'),
        ], 'filament-jet-team-migrations');
    }
}
