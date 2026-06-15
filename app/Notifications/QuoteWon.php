<?php

namespace App\Notifications;

use App\Models\QuoteRequestGarage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteWon extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequestGarage $qrg) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->qrg->loadMissing('quoteRequest.service', 'quote');
        $service = $this->qrg->quoteRequest?->service?->name ?? 'the request';
        $total = $this->qrg->quote?->total_price;

        return (new MailMessage)
            ->subject('You won a quote on Autoplug!')
            ->greeting('Great news, '.$notifiable->name.'!')
            ->line('The customer accepted your quote'.($total !== null ? ' of R'.number_format((float) $total, 2) : '').' for '.$service.'.')
            ->line("You can now see the customer's contact details to arrange the work.")
            ->action('View the job', url('/garage/requests/'.$this->qrg->id));
    }

    public function toSms(object $notifiable): string
    {
        $total = $this->qrg->quote?->total_price;

        return 'Autoplug: You won a quote'.($total !== null ? ' of R'.number_format((float) $total, 2) : '').'! Log in to see the customer details.';
    }

    public function toArray(object $notifiable): array
    {
        $this->qrg->loadMissing('quoteRequest.service', 'quote');
        $total = $this->qrg->quote?->total_price;

        return [
            'title' => 'You won a quote!'.($total !== null ? ' — R'.number_format((float) $total, 2) : ''),
            'body' => ($this->qrg->quoteRequest?->service?->name ?? 'A request').' — the customer accepted your quote.',
            'url' => url('/garage/requests/'.$this->qrg->id),
            'icon' => 'trophy',
        ];
    }
}
