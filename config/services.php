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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // 'openwa' => [

    //     'url' => env(
    //         'WHATSAPP_API_URL',
    //         'http://127.0.0.1:2886'
    //     ),

    //     'api_key' => env(
    //         'OPENWA_API_KEY',
    //         ''
    //     ),

    //     'session_id' => env(
    //         'OPENWA_SESSION_ID',
    //         ''
    //     ),

    //     'insert_template' => env(
    //         'OPENWA_INSERT_TEMPLATE',
    //         'task-assigned'
    //     ),

    //     'due_date_template' => env(
    //         'OPENWA_DUE_DATE_TEMPLATE',
    //         'task-due-date'
    //     ),

    // ],
    // 'whatsapp' => [
    //     'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    //     'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    //     'api_version' => env('WHATSAPP_API_VERSION'),
    // ],
    'whatsapp' => [
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v25.0'),
        'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en_US'),
        'task_assigned_template' => env('WHATSAPP_TASK_ASSIGNED_TEMPLATE', 'task_assigned'),
        'task_due_template' => env('WHATSAPP_TASK_DUE_TEMPLATE', 'task_due_reminder'),
    ],

];
