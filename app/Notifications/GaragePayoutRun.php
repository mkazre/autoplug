<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GaragePayoutRun extends Notification
{
    use Queueable;

    public function __construct(public float $total, public int $count, public string $cycle) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payout processed')
            ->greeting('Hi '.$notifiable->name)
            ->line('A payout of R'.number_format($this->total, 2).' ('.$this->count.' item(s)) has been processed in cycle '.$this->cycle.'.')
            ->action('View earnings', url('/garage/referrals'));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: A payout of R'.number_format($this->total, 2).' has been processed.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payout processed',
            'body' => 'R'.number_format($this->total, 2).' ('.$this->count.' item(s))',
            'url' => url('/garage/referrals'),
            'icon' => 'check',
        ];
    }
}
