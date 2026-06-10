<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Quote;
use App\Notifications\BookingUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with('branch.garage')
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $quote = $this->findQuote($request, (int) $request->query('quote'));
        abort_if($this->hasActiveBooking($quote), 409, 'This quote already has an active booking.');

        return view('bookings.create', compact('quote'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quote_id' => ['required', 'integer', 'exists:quotes,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $quote = $this->findQuote($request, (int) $data['quote_id']);
        abort_if($this->hasActiveBooking($quote), 409, 'This quote already has an active booking.');

        $booking = Booking::create([
            'quote_id' => $quote->id,
            'user_id' => $request->user()->id,
            'branch_id' => $quote->quoteRequestGarage->branch_id,
            'scheduled_at' => $data['scheduled_at'],
            'status' => 'pending',
        ]);

        $quote->quoteRequestGarage->branch->garage->user?->notify(new BookingUpdated($booking, 'requested'));

        return redirect()->route('bookings.show', $booking)->with('status', 'Booking requested — the garage will confirm shortly.');
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);

        $booking->load(['branch.garage', 'quote', 'payment', 'review']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        abort_unless(in_array($booking->status, ['pending', 'confirmed']), 422, 'This booking can no longer be cancelled.');

        $booking->update(['status' => 'cancelled']);
        $booking->loadMissing('branch.garage.user');
        $booking->branch->garage->user?->notify(new BookingUpdated($booking, 'cancelled'));

        return back()->with('status', 'Booking cancelled. You can re-book this quote from your quote request.');
    }

    private function hasActiveBooking(Quote $quote): bool
    {
        return Booking::where('quote_id', $quote->id)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    private function findQuote(Request $request, int $quoteId): Quote
    {
        $quote = Quote::with(['quoteRequestGarage.branch.garage.user', 'quoteRequestGarage.quoteRequest'])
            ->findOrFail($quoteId);

        abort_unless(
            $quote->status === 'accepted'
            && (int) $quote->quoteRequestGarage->quoteRequest->user_id === (int) $request->user()->id,
            403
        );

        return $quote;
    }
}
