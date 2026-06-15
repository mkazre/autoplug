<?php

namespace App\Notifications;

use App\Models\PlanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(public PlanApplication $application) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing('user', 'product');

        return (new MailMessage)
            ->subject('New plan application')
            ->greeting('Hi '.$notifiable->name)
            ->line($this->application->user?->name.' applied for '.($this->application->product?->name ?? 'a plan').'.')
            ->action('Review application', url('/admin/plan-applications'));
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: New plan application from '.($this->application->user?->name ?? 'a customer').'.';
    }

    public function toArray(object $notifiable): array
    {
        $this->application->loadMissing('user', 'product');

        return [
            'title' => 'New plan application',
            'body' => ($this->application->user?->name ?? 'A customer').' — '.($this->application->product?->name ?? 'plan'),
            'url' => url('/admin/plan-applications'),
            'icon' => 'document',
        ];
    }
}
