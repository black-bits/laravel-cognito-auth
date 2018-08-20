<?php

return [
    // AWS Settings
    'credentials'       => [
        'key'    => env('AWS_COGNITO_KEY', ''),
        'secret' => env('AWS_COGNITO_SECRET', ''),
    ],
    'region'            => env('AWS_COGNITO_REGION', 'us-east-1'),
    'version'           => env('AWS_COGNITO_VERSION', 'latest'),
    'app_client_id'     => env('AWS_COGNITO_CLIENT_ID', ''),
    'app_client_secret' => env('AWS_COGNITO_CLIENT_SECRET', ''),
    'user_pool_id'      => env('AWS_COGNITO_USER_POOL_ID', ''),

    // package configuration
    'use_sso'           => env('USE_SSO', false),
    'sso_user_fields'   => [
        'name',
        'email',
    ],
    'sso_user_model'        => 'App\User',
    'delete_user'           => env('AWS_COGNITO_DELETE_USER', false),
];
