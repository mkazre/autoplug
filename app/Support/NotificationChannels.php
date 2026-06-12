<?php

namespace App\Support;

use App\Notifications\Channels\AfricasTalkingChannel;

class NotificationChannels
{
    public static function for(object $notifiable): array
    {
        // Always persist in-app (notification bell); add email/SMS per preference.
        $channels = ['database'];

        $wantsEmail = method_exists($notifiable, 'prefersEmail') ? $notifiable->prefersEmail() : true;
        $wantsSms = method_exists($notifiable, 'prefersSms') ? $notifiable->prefersSms() : true;

        if ($wantsEmail) {
            $channels[] = 'mail';
        }
        if (! empty($notifiable->phone) && $wantsSms) {
            $channels[] = AfricasTalkingChannel::class;
        }

        return $channels;
    }
}
