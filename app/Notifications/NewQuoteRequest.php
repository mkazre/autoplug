<?php

namespace App\Notifications;

use App\Models\Branch;
use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuoteRequest extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequest $quoteRequest, public Branch $branch) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $service = $this->quoteRequest->service?->name ?? 'a service';

        return (new MailMessage)
            ->subject('New quote request for '.$this->branch->name)
            ->greeting('Hi '.$notifiable->name)
            ->line('You have a new quote request for your branch "'.$this->branch->name.'".')
            ->line('Service: '.$service)
            ->lineIf((bool) $this->quoteRequest->description, 'Details: '.$this->quoteRequest->description)
            ->action('View request', url('/garage/requests'));
    }
}
