<?php

namespace App\Notifications;

use App\Models\PlanInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentReminder extends Notification
{
    use Queueable;

    public function __construct(public PlanInstallment $installment) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Plan payment reminder')
            ->greeting('Hi '.$notifiable->name)
            ->line('Your plan installment of R'.number_format((float) $this->installment->amount_due, 2).' is due on '.$this->installment->due_date?->format('d M Y').'.')
            ->action('Pay now', url('/plans/subscriptions/'.$this->installment->plan_subscription_id));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: Plan installment of R'.number_format((float) $this->installment->amount_due, 2).' due '.$this->installment->due_date?->format('d M').'. Log in to pay.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan payment due soon',
            'body' => 'R'.number_format((float) $this->installment->amount_due, 2).' due '.$this->installment->due_date?->format('d M Y'),
            'url' => url('/plans/subscriptions/'.$this->installment->plan_subscription_id),
            'icon' => 'clock',
        ];
    }
}
