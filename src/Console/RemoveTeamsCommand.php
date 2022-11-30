<?php

namespace ArtMin96\FilamentJet\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RemoveTeamsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-jet:remove-teams {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove teams components and resources from Filament Jet';

    public function handle()
    {
        // Models...
        (new Filesystem)->delete(app_path('Models/Membership.php'));
        (new Filesystem)->delete(app_path('Models/Team.php'));
        (new Filesystem)->delete(app_path('Models/TeamInvitation.php'));

        // Actions...
        (new Filesystem)->delete(app_path('Actions/FilamentJet/AddTeamMember.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/CreateTeam.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/DeleteTeam.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/DeleteUser.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/InviteTeamMember.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/RemoveTeamMember.php'));
        (new Filesystem)->delete(app_path('Actions/FilamentJet/UpdateTeamName.php'));

        // Policies...
        (new Filesystem)->delete(app_path('Policies/TeamPolicy.php'));

        // Factories...
        (new Filesystem)->delete(base_path('database/factories/TeamFactory.php'));
    }
}
