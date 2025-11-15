<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WorkOS Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for WorkOS authentication.
    | You can find your API Key and Client ID in your WorkOS dashboard.
    |
    */

    'api_key' => env('WORKOS_API_KEY'),

    'client_id' => env('WORKOS_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URI
    |--------------------------------------------------------------------------
    |
    | The callback URL where WorkOS will redirect after authentication.
    | This must match the redirect URI configured in your WorkOS dashboard.
    |
    */

    'redirect_uri' => env('APP_URL') . '/auth/workos/callback',

];



