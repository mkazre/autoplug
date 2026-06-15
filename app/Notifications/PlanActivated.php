<?php

namespace App\Notifications;

use App\Models\PlanSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanActivated extends Notification
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
            ->subject('Your plan is now active')
            ->greeting('Hi '.$notifiable->name)
            ->line('Your '.($this->subscription->product?->name ?? 'plan').' is now active. Your cover has started.')
            ->action('View your plan', url('/plans/subscriptions/'.$this->subscription->id));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: Your plan is now active. Cover has started.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan activated',
            'body' => 'Your cover has started',
            'url' => url('/plans/subscriptions/'.$this->subscription->id),
            'icon' => 'check',
        ];
    }
}
