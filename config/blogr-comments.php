<?php

return [
    'enabled' => env('BLOGR_COMMENTS_ENABLED', true),

    'moderation' => [
        'mode' => 'post',           // 'pre' | 'post' | 'trust'
        'trust_threshold' => 3,     // approved comments before trusted
    ],

    'notifications' => [
        'new_comment' => true,
        'reply' => true,
        'owner_email' => null,
    ],

    'spam' => [
        'captcha' => [
            'provider' => 'turnstile', // 'turnstile' | 'none'
            'site_key' => env('TURNSTILE_SITE_KEY', ''),
            'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
        ],
        'stop_forum_spam' => true,
        'akismet' => [
            'enabled' => false,
            'api_key' => env('AKISMET_API_KEY', ''),
        ],
        'local_filters' => [
            'max_links' => 2,
            'blocked_keywords' => [],
        ],
    ],

    'rate_limit' => [
        'comments' => 5,
        'votes' => 30,
    ],

    'threading' => [
        'max_depth' => 3,
    ],

    'editing' => [
        'window_minutes' => 15,
    ],
];
