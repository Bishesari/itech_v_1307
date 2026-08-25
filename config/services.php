<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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
    'parsgreen' => [
        'api_key' => env('PARSGREEN_API_KEY'),
        'base_url' => env('PARSGREEN_BASE_URL', 'https://sms.parsgreen.ir'),
        'default_sms_number' => env('PARSGREEN_DEFAULT_SMS_NUMBER'),
        'otp_template_id' => (int) env('PARSGREEN_OTP_TEMPLATE_ID', 0),
        'otp_add_name' => filter_var(env('PARSGREEN_OTP_ADD_NAME', false), FILTER_VALIDATE_BOOLEAN),
    ],
];
