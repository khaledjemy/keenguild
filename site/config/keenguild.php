<?php

return [
    'public_path' => env('KEENGUILD_PUBLIC_PATH'),
    'inquiries_enabled' => (bool) env('KEENGUILD_INQUIRIES_ENABLED', false),
    'privacy_url' => env('KEENGUILD_PRIVACY_URL'),
    'agent_api_url' => env('KEENGUILD_AGENT_API_URL'),
    'agent_site_key' => env('KEENGUILD_AGENT_SITE_KEY'),
];
