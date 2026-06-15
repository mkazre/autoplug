<?php

namespace App\Support;

use App\Models\Garage;
use App\Models\GaragePayout;
use App\Models\GarageReferral;
use App\Models\PlanSubscription;
use App\Notifications\ReferralReward;

class PlanReferralService
{
    public static function rewardIfReferred(PlanSubscription $subscription): void
    {
        $code = $subscription->referral_code;
        if (! $code) {
            return;
        }
        if (GarageReferral::where('plan_subscription_id', $subscription->id)->exists()) {
            return;
        }

        $garage = Garage::where('referral_code', $code)->first();
        if (! $garage || (int) $garage->user_id === (int) $subscription->user_id) {
            return; // unknown code or self-referral
        }

        $type = Settings::get('plan_referral_reward_type', 'fixed');
        $value = (float) Settings::get('plan_referral_reward_value', 100);
        $amount = $type === 'percentage'
            ? round((float) $subscription->total_paid * $value / 100, 2)
            : $value;

        $referral = GarageReferral::create([
            'referring_garage_id' => $garage->id,
            'referred_user_id' => $subscription->user_id,
            'plan_subscription_id' => $subscription->id,
            'reward_status' => 'pending',
            'reward_amount' => $amount,
        ]);

        $garage->loadMissing('user');
        $garage->user?->notify(new ReferralReward($referral, 'earned'));
        PlanAudit::log($referral, 'referral_earned', ['amount' => $amount]);
    }

    public static function approve(GarageReferral $referral, ?int $adminId = null): void
    {
        $referral->update(['reward_status' => 'approved']);

        GaragePayout::create([
            'garage_id' => $referral->referring_garage_id,
            'type' => 'referral',
            'source_id' => $referral->id,
            'amount' => $referral->reward_amount,
            'status' => 'pending',
        ]);

        $referral->loadMissing('garage.user');
        $referral->garage?->user?->notify(new ReferralReward($referral, 'approved'));
        PlanAudit::log($referral, 'referral_approved', [], $adminId);
    }
}
