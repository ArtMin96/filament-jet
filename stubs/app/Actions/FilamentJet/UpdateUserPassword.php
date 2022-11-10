<?php

namespace App\Actions\FilamentJet;

use App\Models\User;
use ArtMin96\FilamentJet\Contracts\UpdatesUserPasswords;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use Illuminate\Support\Facades\Hash;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Update the user's password.
     */
    public function update(User $user, array $input): void
    {
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
