<?php

namespace App\Notifications\Channels;

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

        $cfg = config('africastalking');
        if (empty($cfg['api_key'])) {
            Log::info('AfricasTalking: no API key set, skipping SMS.');
            return;
        }

        $message = $notification->toSms($notifiable);
        $base = $cfg['sandbox'] ? 'https://api.sandbox.africastalking.com' : 'https://api.africastalking.com';

        try {
            $resp = Http::asForm()->withHeaders([
                'apiKey' => $cfg['api_key'],
                'Accept' => 'application/json',
            ])->post($base.'/version1/messaging', array_filter([
                'username' => $cfg['username'],
                'to' => $to,
                'message' => $message,
                'from' => $cfg['from'] ?: null,
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
