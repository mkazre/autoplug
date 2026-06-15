<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\GaragePayout;
use App\Models\PlanDiscountUsage;
use App\Models\User;

class PlanDiscount
{
    /** Best active member discount for a user, or null. */
    public static function for(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $subscription = $user->planSubscriptions()
            ->where('status', 'active')
            ->with('product.discounts')
            ->latest()
            ->first();

        $discount = $subscription?->product?->discounts?->firstWhere('is_active', true);
        if (! $subscription || ! $discount) {
            return null;
        }

        return [
            'subscription_id' => $subscription->id,
            'type' => $discount->discount_type,
            'value' => (float) $discount->discount_value,
            'applies_to' => $discount->applies_to,
        ];
    }

    public static function compute(float $total, array $discount): float
    {
        $amount = $discount['type'] === 'percentage'
            ? $total * ($discount['value'] / 100)
            : $discount['value'];

        return round(min(max($amount, 0), $total), 2);
    }

    /** On payment: log the discount + top the garage up by the discounted amount (Autoplug-funded). */
    public static function realize(?Booking $booking): void
    {
        if (! $booking || (float) $booking->discount_amount <= 0) {
            return;
        }
        if (PlanDiscountUsage::where('booking_id', $booking->id)->exists()) {
            return;
        }

        $booking->loadMissing('branch', 'quote');
        $garageId = $booking->branch?->garage_id;

        PlanDiscountUsage::create([
            'plan_subscription_id' => $booking->plan_subscription_id,
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'quote_id' => $booking->quote_id,
            'garage_id' => $garageId,
            'original_amount' => $booking->quote?->total_price ?? 0,
            'discount_amount' => $booking->discount_amount,
            'net_amount' => $booking->net_amount ?? 0,
        ]);

        if ($garageId) {
            GaragePayout::create([
                'garage_id' => $garageId,
                'type' => 'discount_subsidy',
                'source_id' => $booking->id,
                'amount' => $booking->discount_amount,
                'status' => 'pending',
            ]);
        }
    }
}
