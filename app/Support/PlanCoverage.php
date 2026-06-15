<?php

namespace App\Support;

use App\Models\PlanBenefitItem;
use App\Models\PlanSubscription;

class PlanCoverage
{
    /**
     * How much of a benefit item remains for a subscription in the current period.
     * coverage_unit: per_item (count, lifetime) | per_year (count, per calendar year) | lifetime (count, total).
     *
     * @return array{ok: bool, used: float, remaining: ?float, limit: ?float}
     */
    public static function status(PlanSubscription $subscription, PlanBenefitItem $item): array
    {
        $query = $subscription->redemptions()->where('status', 'approved');

        if ($item->coverage_unit === 'per_year') {
            $query->whereYear('redeemed_at', now()->year);
        }

        $used = 0.0;
        foreach ($query->get() as $redemption) {
            foreach ((array) $redemption->items_json as $line) {
                if (($line['item_name'] ?? null) === $item->item_name) {
                    $used += (float) ($line['qty'] ?? 1);
                }
            }
        }

        $limit = $item->coverage_limit !== null ? (float) $item->coverage_limit : null;
        $remaining = $limit !== null ? max(0, $limit - $used) : null;

        return [
            'ok' => $limit === null || $remaining > 0,
            'used' => $used,
            'remaining' => $remaining,
            'limit' => $limit,
        ];
    }

    public static function canClaim(PlanSubscription $subscription, PlanBenefitItem $item): bool
    {
        return self::status($subscription, $item)['ok'];
    }
}
