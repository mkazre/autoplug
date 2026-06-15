<?php

namespace App\Notifications;

use App\Models\PlanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanApplicationOutcome extends Notification
{
    use Queueable;

    public function __construct(public PlanApplication $application, public string $outcome) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->application->loadMissing('product');
        $name = $this->application->product?->name ?? 'your plan';
        $url = url('/plans/applications/'.$this->application->id);
        $mail = (new MailMessage)->greeting('Hi '.$notifiable->name);

        return match ($this->outcome) {
            'approved' => $mail->subject('Your plan is approved!')
                ->line('Good news — your application for '.$name.' has been approved and your cover is now active.')
                ->action('View your plan', $url),
            'rejected' => $mail->subject('Update on your plan application')
                ->line('Unfortunately your application for '.$name.' was not approved.')
                ->lineIf((bool) $this->application->reject_reason, 'Reason: '.$this->application->reject_reason)
                ->action('View details', $url),
            default => $mail->subject('We need a little more information')
                ->line('We need more information to process your application for '.$name.'.')
                ->lineIf((bool) $this->application->reject_reason, 'Details: '.$this->application->reject_reason)
                ->action('View application', $url),
        };
    }

    public function toSms(object $notifiable): string
    {
        return match ($this->outcome) {
            'approved' => 'Autoplug: Your plan application is approved and now active!',
            'rejected' => 'Autoplug: Your plan application was not approved. Log in for details.',
            default => 'Autoplug: We need more info for your plan application. Log in to view.',
        };
    }

    public function toArray(object $notifiable): array
    {
        $this->application->loadMissing('product');
        $title = match ($this->outcome) {
            'approved' => 'Plan approved',
            'rejected' => 'Plan not approved',
            default => 'More information needed',
        };

        return [
            'title' => $title,
            'body' => $this->application->product?->name ?? 'Plan application',
            'url' => url('/plans/applications/'.$this->application->id),
            'icon' => 'document',
        ];
    }
}
