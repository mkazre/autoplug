<?php

namespace App\Notifications;

use App\Models\PlanPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManualPaymentSubmitted extends Notification
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
            ->subject('Plan payment proof to verify')
            ->greeting('Hi '.$notifiable->name)
            ->line('A '.strtoupper((string) $this->payment->method).' payment of R'.number_format((float) $this->payment->amount, 2).' was submitted with proof for verification.')
            ->action('Verify payment', url('/admin/plan-payments'));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: A manual plan payment needs verification.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan payment to verify',
            'body' => strtoupper((string) $this->payment->method).' R'.number_format((float) $this->payment->amount, 2),
            'url' => url('/admin/plan-payments'),
            'icon' => 'document',
        ];
    }
}
