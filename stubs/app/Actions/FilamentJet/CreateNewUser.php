<?php

namespace App\Actions\FilamentJet;

use App\Models\User;
use ArtMin96\FilamentJet\Contracts\CreatesNewUsers;
use ArtMin96\FilamentJet\FilamentJet;
use ArtMin96\FilamentJet\Traits\PasswordValidationRules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function create(array $input)
    {
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
