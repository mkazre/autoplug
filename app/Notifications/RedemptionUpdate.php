<?php

namespace App\Notifications;

use App\Models\PlanRedemption;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RedemptionUpdate extends Notification
{
    use Queueable;

    public function __construct(public PlanRedemption $redemption, public string $context, public string $url) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = $this->context === 'approved'
            ? (float) $this->redemption->amount_approved
            : (float) $this->redemption->amount_claimed;
        $mail = (new MailMessage)->greeting('Hi '.$notifiable->name);

        return match ($this->context) {
            'submitted' => $mail->subject('New coverage claim')
                ->line('A coverage claim of R'.number_format($amount, 2).' was submitted for review.')
                ->action('Review claim', $this->url),
            'approved' => $mail->subject('Coverage claim approved')
                ->line('A coverage claim has been approved for R'.number_format($amount, 2).'.')
                ->action('View', $this->url),
            default => $mail->subject('Coverage claim declined')
                ->line('A coverage claim was declined.')
                ->action('View', $this->url),
        };
    }

    public function toSms(object $notifiable): string
    {
        return match ($this->context) {
            'submitted' => 'Autoplug: A new plan coverage claim needs review.',
            'approved' => 'Autoplug: A plan coverage claim of R'.number_format((float) $this->redemption->amount_approved, 2).' was approved.',
            default => 'Autoplug: A plan coverage claim was declined.',
        };
    }

    public function toArray(object $notifiable): array
    {
        $title = match ($this->context) {
            'submitted' => 'New coverage claim',
            'approved' => 'Coverage claim approved',
            default => 'Coverage claim declined',
        };

        return [
            'title' => $title,
            'body' => $this->redemption->branch?->garage?->name ?? 'Plan claim',
            'url' => $this->url,
            'icon' => 'shield',
        ];
    }
}
