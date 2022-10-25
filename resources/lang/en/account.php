<?php

return [
    'profile_information' => [
        'title' => 'Profile Information',
        'description' => 'Update your account\'s profile information and email address',

        'columns' => [
            'photo' => 'Photo',
            'name' => 'Name',
            'email' => 'Email',
        ],

        'submit' => 'Save',
        'updated' => 'Updated',
    ],

    'update_password' => [
        'title' => 'Update password',
        'description' => 'Ensure your account is using a long, random password to stay secure.',

        'columns' => [
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
        ],

        'submit' => 'Save',
        'changed' => 'Password changed.',
    ],
];
