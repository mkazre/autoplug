<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PlanRedemption;
use App\Models\PlanSubscription;
use App\Models\User;
use App\Notifications\RedemptionUpdate;
use App\Support\PlanCoverage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GarageRedemptionController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $garageId = $request->user()->garage?->id;
        abort_unless($booking->branch && (int) $booking->branch->garage_id === (int) $garageId, 403);

        $booking->loadMissing('quote.quoteRequestGarage.quoteRequest');
        $vehicleId = $booking->quote?->quoteRequestGarage?->quoteRequest?->vehicle_id;

        $subscription = $vehicleId
            ? PlanSubscription::where('vehicle_id', $vehicleId)->where('user_id', $booking->user_id)
                ->where('status', 'active')->with('product.benefitItems')->first()
            : null;
        abort_unless($subscription, 422, 'No active plan coverage on this vehicle.');

        $data = $request->validate([
            'redemption_type' => ['required', 'in:service,part_replacement'],
            'description' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:120'],
            'items.*.qty' => ['required', 'numeric', 'min:1'],
            'items.*.cost' => ['required', 'numeric', 'min:0'],
        ]);

        $benefitByName = $subscription->product->benefitItems->keyBy('item_name');
        foreach ($data['items'] as $line) {
            $benefit = $benefitByName->get($line['item_name']);
            if ($benefit && ! PlanCoverage::canClaim($subscription, $benefit)) {
                return back()->with('error', 'Coverage limit already reached for "'.$line['item_name'].'".');
            }
        }

        $amount = collect($data['items'])->sum(fn ($i) => (float) $i['cost']);

        $redemption = PlanRedemption::create([
            'plan_subscription_id' => $subscription->id,
            'branch_id' => $booking->branch_id,
            'booking_id' => $booking->id,
            'redemption_type' => $data['redemption_type'],
            'description' => $data['description'] ?? null,
            'items_json' => $data['items'],
            'amount_claimed' => $amount,
            'status' => 'pending',
            'redeemed_at' => now(),
        ]);

        User::role('admin')->get()->each(fn ($a) => $a->notify(new RedemptionUpdate($redemption, 'submitted', url('/admin/plan-redemptions'))));

        return back()->with('status', 'Coverage claim submitted for review.');
    }
}
