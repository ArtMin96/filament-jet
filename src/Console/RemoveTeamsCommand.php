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
    protected $description = 'Remove teams components and resources from FilamentJet';

    public function handle()
    {
        (new Filesystem)->delete(resource_path('views/filament/pages/teams.blade.php'));
        (new Filesystem)->delete(app_path('Filament/Pages/Teams.php'));
    }
}
