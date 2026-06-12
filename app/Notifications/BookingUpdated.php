<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Channels\AfricasTalkingChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingUpdated extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking, public string $context) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->booking->loadMissing('branch.garage');
        $garage = $this->booking->branch?->garage?->name ?? 'the garage';
        $when = $this->booking->scheduled_at?->format('D, d M Y H:i');

        $mail = (new MailMessage)->greeting('Hi '.$notifiable->name);

        return match ($this->context) {
            'requested' => $mail->subject('New booking request')
                ->line('New booking request for '.$garage.' on '.$when.'.')
                ->action('View booking', url('/garage/bookings/'.$this->booking->id)),
            'confirmed' => $mail->subject('Booking confirmed')
                ->line('Your booking at '.$garage.' on '.$when.' is confirmed.')
                ->action('View booking', url('/bookings/'.$this->booking->id)),
            'inprogress' => $mail->subject('Service in progress')
                ->line('Your service at '.$garage.' is now in progress.'),
            'completed' => $mail->subject('Service completed')
                ->line('Your service at '.$garage.' is complete. Thank you!'),
            'cancelled' => $mail->subject('Booking cancelled')
                ->line('The booking at '.$garage.' on '.$when.' has been cancelled.'),
            default => $mail->line('Your booking has been updated.'),
        };
    }

    public function toSms(object $notifiable): string
    {
        $this->booking->loadMissing('branch.garage');
        $garage = $this->booking->branch?->garage?->name ?? 'the garage';
        $when = $this->booking->scheduled_at?->format('d M H:i');

        return match ($this->context) {
            'requested' => 'Autoplug: New booking request for '.$garage.' on '.$when.'.',
            'confirmed' => 'Autoplug: Booking at '.$garage.' on '.$when.' confirmed.',
            'inprogress' => 'Autoplug: Your service at '.$garage.' is in progress.',
            'completed' => 'Autoplug: Your service at '.$garage.' is complete.',
            'cancelled' => 'Autoplug: Booking at '.$garage.' on '.$when.' cancelled.',
            default => 'Autoplug: booking update.',
        };
    }

    public function toArray(object $notifiable): array
    {
        $this->booking->loadMissing('branch.garage');
        $garage = $this->booking->branch?->garage?->name ?? 'the garage';
        $url = $this->context === 'requested'
            ? url('/garage/bookings/'.$this->booking->id)
            : url('/bookings/'.$this->booking->id);

        $title = match ($this->context) {
            'requested' => 'New booking request',
            'confirmed' => 'Booking confirmed',
            'inprogress' => 'Service in progress',
            'completed' => 'Service completed',
            'cancelled' => 'Booking cancelled',
            default => 'Booking updated',
        };

        return ['title' => $title, 'body' => $garage, 'url' => $url, 'icon' => 'calendar'];
    }
}
