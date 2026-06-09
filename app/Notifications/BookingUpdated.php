<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingUpdated extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking, public string $context) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
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
}
