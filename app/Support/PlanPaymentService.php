<?php

namespace App\Support;

use App\Models\PlanPayment;
use App\Notifications\PlanPaymentReceived;

class PlanPaymentService
{
    public static function verify(PlanPayment $payment, ?int $adminId = null): void
    {
        if ($payment->status === 'paid') {
            return;
        }
        $payment->loadMissing('installment', 'subscription.user');

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'verified_by' => $adminId,
            'verified_at' => now(),
        ]);

        if ($payment->installment && $payment->installment->status !== 'paid') {
            $payment->installment->update(['status' => 'paid', 'paid_at' => now(), 'plan_payment_id' => $payment->id]);
        }

        if ($subscription = $payment->subscription) {
            $subscription->increment('total_paid', $payment->amount);
            $subscription->decrement('current_balance', $payment->amount);
            if ($subscription->status === 'suspended') {
                $subscription->update(['status' => 'active', 'missed_count' => 0]);
            }
            PlanLifecycle::recordActivation($subscription);
            $subscription->user?->notify(new PlanPaymentReceived($payment));
        }

        PlanAudit::log($payment, 'payment_verified', ['amount' => $payment->amount], $adminId);
    }

    public static function reject(PlanPayment $payment, ?int $adminId = null): void
    {
        $payment->update(['status' => 'failed', 'verified_by' => $adminId, 'verified_at' => now()]);
        PlanAudit::log($payment, 'payment_rejected', [], $adminId);
    }
}
