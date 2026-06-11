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
        'admin_frequency' => 'immediate',   // 'immediate' | 'daily' | 'weekly'
        'digest_time' => '09:00',
        'digest_day' => 'monday',
    ],

    'spam' => [
        'captcha' => [
            'provider' => 'none', // 'turnstile' | 'none'
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
        'comments' => 15,
        'votes' => 30,
    ],

    'threading' => [
        'max_depth' => 3,
    ],

    'display' => [
        'show_comment_count_on_cards' => true,
        'show_comment_count_on_articles' => true,
    ],

    'voting' => [
        'allow_self_vote' => true,
    ],

    'editing' => [
        'window_minutes' => 15,
        'max_comment_length' => 5000,
    ],
];
