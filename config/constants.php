<?php

declare(strict_types=1);

return [

    'default_icon' => env('HABITS_DEFAULT_ICON', 'lucide-activity-square'),

    'registration_enabled' => (bool) env('HABITS_REGISTRATION_ENABLED', false),

    'admin' => [

        'name' => env('HABITS_ADMIN_NAME', ''),

        'email' => env('HABITS_ADMIN_EMAIL', ''),

    ],

    'default_timezone' => env('DEFAULT_TIMEZONE', 'America/New_York'),

];
