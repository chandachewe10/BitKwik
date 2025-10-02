<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

     'lnbits' => [
        'x-api-key' => env('LNBITS_ADMIN_KEY'),
        'base_uri' => env('LNBITS_BASE_URI'),
        'mobile_money' => env('LNBITS_WEBHOOK_MOBILE_MONEY'),
        'bank_transfer' => env('LNBITS_WEBHOOK_BANK_TRANSFER'),
    ],

     'lenco' => [
        'token' => env('LENCO_TOKEN'),
        'base_uri' => env('LENCO_BASE_URI'),
        'wallet_uuid' => env('LENCO_WALLET_UUID'),
        
    ],

];
