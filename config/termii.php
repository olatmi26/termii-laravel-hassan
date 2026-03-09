<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Termii API Key
    |--------------------------------------------------------------------------
    | Your Termii API key found on your Termii dashboard under Settings > API.
    */
    'api_key' => env('TERMII_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Termii Base URL
    |--------------------------------------------------------------------------
    | Your account-specific base URL found on your Termii dashboard.
    | Default: https://api.ng.termii.com
    */
    'base_url' => env('TERMII_BASE_URL', 'https://api.ng.termii.com'),

    /*
    |--------------------------------------------------------------------------
    | Default Sender ID
    |--------------------------------------------------------------------------
    | The default sender ID (alphanumeric) used when sending messages.
    | This must be an approved sender ID on your Termii account.
    */
    'sender_id' => env('TERMII_SENDER_ID', 'N-Alert'),

    /*
    |--------------------------------------------------------------------------
    | Default Channel
    |--------------------------------------------------------------------------
    | The default messaging channel. Options: generic, dnd, whatsapp, tiktok
    */
    'channel' => env('TERMII_CHANNEL', 'generic'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout (seconds)
    |--------------------------------------------------------------------------
    | The number of seconds to wait for the Termii API to respond.
    */
    'timeout' => env('TERMII_TIMEOUT', 30),
];
