<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\AfricasTalkingChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment received')
            ->greeting('Hi '.$notifiable->name)
            ->line('We received your payment of R'.number_format((float) $this->payment->amount, 2).' for booking #'.$this->payment->booking_id.'.')
            ->line('Thank you for using Autoplug.');
    }

    public function toSms(object $notifiable): string
    {
        return 'Autoplug: Payment of R'.number_format((float) $this->payment->amount, 2).' received. Thank you.';
    }
}
