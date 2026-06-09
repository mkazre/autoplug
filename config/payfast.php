<?php

return [
    'sandbox' => (bool) env('PAYFAST_SANDBOX', true),
    'merchant_id' => env('PAYFAST_MERCHANT_ID', '10000100'),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY', '46f0cd694581a'),
    'passphrase' => env('PAYFAST_PASSPHRASE', ''),
];
