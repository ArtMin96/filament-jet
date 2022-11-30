<?php

namespace App\Actions\FilamentJet;

use App\Models\User;
use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use ArtMin96\FilamentJet\Features;
use ArtMin96\FilamentJet\FilamentJet;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param Model|Authenticatable $user
     * @param array<string, string> $input
     */
    public function update(Model | Authenticatable $user, array $input): void
    {
        if (Features::managesProfilePhotos()) {
            $user->updateProfilePhoto($input['profile_photo_path']);
        }

        if ($input[FilamentJet::email()] !== $user->{FilamentJet::email()} &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                FilamentJet::username() => $input[FilamentJet::username()],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param Model|Authenticatable $user
     * @param array<string, string> $input
     */
    protected function updateVerifiedUser(Model | Authenticatable $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            FilamentJet::email() => $input[FilamentJet::email()],
            FilamentJet::email().'_verified_at' => null,
        ])->save();

        app()->bind(
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
            \ArtMin96\FilamentJet\Listeners\Auth\SendEmailVerificationNotification::class,
        );
    }
}
