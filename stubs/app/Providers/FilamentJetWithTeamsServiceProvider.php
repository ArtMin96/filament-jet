<?php

namespace App\Providers;

use App\Actions\FilamentJet\AddTeamMember;
use App\Actions\FilamentJet\CreateTeam;
use App\Actions\FilamentJet\DeleteTeam;
use App\Actions\FilamentJet\DeleteUser;
use App\Actions\FilamentJet\InviteTeamMember;
use App\Actions\FilamentJet\RemoveTeamMember;
use App\Actions\FilamentJet\UpdateTeamName;
use App\Filament\Pages\Account;
use App\Filament\Pages\ApiTokens;
use App\Filament\Pages\CreateTeam as BaseCreateTeamPage;
use App\Filament\Pages\TeamSettings;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Http\Livewire\SwitchableTeam;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Livewire\Livewire;

class FilamentJetServiceProvider extends ServiceProvider
{
    protected array $pages = [
        Account::class,
        ApiTokens::class,
        TeamSettings::class,
        BaseCreateTeamPage::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Features::hasTeamFeatures()) {
            $this->configurePermissions();

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
                            ->url(BaseCreateTeamPage::getUrl());
                    }

                    Filament::registerUserMenuItems($userMenuItems);
                });
            }
        }

        if (Features::hasApiFeatures()) {
            $this->configureApiPermissions();
        }
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
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
     *
     * @return void
     */
    protected function configureApiPermissions()
    {
        FilamentJet::defaultApiTokenPermissions(['read']);
    }
}
