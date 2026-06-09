<?php

return [
    'username' => env('AT_USERNAME', 'sandbox'),
    'api_key' => env('AT_API_KEY', ''),
    'from' => env('AT_FROM', ''),
    'sandbox' => (bool) env('AT_SANDBOX', true),
];
