<?php

namespace App\Notifications;

use App\Models\PlanInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentOverdue extends Notification
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
            ->subject('Plan payment overdue')
            ->greeting('Hi '.$notifiable->name)
            ->line('Your plan installment of R'.number_format((float) $this->installment->amount_due, 2).' (due '.$this->installment->due_date?->format('d M Y').') is now overdue.')
            ->line('Please settle it to keep your cover active.')
            ->action('Pay now', url('/plans/subscriptions/'.$this->installment->plan_subscription_id));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: A plan installment of R'.number_format((float) $this->installment->amount_due, 2).' is overdue. Pay to keep cover active.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan payment overdue',
            'body' => 'R'.number_format((float) $this->installment->amount_due, 2).' overdue',
            'url' => url('/plans/subscriptions/'.$this->installment->plan_subscription_id),
            'icon' => 'info',
        ];
    }
}
