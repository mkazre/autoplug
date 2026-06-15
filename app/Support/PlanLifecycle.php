<?php

namespace App\Support;

use App\Models\PlanApplication;
use App\Models\PlanInstallment;
use App\Models\PlanSubscription;
use App\Notifications\PlanApplicationOutcome;

class PlanLifecycle
{
    public static function approve(PlanApplication $application, ?int $adminId = null): PlanSubscription
    {
        $application->loadMissing('product', 'tier', 'vehicle', 'user');
        $tier = $application->tier;
        $product = $application->product;

        $start = now()->startOfDay();
        $end = $tier ? $start->copy()->addMonths((int) $tier->term_months) : null;

        $isInstallments = $application->payment_method === 'installments' && $tier && (int) $tier->installment_count > 1;
        $total = $tier
            ? ($isInstallments ? (float) $tier->monthly_price * (int) $tier->installment_count : (float) $tier->upfront_price)
            : (float) ($product->base_price ?? 0);

        $subscription = PlanSubscription::create([
            'plan_application_id' => $application->id,
            'user_id' => $application->user_id,
            'vehicle_id' => $application->vehicle_id,
            'plan_product_id' => $application->plan_product_id,
            'pricing_tier_id' => $application->pricing_tier_id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'active',
            'km_at_start' => $application->vehicle?->odometer_km,
            'current_balance' => $total,
            'total_paid' => 0,
            'referral_code' => $application->referral_code,
        ]);

        if ($isInstallments) {
            for ($i = 1; $i <= (int) $tier->installment_count; $i++) {
                PlanInstallment::create([
                    'plan_subscription_id' => $subscription->id,
                    'installment_number' => $i,
                    'amount_due' => (float) $tier->monthly_price,
                    'due_date' => $start->copy()->addMonths($i - 1),
                    'status' => 'pending',
                ]);
            }
        } else {
            PlanInstallment::create([
                'plan_subscription_id' => $subscription->id,
                'installment_number' => 1,
                'amount_due' => $total,
                'due_date' => $start,
                'status' => 'pending',
            ]);
        }

        $application->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);

        $subscription->update(['contract_path' => PlanContract::generate($subscription)]);

        PlanAudit::log($application, 'approved', ['subscription_id' => $subscription->id], $adminId);
        $application->user?->notify(new PlanApplicationOutcome($application->fresh(), 'approved'));

        return $subscription;
    }

    public static function reject(PlanApplication $application, string $reason, ?int $adminId = null): void
    {
        $application->update(['status' => 'rejected', 'reject_reason' => $reason]);
        PlanAudit::log($application, 'rejected', ['reason' => $reason], $adminId);
        $application->user?->notify(new PlanApplicationOutcome($application->fresh(), 'rejected'));
    }

    public static function requestInfo(PlanApplication $application, string $note, ?int $adminId = null): void
    {
        $application->update(['status' => 'under_review', 'reject_reason' => $note]);
        PlanAudit::log($application, 'info_requested', ['note' => $note], $adminId);
        $application->user?->notify(new PlanApplicationOutcome($application->fresh(), 'info_requested'));
    }
}
