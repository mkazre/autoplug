<?php

namespace App\Notifications;

use App\Models\GarageReferral;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralReward extends Notification
{
    use Queueable;

    public function __construct(public GarageReferral $referral, public string $context) {}

    public function via(object $notifiable): array
    {
        return \App\Support\NotificationChannels::for($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = 'R'.number_format((float) $this->referral->reward_amount, 2);
        $mail = (new MailMessage)->greeting('Hi '.$notifiable->name);

        return $this->context === 'approved'
            ? $mail->subject('Referral reward approved')
                ->line('Your referral reward of '.$amount.' has been approved and is queued for payout.')
                ->action('View referrals', url('/garage/referrals'))
            : $mail->subject('You earned a referral reward!')
                ->line('A customer you referred activated a plan — you earned '.$amount.' (pending approval).')
                ->action('View referrals', url('/garage/referrals'));
    }

    public function toSms(object $notifiable): string
    {
        $amount = 'R'.number_format((float) $this->referral->reward_amount, 2);

        return $this->context === 'approved'
            ? 'Autoplug: Your referral reward of '.$amount.' is approved for payout.'
            : 'Autoplug: You earned a referral reward of '.$amount.'!';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->context === 'approved' ? 'Referral reward approved' : 'Referral reward earned',
            'body' => 'R'.number_format((float) $this->referral->reward_amount, 2),
            'url' => url('/garage/referrals'),
            'icon' => 'trophy',
        ];
    }
}
