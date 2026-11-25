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

    // Stripe Checkout en modo test (rama 11). Las claves se leen del .env:
    // nunca se escriben aquí. En el resto del código: config('services.stripe.secret').
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        // Precio de la suscripción mensual de cada restaurante (rama 24), en
        // céntimos: 2900 = 29,00 €. (int) porque env() devuelve texto.
        'suscripcion_precio_centimos' => (int) env('STRIPE_SUSCRIPCION_PRECIO_CENTIMOS', 2900),
        // Secreto con el que Stripe firma los avisos del webhook (whsec_...).
        // En local lo da "stripe listen" (Stripe CLI). Rama 24, PR 2.
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
