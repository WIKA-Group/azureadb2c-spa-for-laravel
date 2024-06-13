<?php

return [
    'connection' => [
        'client_id' => env('AADB2C_CLIENT_ID'),
        'domain' => env('AADB2C_DOMAIN'),  // {your_domain}.b2clogin.com
        'custom_domain' => env('AADB2C_CUSTOM_DOMAIN'), // Optional: set to use custom domain e.g. login.contoso.com
        'policy' => env('AADB2C_POLICY') ?: 'B2C_1_sign-up_and_sign-in_policy', // Default: 'B2C_1_sign-up_and_sign-in_policy'
        'default_algorithm' => env('AADB2C_DEFAULT_ALGORITHM') ?: 'RS256', // Decoding algorithm JWK key. Default: 'RS256'
    ],

    'table' => [
        'oauth_column' => env('AADB2C_OAUTH_COLUMN') ?: 'oauth_id', // Name of the OAuth ID column. Default 'oauth_id'
    ],
];