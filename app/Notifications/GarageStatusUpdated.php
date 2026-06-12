<?php

namespace App\Notifications;

use App\Models\Garage;
use App\Notifications\Channels\AfricasTalkingChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GarageStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Garage $garage, public ?string $context = null) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
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

    public function toSms(object $notifiable): string
    {
        $status = $this->context ?? $this->garage->status;

        return match ($status) {
            'approved' => 'Autoplug: Your garage "'.$this->garage->name.'" has been approved.',
            'rejected' => 'Autoplug: Your garage "'.$this->garage->name.'" was not approved.',
            'info_requested' => 'Autoplug: We need more info for "'.$this->garage->name.'".',
            default => 'Autoplug: update on your garage application.',
        };
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->context ?? $this->garage->status;

        $title = match ($status) {
            'approved' => 'Garage approved',
            'rejected' => 'Garage not approved',
            'info_requested' => 'More information needed',
            default => 'Garage application update',
        };

        return ['title' => $title, 'body' => $this->garage->name, 'url' => url('/dashboard'), 'icon' => 'wrench'];
    }
}
