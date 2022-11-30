<?php

namespace ArtMin96\FilamentJet\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-jet:install {--teams : Indicates if team support should be installed}
                                              {--api : Indicates if API support should be installed}
                                              {--verification : Indicates if email verification support should be installed}
                                              {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Filament Jet components and resources';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'filament-jet-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'filament-jet-migrations', '--force' => true]);

        // Storage...
        $this->callSilent('storage:link');

        // Configure Session...
        $this->configureSession();

        // Configure API...
        if ($this->option('api')) {
            $this->replaceInFile('// Features::api(),', 'Features::api(),', config_path('filament-jet.php'));
        }

        // Configure Email Verification...
        if ($this->option('verification')) {
            $this->replaceInFile(
                '// Features::emailVerification([
        //     \'page\' => \ArtMin96\FilamentJet\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt::class,
        //     \'controller\' => \ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController::class,
        //     \'card_width\' => \'md\',
        //     \'has_brand\' => true,
        //     \'rate_limiting\' => [
        //         \'enabled\' => true,
        //         \'limit\' => 5
        //     ],
        // ]),',
                'Features::emailVerification([
            \'page\' => \ArtMin96\FilamentJet\Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt::class,
            \'controller\' => \ArtMin96\FilamentJet\Http\Controllers\Auth\EmailVerificationController::class,
            \'card_width\' => \'md\',
            \'has_brand\' => true,
            \'rate_limiting\' => [
                \'enabled\' => true,
                \'limit\' => 5
            ],
        ]),',
                config_path('filament-jet.php')
            );
        }

        $this->installStack();
    }

    /**
     * Configure the session driver for Jetstream.
     *
     * @return void
     */
    protected function configureSession()
    {
        if (! class_exists('CreateSessionsTable')) {
            try {
                $this->call('session:table');
            } catch (Exception $e) {
                //
            }
        }

        $this->replaceInFile("'SESSION_DRIVER', 'file'", "'SESSION_DRIVER', 'database'", config_path('session.php'));
        $this->replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env'));
        $this->replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env.example'));
    }

    /**
     * Install the Livewire stack into the application.
     *
     * @return void
     */
    protected function installStack()
    {
        // Sanctum...
        (new Process([$this->phpBinary(), 'artisan', 'vendor:publish', '--provider=Laravel\Sanctum\SanctumServiceProvider', '--force'], base_path()))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });

        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/FilamentJet'));
        (new Filesystem)->ensureDirectoryExists(resource_path('markdown'));

        // Terms Of Service / Privacy Policy...
        copy(__DIR__.'/../../stubs/resources/markdown/terms.md', resource_path('markdown/terms.md'));
        copy(__DIR__.'/../../stubs/resources/markdown/policy.md', resource_path('markdown/policy.md'));

        // Service Providers...
        copy(__DIR__.'/../../stubs/app/Providers/FilamentJetServiceProvider.php', app_path('Providers/FilamentJetServiceProvider.php'));
        $this->installServiceProviderAfter('RouteServiceProvider', 'FilamentJetServiceProvider');

        // Models...
        copy(__DIR__.'/../../stubs/app/Models/User.php', app_path('Models/User.php'));

        // Factories...
        copy(__DIR__.'/../../database/factories/UserFactory.php', base_path('database/factories/UserFactory.php'));

        // Actions...
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/CreateNewUser.php', app_path('Actions/FilamentJet/CreateNewUser.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/ResetUserPassword.php', app_path('Actions/FilamentJet/ResetUserPassword.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/UpdateUserProfileInformation.php', app_path('Actions/FilamentJet/UpdateUserProfileInformation.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/UpdateUserPassword.php', app_path('Actions/FilamentJet/UpdateUserPassword.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/DeleteUser.php', app_path('Actions/FilamentJet/DeleteUser.php'));

        // Routes...
        $this->replaceInFile('auth:api', 'auth:sanctum', base_path('routes/api.php'));

        if (! Str::contains(file_get_contents(base_path('routes/web.php')), "'/register'")) {
            (new Filesystem)->append(base_path('routes/web.php'), $this->routeDefinition());
        }

        // Teams...
        if ($this->option('teams')) {
            $this->installTeamStack();
        }

        $this->line('');
        $this->components->info('Filament Jet scaffolding installed successfully.');
    }

    protected function installTeamStack()
    {
        // ...

        $this->ensureApplicationIsTeamCompatible();
    }

    /**
     * Ensure the installed user model is ready for team usage.
     *
     * @return void
     */
    protected function ensureApplicationIsTeamCompatible()
    {
        // Publish Team Migrations...
        $this->callSilent('vendor:publish', ['--tag' => 'filament-jet-team-migrations', '--force' => true]);

        // Configuration...
        $this->replaceInFile(
            '// Features::teams([
        //     \'invitations\' => true,
        //     \'middleware\' => [\'verified\'],
        //     \'invitation\' => [
        //         \'controller\' => \ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController::class,
        //         \'actions\' => [
        //             \'accept\' => \'accept\',
        //             \'destroy\' => \'destroy\',
        //         ],
        //     ],
        // ]),',
            'Features::teams([
            \'invitations\' => true,
            \'middleware\' => [\'verified\'],
            \'invitation\' => [
                \'controller\' => \ArtMin96\FilamentJet\Http\Controllers\TeamInvitationController::class,
                \'actions\' => [
                    \'accept\' => \'accept\',
                    \'destroy\' => \'destroy\',
                ],
            ],
        ]),',
            config_path('filament-jet.php')
        );

        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/FilamentJet'));
        (new Filesystem)->ensureDirectoryExists(app_path('Events'));
        (new Filesystem)->ensureDirectoryExists(app_path('Policies'));

        // Service Providers...
        copy(__DIR__.'/../../stubs/app/Providers/AuthServiceProvider.php', app_path('Providers/AuthServiceProvider.php'));
        copy(__DIR__.'/../../stubs/app/Providers/FilamentJetWithTeamsServiceProvider.php', app_path('Providers/FilamentJetServiceProvider.php'));

        // Models...
        copy(__DIR__.'/../../stubs/app/Models/Membership.php', app_path('Models/Membership.php'));
        copy(__DIR__.'/../../stubs/app/Models/Team.php', app_path('Models/Team.php'));
        copy(__DIR__.'/../../stubs/app/Models/TeamInvitation.php', app_path('Models/TeamInvitation.php'));
        copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.php', app_path('Models/User.php'));

        // Actions...
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/AddTeamMember.php', app_path('Actions/FilamentJet/AddTeamMember.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/CreateTeam.php', app_path('Actions/FilamentJet/CreateTeam.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/DeleteTeam.php', app_path('Actions/FilamentJet/DeleteTeam.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/DeleteUserWithTeams.php', app_path('Actions/FilamentJet/DeleteUser.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/InviteTeamMember.php', app_path('Actions/FilamentJet/InviteTeamMember.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/RemoveTeamMember.php', app_path('Actions/FilamentJet/RemoveTeamMember.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/UpdateTeamName.php', app_path('Actions/FilamentJet/UpdateTeamName.php'));
        copy(__DIR__.'/../../stubs/app/Actions/FilamentJet/CreateNewUserWithTeams.php', app_path('Actions/FilamentJet/CreateNewUser.php'));

        // Policies...
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/app/Policies', app_path('Policies'));

        // Factories...
        copy(__DIR__.'/../../database/factories/UserFactory.php', base_path('database/factories/UserFactory.php'));
        copy(__DIR__.'/../../database/factories/TeamFactory.php', base_path('database/factories/TeamFactory.php'));
    }

    /**
     * Get the route definition(s) that should be installed for Livewire.
     *
     * @return string
     */
    protected function routeDefinition()
    {
        return <<<'EOF'
Route::domain(config("filament.domain"))
    ->middleware(config("filament.middleware.base"))
    ->name(config('filament-jet.route_group_prefix'))
    ->prefix(config("filament.path"))
    ->group(function () {
        // Personal data export...
        if (\ArtMin96\FilamentJet\Features::canExportPersonalData()) {
            Route::personalDataExports('personal-data-exports');
        }
    });
EOF;
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @param  string  $after
     * @param  string  $name
     * @return void
     */
    protected function installServiceProviderAfter($after, $name)
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\'.$name.'::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\'.$after.'::class,',
                'App\\Providers\\'.$after.'::class,'.PHP_EOL.'        App\\Providers\\'.$name.'::class,',
                $appConfig
            ));
        }
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }
}
