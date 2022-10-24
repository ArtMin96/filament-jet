<?php

use ArtMin96\FilamentJet\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users but you are free to change this value here.
    |
    | Out of the box, FilamentJet expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

    'username' => 'email',

    'email' => 'email',

    'redirects' => [
        'login' => null,
        'logout' => null,
        'password-confirmation' => null,
        'register' => '/testtt',
        //        'register' => config('filament.home_url', '/'),
        'email-verification' => null,
        'password-reset' => null,
    ],

    'route_group_prefix' => '',

    'profile' => [
        'require_current_password_on_change_password' => true,
    ],

    'should_register_navigation' => [
        'account' => false,
        'api_tokens' => false,
        'team_settings' => false,
        'create_team' => false,
    ],

    'user_menu' => [
        'account' => true,
        'api_tokens' => [
            'show' => true,
            'icon' => 'heroicon-o-key',
            'sort' => 1,
        ],
        'team_settings' => [
            'show' => true,
            'icon' => 'heroicon-o-cog',
            'sort' => 2,
        ],
        'create_team' => [
            'show' => true,
            'icon' => 'heroicon-o-users',
            'sort' => 3,
        ],

        'switchable_team' => [
            'show' => true,
            'icon' => '',
        ],
    ],

    'password_confirmation' => [
        'update_profile_information' => true,
        'change_password' => true,
        'enable_two_factor_authentication' => true,
        'delete_account' => true,
        'download_personal_data' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of FilamentJet's features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

    'features' => [
        Features::registration([
            'component' => \ArtMin96\FilamentJet\Http\Livewire\Auth\Register::class,
            'terms_of_service' => \ArtMin96\FilamentJet\Http\Livewire\TermsOfService::class,
            'privacy_policy' => \ArtMin96\FilamentJet\Http\Livewire\PrivacyPolicy::class,
            'auth_card_max_w' => null,
        ]),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
            // 'window' => 0,
        ]),

        Features::termsAndPrivacyPolicy(),
        Features::profilePhotos(),
        Features::api(),
        Features::teams(['invitations' => false]),
        Features::accountDeletion(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Photo Disk
    |--------------------------------------------------------------------------
    |
    | This configuration value determines the default disk that will be used
    | when storing profile photos for your application's users. Typically
    | this will be the "public" disk but you may adjust this if needed.
    |
    */

    'profile_photo_disk' => 'public',
];
