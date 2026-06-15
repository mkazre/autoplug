<?php

namespace App\Support;

use App\Models\PlanProduct;
use App\Models\Vehicle;

class PlanEligibility
{
    /**
     * @return array{eligible: bool, reasons: array<int, string>}
     */
    public static function check(Vehicle $vehicle, PlanProduct $product): array
    {
        $reasons = [];

        $km = (int) ($vehicle->odometer_km ?? 0);
        if ($product->max_km && $km > 0 && $km >= (int) $product->max_km) {
            $reasons[] = 'Vehicle mileage ('.number_format($km).' km) is at or above the '.number_format((int) $product->max_km).' km limit.';
        }

        $age = self::ageYears($vehicle);
        if ($product->max_age_years && $age !== null && $age > (int) $product->max_age_years) {
            $reasons[] = 'Vehicle age ('.$age.' years) exceeds the '.(int) $product->max_age_years.'-year limit.';
        }

        // requires_full_history is enforced at application time (service-history document upload).

        return ['eligible' => empty($reasons), 'reasons' => $reasons];
    }

    public static function ageYears(Vehicle $vehicle): ?int
    {
        if ($vehicle->first_registered_on) {
            return (int) $vehicle->first_registered_on->diffInYears(now());
        }
        if ($vehicle->year) {
            return max(0, (int) now()->year - (int) $vehicle->year);
        }

        return null;
    }
}
