<?php

namespace App\Notifications;

use App\Models\QuoteRequestGarage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteLost extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequestGarage $qrg) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->qrg->loadMissing('quoteRequest.service');
        $service = $this->qrg->quoteRequest?->service?->name ?? 'a request';

        return (new MailMessage)
            ->subject('Quote result on Autoplug')
            ->greeting('Hi '.$notifiable->name)
            ->line('The customer chose another garage for '.$service.' this time.')
            ->line('Thanks for quoting — better luck on the next one!')
            ->action('View requests', url('/garage/requests'));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: A customer chose another garage for a recent quote. Keep quoting!';
    }

    public function toArray(object $notifiable): array
    {
        $this->qrg->loadMissing('quoteRequest.service');

        return [
            'title' => 'Bid not successful',
            'body' => 'The customer chose another garage for '.($this->qrg->quoteRequest?->service?->name ?? 'a request').'.',
            'url' => url('/garage/requests'),
            'icon' => 'info',
        ];
    }
}
