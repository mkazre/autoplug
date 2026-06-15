<?php

namespace App\Support;

use App\Models\GaragePayout;
use App\Models\PlanRedemption;
use App\Notifications\RedemptionUpdate;

class PlanRedemptionService
{
    public static function approve(PlanRedemption $redemption, float $amount, ?int $adminId = null): void
    {
        $redemption->loadMissing('subscription.user', 'branch.garage.user');

        $redemption->update([
            'status' => 'approved',
            'amount_approved' => $amount,
            'reviewed_by' => $adminId,
            'redeemed_at' => $redemption->redeemed_at ?? now(),
        ]);

        if ($garageId = $redemption->branch?->garage_id) {
            GaragePayout::create([
                'garage_id' => $garageId,
                'type' => 'redemption',
                'source_id' => $redemption->id,
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }

        PlanAudit::log($redemption, 'redemption_approved', ['amount' => $amount], $adminId);
        self::notifyOutcome($redemption, 'approved');
    }

    public static function reject(PlanRedemption $redemption, ?int $adminId = null): void
    {
        $redemption->loadMissing('subscription.user', 'branch.garage.user');
        $redemption->update(['status' => 'rejected', 'reviewed_by' => $adminId]);
        PlanAudit::log($redemption, 'redemption_rejected', [], $adminId);
        self::notifyOutcome($redemption, 'rejected');
    }

    private static function notifyOutcome(PlanRedemption $redemption, string $context): void
    {
        $redemption->subscription?->user?->notify(new RedemptionUpdate($redemption, $context, url('/plans/subscriptions/'.$redemption->plan_subscription_id)));
        $redemption->branch?->garage?->user?->notify(new RedemptionUpdate($redemption, $context, url('/garage/bookings/'.$redemption->booking_id)));
    }
}
