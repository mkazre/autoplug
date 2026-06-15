<?php

namespace App\Notifications;

use App\Models\PlanPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanPaymentReceived extends Notification
{
    use Queueable;

    public function __construct(public PlanPayment $payment) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plan payment received')
            ->greeting('Hi '.$notifiable->name)
            ->line('We received your plan payment of R'.number_format((float) $this->payment->amount, 2).'.')
            ->action('View your plan', url('/plans/subscriptions/'.$this->payment->plan_subscription_id));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: Plan payment of R'.number_format((float) $this->payment->amount, 2).' received. Thank you.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan payment received',
            'body' => 'R'.number_format((float) $this->payment->amount, 2).' received',
            'url' => url('/plans/subscriptions/'.$this->payment->plan_subscription_id),
            'icon' => 'check',
        ];
    }
}
