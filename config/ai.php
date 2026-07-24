<?php

return [
    'service_url'    => env('AI_SERVICE_URL', 'http://localhost:3000'),
    'service_secret' => env('AI_SERVICE_SECRET', 'mealit_secret_2025'),
    'provider'       => env('AI_PROVIDER', 'openai'),
    'openai_key'     => env('OPENAI_API_KEY'),
    'gemini_key'     => env('GEMINI_API_KEY'),
];
