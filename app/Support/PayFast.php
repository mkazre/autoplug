<?php

namespace App\Support;

class PayFast
{
    public static function sandbox(): bool
    {
        return Settings::bool('payfast_sandbox', (bool) config('payfast.sandbox'));
    }

    public static function passphrase(): string
    {
        return (string) Settings::get('payfast_passphrase', config('payfast.passphrase'));
    }

    public static function processUrl(): string
    {
        return self::sandbox()
            ? 'https://sandbox.payfast.co.za/eng/process'
            : 'https://www.payfast.co.za/eng/process';
    }

    public static function signature(array $data, ?string $passphrase = ''): string
    {
        $output = '';
        foreach ($data as $key => $val) {
            if ($key === 'signature' || $val === null || $val === '') {
                continue;
            }
            $output .= $key.'='.urlencode(trim((string) $val)).'&';
        }
        $getString = rtrim($output, '&');
        if (! empty($passphrase)) {
            $getString .= '&passphrase='.urlencode(trim($passphrase));
        }

        return md5($getString);
    }

    /** Merge merchant creds (first) + the ordered base fields, then sign. */
    public static function fields(array $base): array
    {
        $data = array_merge([
            'merchant_id' => Settings::get('payfast_merchant_id', config('payfast.merchant_id')),
            'merchant_key' => Settings::get('payfast_merchant_key', config('payfast.merchant_key')),
        ], $base);
        $data['signature'] = self::signature($data, self::passphrase());

        return $data;
    }
}
