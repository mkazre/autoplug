<?php

namespace App\Notifications;

use App\Models\PlanSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanSuspended extends Notification
{
    use Queueable;

    public function __construct(public PlanSubscription $subscription) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->subscription->loadMissing('product');

        return (new MailMessage)
            ->subject('Your plan has been suspended')
            ->greeting('Hi '.$notifiable->name)
            ->line('Your '.($this->subscription->product?->name ?? 'plan').' has been suspended due to missed payments.')
            ->line('Settle the overdue installment(s) to reactivate your cover.')
            ->action('Reactivate', url('/plans/subscriptions/'.$this->subscription->id));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: Your plan is suspended for missed payments. Log in to settle and reactivate.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan suspended',
            'body' => 'Missed payments — settle to reactivate',
            'url' => url('/plans/subscriptions/'.$this->subscription->id),
            'icon' => 'info',
        ];
    }
}
