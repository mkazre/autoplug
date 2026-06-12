<?php

namespace App\Notifications;

use App\Models\Branch;
use App\Models\QuoteRequest;
use App\Notifications\Channels\AfricasTalkingChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuoteRequest extends Notification
{
    use Queueable;

    public function __construct(public QuoteRequest $quoteRequest, public Branch $branch) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
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

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: New quote request for '.$this->branch->name.'. Log in to respond.';
    }

    public function toArray(object $notifiable): array
    {
        $service = $this->quoteRequest->service?->name ?? 'a service';

        return [
            'title' => 'New quote request',
            'body' => $this->branch->name.' — '.$service,
            'url' => url('/garage/requests'),
            'icon' => 'inbox',
        ];
    }
}
