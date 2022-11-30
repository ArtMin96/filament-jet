<?php

namespace App\Actions\FilamentJet;

use App\Models\Team;
use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Model | Authenticatable
    {
        return DB::transaction(function () use ($input) {
            return tap(FilamentJet::userModel()::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function ($user) {

                app()->bind(
                    \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
                    \ArtMin96\FilamentJet\Listeners\Auth\SendEmailVerificationNotification::class,
                );

                event(new Registered($user));

                if (Features::hasTeamFeatures()) {
                    $this->createTeam($user);
                }

                return $user;
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param Model|Authenticatable $user
     */
    protected function createTeam(Model | Authenticatable $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->getKey(),
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
