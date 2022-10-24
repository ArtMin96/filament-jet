<?php

namespace App\Actions\FilamentJet;

use ArtMin96\FilamentJet\Contracts\UpdatesUserProfileInformation;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param    $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        $user->updateProfilePhoto($input['profile_photo_path']);

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
