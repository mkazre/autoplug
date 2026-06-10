<?php

namespace App\Notifications\Channels;

use App\Support\Settings;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AfricasTalkingChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $to = $this->normalize($notifiable->phone ?? null);
        if (! $to) {
            return;
        }

        $apiKey = Settings::get('at_api_key', config('africastalking.api_key'));
        if (empty($apiKey)) {
            Log::info('AfricasTalking: no API key set, skipping SMS.');
            return;
        }

        $username = Settings::get('at_username', config('africastalking.username'));
        $sandbox = Settings::bool('at_sandbox', (bool) config('africastalking.sandbox'));
        $from = config('africastalking.from');
        $message = $notification->toSms($notifiable);
        $base = $sandbox ? 'https://api.sandbox.africastalking.com' : 'https://api.africastalking.com';

        try {
            $resp = Http::asForm()->withHeaders([
                'apiKey' => $apiKey,
                'Accept' => 'application/json',
            ])->post($base.'/version1/messaging', array_filter([
                'username' => $username,
                'to' => $to,
                'message' => $message,
                'from' => $from ?: null,
            ]));

            if ($resp->failed()) {
                Log::warning('AfricasTalking SMS failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            }
        } catch (\Throwable $e) {
            Log::warning('AfricasTalking SMS error: '.$e->getMessage());
        }
    }

    private function normalize(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }
        $phone = preg_replace('/\s+/', '', $phone);
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        if (str_starts_with($phone, '0')) {
            return '+27'.substr($phone, 1);
        }
        if (str_starts_with($phone, '27')) {
            return '+'.$phone;
        }

        return $phone;
    }
}
