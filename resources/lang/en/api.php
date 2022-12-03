<?php

return [

    'create' => [
        'title' => 'Create API Token',
        'description' => 'API tokens allow third-party services to authenticate with our application on your behalf.',

        'submit' => 'Create',
    ],

    'update' => [
        'notify' => 'Token updated successfully!',
    ],

    'delete' => [
        'notify' => 'Token deleted',
    ],

    'modal' => [
        'title' => 'API Token',
        'description' => 'Please copy your new API token. For your security, it won\'t be shown again.',

        'buttons' => [
            'close' => 'Close',
        ],
    ],

    'table' => [
        'never' => 'Never',

        'bulk_actions' => [
            'delete' => 'Delete',
        ],
    ],

    'fields' => [
        'token_name' => 'Token Name',
        'permissions' => 'Permissions',
        'last_used_at' => 'Last used',
    ],

];
