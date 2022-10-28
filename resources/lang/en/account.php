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

    '2fa' => [
        'title' => 'Two Factor Authentication',
        'description' => 'Add additional security to your account using two factor authentication.',

        'card_description' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',

        'setup_key' => 'Setup key',

        'enabled' => [
            'title' => 'You have enabled two factor authentication.',
            'description' => 'Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.',
            'store_codes' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.',
            'show_codes' => 'Show Recovery Codes',
            'hide_codes' => 'Hide Recovery Codes',
        ],

        'disabled' => [
            'title' => 'You have not enabled two factor authentication.',
        ],

        'finish_enabling' => [
            'title' => 'Finish enabling two factor authentication.',
            'description' => 'To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.',
        ],

        'notify' => [
            'disabled' => 'Two factor authentication has been disabled.',
        ],

        'recovery_code' => [
            'regenerated' => 'Recovery codes regenerated.',
        ],

        'confirmation' => [
            'success_notification' => 'Code verified. Two factor authentication enabled.',
            'invalid_code' => 'The code you have entered is invalid.',
        ],

        'columns' => [
            '2fa_code' => 'Code',
            '2fa_recovery_code' => 'Recovery Code',
        ],

        'actions' => [
            'enable' => 'Enable',
            'regenerate_codes' => 'Regenerate Codes',
            'disable' => 'Disable',
            'confirm_finish' => 'Confirm & finish',
            'cancel_setup' => 'Cancel setup',
        ],
    ],
];
