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
        'register' => config('filament.home_url', '/'),
        'email-verification' => null,
        'password-reset' => null,
    ],

    'route_group_prefix' => '',

    'password_reset_component' => \ArtMin96\FilamentJet\Http\Livewire\Auth\ResetPassword::class,

    'profile' => [
        'login_field' => [
            'hint_action' => [
                'icon' => 'heroicon-o-question-mark-circle',
                'tooltip' => 'After changing the email address, confirmation is mandatory.',
            ],
        ],

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
        'enable_two_factor_authentication' => true,
        'disable_two_factor_authentication' => true,
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
        Features::teams(['invitations' => true]),
        Features::logoutOtherBrowserSessions(),
        Features::accountDeletion(),

        /**
         * @see https://github.com/spatie/laravel-personal-data-export
         */
        Features::personalDataExport([
            /**
             * The name of the export itself can be set using the personalDataExportName on the user.
             * This will only affect the name of the download that will be sent as a response to the user,
             * not the name of the zip stored on disk.
             */
            'export-name' => 'personal-data',

            /**
             * The first parameter is the name of the file in the inside the zip file.
             * The second parameter is the content that should go in that file.
             * If you pass an array here, we will encode it to JSON.
             */
            'add' => [
//                ['nameInDownload' => '', 'content' => []]
            ],

            /**
             * The first parameter is a path to a file which will be copied to the zip.
             * You can also add a disk name as the second parameter and directory as the third parameter.
             */
            'add-files' => [
//                ['pathToFile' => '', 'diskName' => '', 'directory' => '']
            ]
        ]),
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

    'profile_photo_directory' => 'profile-photos',

    'password_confirmation_seconds' => config('auth.password_timeout'),

    /*
    |--------------------------------------------------------------------------
    | The reset broker to be used in your reset password requests
    */

    'reset_broker' => config('auth.defaults.passwords'),
];
