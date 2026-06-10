<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 422, 'You can review once the service is completed.');
        abort_if($booking->review()->exists(), 422, 'You have already reviewed this booking.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->loadMissing('branch');

        Review::create([
            'user_id' => $request->user()->id,
            'garage_id' => $booking->branch->garage_id,
            'booking_id' => $booking->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return back()->with('status', 'Thanks for your review!');
    }
}
