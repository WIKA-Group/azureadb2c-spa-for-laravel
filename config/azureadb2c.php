<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Azure AD B2C
    |--------------------------------------------------------------------------
    |
    | Connection details for Azure AD B2C.
    |
    | Domain: {your_domain}.b2clogin.com
    | Custom_Domain: Optional - set to use custom domain e.g. login.contoso.com
    | Policy: If no value is given, 'B2C_1_sign-up_and_sign-in_policy' is used.
    | Default_Algorithm: Decoding algorithm JWK key. Default: 'RS256'
    */

    'connection' => [
        'client_id' => env('AADB2C_CLIENT_ID'),
        'domain' => env('AADB2C_DOMAIN'),  // 
        'custom_domain' => env('AADB2C_CUSTOM_DOMAIN'), 
        'policy' => env('AADB2C_POLICY') ?: 'B2C_1_sign-up_and_sign-in_policy',
        'default_algorithm' => env('AADB2C_DEFAULT_ALGORITHM') ?: 'RS256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Name of the OAuth ID column
    |--------------------------------------------------------------------------
    |
    | The column name is used inside the migration and the SsoLoginController.
    |
    | If no name is given, the column 'oauth_id' is used.
    */

    'table' => [
        'oauth_column' => env('AADB2C_OAUTH_COLUMN') ?: 'oauth_id',
    ],
];