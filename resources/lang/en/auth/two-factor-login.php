<?php

return [

    'title' => 'Two Factor Challenge',

    'heading' => 'Two Factor Challenge',

    'buttons' => [

        'authenticate' => [
            'label' => 'Sign in',
        ],

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'recovery_code' => [
            'label' => 'Use a recovery code',
        ],

        'authentication_code' => [
            'label' => 'Use an authentication code',
        ],

    ],

    'fields' => [

        'code' => [
            'label' => 'Code',
            'placeholder' => 'XXX-XXX',
        ],

        'recoveryCode' => [
            'label' => 'Recovery code',
            'placeholder' => 'abcdef-98765',
        ],

    ],

    'messages' => [
        'failed' => [
            'code' => 'The provided two factor authentication code was invalid.',
            'recoveryCode' => 'The provided two factor recovery code was invalid.',
        ],
        'throttled' => 'Too many login attempts. Please try again in :seconds seconds.',
    ],

];
