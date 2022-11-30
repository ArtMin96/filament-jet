<?php

namespace ArtMin96\FilamentJet;

use App\Actions\FilamentJet\AddTeamMember;
use App\Actions\FilamentJet\CreateTeam;
use App\Actions\FilamentJet\DeleteTeam;
use App\Actions\FilamentJet\DeleteUser;
use App\Actions\FilamentJet\InviteTeamMember;
use App\Actions\FilamentJet\RemoveTeamMember;
use App\Actions\FilamentJet\ResetUserPassword;
use App\Actions\FilamentJet\UpdateTeamName;
use App\Actions\FilamentJet\UpdateUserPassword;
use App\Actions\FilamentJet\UpdateUserProfileInformation;
use ArtMin96\FilamentJet\Filament\Pages\CreateTeam as CreateTeamPage;
use ArtMin96\FilamentJet\Console\InstallCommand;
use ArtMin96\FilamentJet\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use ArtMin96\FilamentJet\Filament\Pages\Account;
use ArtMin96\FilamentJet\Filament\Pages\ApiTokens;
use ArtMin96\FilamentJet\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use ArtMin96\FilamentJet\Filament\Pages\Auth\Login;
use ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use ArtMin96\FilamentJet\Filament\Pages\Auth\PasswordReset\ResetPassword;
use ArtMin96\FilamentJet\Filament\Pages\Auth\Register;
use ArtMin96\FilamentJet\Filament\Pages\Auth\TwoFactorLogin;
use ArtMin96\FilamentJet\Filament\Pages\TeamSettings;
use ArtMin96\FilamentJet\Http\Livewire\ApiTokensTable;
use ArtMin96\FilamentJet\Http\Livewire\LogoutOtherBrowserSessions;
use ArtMin96\FilamentJet\Http\Livewire\PrivacyPolicy;
use ArtMin96\FilamentJet\Http\Livewire\SwitchableTeam;
use ArtMin96\FilamentJet\Http\Livewire\TermsOfService;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\EmailVerificationResponse as EmailVerificationResponseContract;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use ArtMin96\FilamentJet\Http\Responses\Auth\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use ArtMin96\FilamentJet\Http\Responses\Auth\EmailVerificationResponse;
use ArtMin96\FilamentJet\Http\Responses\Auth\PasswordResetResponse;
use ArtMin96\FilamentJet\Http\Responses\Auth\TwoFactorLoginResponse;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\PluginServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Spatie\LaravelPackageTools\Package;

include 'helpers.php';

class FilamentJetServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-jet';

    /**
     * @var array<int, class-string>
     */
    protected array $pages = [
        Account::class,
        ApiTokens::class,
        TeamSettings::class,
        CreateTeamPage::class,
    ];

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

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        if (Features::enabled(Features::emailVerification())) {
            $this->app->bind(EmailVerificationResponseContract::class, EmailVerificationResponse::class);
        }

        if (Features::hasResetPasswordFeature()) {
            $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
        }

        if (Features::enabled(Features::twoFactorAuthentication())) {
            $this->app->bind(TwoFactorLoginResponseContract::class, TwoFactorLoginResponse::class);
        }
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

        $this->ensureApplicationIsTeamCompatible();
        $this->configureComponents();
        $this->configurePublishing();

        Livewire::component(Login::getName(), Login::class);
        Livewire::component(TwoFactorLogin::getName(), TwoFactorLogin::class);
        Livewire::component(EmailVerificationPrompt::getName(), EmailVerificationPrompt::class);
        Livewire::component(RequestPasswordReset::getName(), RequestPasswordReset::class);
        Livewire::component(ResetPassword::getName(), ResetPassword::class);
        Livewire::component(LogoutOtherBrowserSessions::getName(), LogoutOtherBrowserSessions::class);
        Livewire::component(ApiTokensTable::getName(), ApiTokensTable::class);

        if (Features::hasRegistrationFeature()) {
            Livewire::component(Register::getName(), Register::class);
        }

        if (Features::hasTermsAndPrivacyPolicyFeature()) {
            Livewire::component(TermsOfService::getName(), TermsOfService::class);
            Livewire::component(PrivacyPolicy::getName(), PrivacyPolicy::class);
        }

        FilamentJet::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        FilamentJet::updateUserPasswordsUsing(UpdateUserPassword::class);
        FilamentJet::resetUserPasswordsUsing(ResetUserPassword::class);
        FilamentJet::deleteUsersUsing(DeleteUser::class);
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

        $this->app->bind(StatefulGuard::class, function () {
            return Filament::auth();
        });
    }

    /**
     * Configure the Filament Account Blade components.
     */
    protected function configureComponents(): void
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
     */
    protected function registerComponent(string $component): void
    {
        Blade::component('filament-jet::components.'.$component, 'filament-jet-'.$component);
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishing(): void
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

    /**
     * Ensure the installed user model is ready for team usage.
     */
    protected function ensureApplicationIsTeamCompatible(): void
    {
        if (Features::hasTeamFeatures()) {
            FilamentJet::createTeamsUsing(CreateTeam::class);
            FilamentJet::updateTeamNamesUsing(UpdateTeamName::class);
            FilamentJet::addTeamMembersUsing(AddTeamMember::class);
            FilamentJet::inviteTeamMembersUsing(InviteTeamMember::class);
            FilamentJet::removeTeamMembersUsing(RemoveTeamMember::class);
            FilamentJet::deleteTeamsUsing(DeleteTeam::class);
            FilamentJet::deleteUsersUsing(DeleteUser::class);

            if (config('filament-jet.user_menu.switchable_team', true)) {
                Livewire::component('switchable-team', SwitchableTeam::class);

                Filament::registerRenderHook(
                    'user-menu.start',
                    fn (): string => Blade::render('@livewire(\'switchable-team\')'),
                );
            }

            if (config('filament-jet.user_menu.team_settings.show') ||
                config('filament-jet.user_menu.create_team.show')) {
                Filament::serving(function () {
                    $userMenuItems = [];

                    if (config('filament-jet.user_menu.team_settings.show')) {
                        $userMenuItems['team-settings'] = UserMenuItem::make()
                            ->label(__('filament-jet::jet.user_menu.team_settings'))
                            ->icon(config('filament-jet.user_menu.team_settings.icon', 'heroicon-o-cog'))
                            ->sort(config('filament-jet.user_menu.team_settings.sort'))
                            ->url(TeamSettings::getUrl());
                    }

                    if (config('filament-jet.user_menu.create_team.show')) {
                        $userMenuItems['create-team'] = UserMenuItem::make()
                            ->label(__('filament-jet::jet.user_menu.create_team'))
                            ->icon(config('filament-jet.user_menu.create_team.icon', 'heroicon-o-users'))
                            ->sort(config('filament-jet.user_menu.create_team.sort'))
                            ->url(CreateTeamPage::getUrl());
                    }

                    Filament::registerUserMenuItems($userMenuItems);
                });
            }
        }
    }
}
