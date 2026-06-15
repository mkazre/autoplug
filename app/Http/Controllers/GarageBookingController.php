<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PlanRedemption;
use App\Models\PlanSubscription;
use App\Notifications\BookingUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GarageBookingController extends Controller
{
    private const TRANSITIONS = [
        'confirm' => ['from' => ['pending'], 'to' => 'confirmed', 'context' => 'confirmed'],
        'start' => ['from' => ['confirmed'], 'to' => 'inprogress', 'context' => 'inprogress'],
        'complete' => ['from' => ['inprogress'], 'to' => 'completed', 'context' => 'completed'],
        'cancel' => ['from' => ['pending', 'confirmed'], 'to' => 'cancelled', 'context' => 'cancelled'],
    ];

    public function index(Request $request): View
    {
        $branchIds = $request->user()->garage->branches()->pluck('id');

        $bookings = Booking::whereIn('branch_id', $branchIds)
            ->with(['branch', 'user', 'quote'])
            ->latest()
            ->get();

        return view('garage.bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load(['branch.garage', 'user', 'quote.quoteRequestGarage.quoteRequest']);

        $vehicleId = $booking->quote?->quoteRequestGarage?->quoteRequest?->vehicle_id;
        $subscription = $vehicleId
            ? PlanSubscription::where('vehicle_id', $vehicleId)->where('user_id', $booking->user_id)
                ->where('status', 'active')->with('product.benefitItems')->first()
            : null;

        $claims = PlanRedemption::where('booking_id', $booking->id)->latest()->get();

        return view('garage.bookings.show', compact('booking', 'subscription', 'claims'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        $action = $request->validate([
            'action' => ['required', 'in:confirm,start,complete,cancel'],
        ])['action'];

        $t = self::TRANSITIONS[$action];

        if (! in_array($booking->status, $t['from'])) {
            return back()->with('error', 'That action is no longer available — the booking is already "'.$booking->status.'".');
        }

        $booking->update(['status' => $t['to']]);
        $booking->loadMissing('user');
        $booking->user?->notify(new BookingUpdated($booking, $t['context']));

        return back()->with('status', 'Booking marked as '.$t['to'].'.');
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        $garageId = $request->user()->garage?->id;
        abort_unless($booking->branch && (int) $booking->branch->garage_id === (int) $garageId, 403);
    }
}
