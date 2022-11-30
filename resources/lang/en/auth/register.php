<?php

return [

    'title' => 'Register',

    'heading' => 'Sign up',

    'buttons' => [

        'login' => [
            'before' => 'or',
            'label' => 'sign in to your account',
        ],

        'register' => [
            'label' => 'Sign up',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Email address',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'passwordConfirmation' => [
            'label' => 'Confirm your password',
        ],

        'terms_and_policy' => [
            'label' => 'I agree to the :terms_of_service and :privacy_policy',
            'terms_of_service' => 'Terms of Service',
            'privacy_policy' => 'Privacy Policy',
        ],

    ],

    'messages' => [
        'throttled' => 'Too many registration attempts. Please try again in :seconds seconds.',
    ],

];
