<?php

namespace App\Notifications;

use App\Models\QuoteRequestGarage;
use App\Notifications\Channels\AfricasTalkingChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteReady extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequestGarage $qrg) {}

    public function via(object $notifiable): array
    {
        return $notifiable->phone ? ['mail', AfricasTalkingChannel::class] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $garage = $this->qrg->branch?->garage?->name ?? 'A garage';
        $total = $this->qrg->quote?->total_price;

        return (new MailMessage)
            ->subject('A quote is ready for your request')
            ->greeting('Hi '.$notifiable->name)
            ->line($garage.' has sent you a quote'.($total !== null ? ' of R'.number_format((float) $total, 2) : '').'.')
            ->action('View your quotes', url('/quotes/'.$this->qrg->quote_request_id));
    }

    public function toSms(object $notifiable): string
    {
        $total = $this->qrg->quote?->total_price;

        return 'Autoplug: A quote'.($total !== null ? ' of R'.number_format((float) $total, 2) : '').' is ready. Log in to view.';
    }
}
