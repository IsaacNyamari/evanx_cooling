<?php

// Site-wide contact details. Read through config() (not env() in views) so they keep
// working after `php artisan config:cache`, and fall back to sensible defaults.
return [
    'phone' => env('PHONE', '+254707856908'),
    // International format, digits only (no +, spaces or dashes). Defaults to the phone number.
    'whatsapp' => preg_replace('/\D+/', '', (string) env('WHATSAPP_NUMBER', env('PHONE', '+254707856908'))),
    'email' => env('CONTACT_EMAIL', 'info@evanxcoolingsystems.co.ke'),
    'facebook' => env('FACEBOOK_URL', 'https://www.facebook.com/evanxcooling'),
    'youtube' => env('YOUTUBE_URL', 'https://www.youtube.com/@EvanxCoolingSystemsNairobikeny'),
];
