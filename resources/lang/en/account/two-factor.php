<?php

return [

    'title' => 'Two Factor Authentication',

    'description' => 'Add additional security to your account using two factor authentication.',

    'note' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',

    'setup_key' => 'Setup key',

    'enabled' => [
        'title' => 'You have enabled two factor authentication.',
        'description' => 'Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.',
        'store_codes' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.',
    ],

    'disabled' => [
        'title' => 'You have not enabled two factor authentication.',
    ],

    'finish_enabling' => [
        'title' => 'Finish enabling two factor authentication.',
        'description' => 'To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.',
    ],

    'buttons' => [
        'enable' => 'Enable',
        'regenerate_codes' => 'Regenerate Codes',
        'disable' => 'Disable',
        'confirm_finish' => 'Confirm & finish',
        'cancel_setup' => 'Cancel setup',
        'show_codes' => 'Show Recovery Codes',
        'hide_codes' => 'Hide Recovery Codes',
    ],

    'fields' => [
        'code' => 'Code',
        'recovery_code' => 'Recovery Code',
    ],

    'messages' => [
        'verified' => 'Code verified. Two factor authentication enabled.',
        'disabled' => 'Two factor authentication has been disabled.',
        'recovery_codes_regenerated' => 'Recovery codes regenerated.',
        'invalid_code' => 'The code you have entered is invalid.',
        'invalid_confirmation_code' => 'The provided two factor authentication code was invalid.',
    ],

];
