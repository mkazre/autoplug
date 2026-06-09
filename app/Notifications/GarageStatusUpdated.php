<?php

namespace App\Notifications;

use App\Models\Garage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GarageStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Garage $garage, public ?string $context = null) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $garage = $this->garage;
        $status = $this->context ?? $garage->status;

        $mail = (new MailMessage)->subject('Update on your garage application: '.$garage->name);

        return match ($status) {
            'approved' => $mail
                ->greeting('Good news, '.$notifiable->name.'!')
                ->line('Your garage "'.$garage->name.'" has been approved and is now listed on Autoplug.')
                ->action('Go to your dashboard', url('/dashboard')),
            'rejected' => $mail
                ->greeting('Hi '.$notifiable->name)
                ->line('Unfortunately your garage "'.$garage->name.'" was not approved.')
                ->lineIf((bool) $garage->admin_notes, 'Reason: '.$garage->admin_notes),
            'info_requested' => $mail
                ->greeting('Hi '.$notifiable->name)
                ->line('We need more information about your garage "'.$garage->name.'" application.')
                ->lineIf((bool) $garage->admin_notes, 'Details: '.$garage->admin_notes),
            default => $mail->line('There is an update on your garage application.'),
        };
    }
}
