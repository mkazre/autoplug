<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\QuoteRequestGarage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GarageController extends Controller
{
    public function dashboard(Request $request): View
    {
        $garage = $request->user()->garage;
        $branchIds = $garage->branches()->pluck('id');

        $newRequestsCount = QuoteRequestGarage::whereIn('branch_id', $branchIds)->where('status', 'pending')->count();

        $upcomingBookings = Booking::whereIn('branch_id', $branchIds)
            ->whereIn('status', ['pending', 'confirmed', 'inprogress'])
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->with(['user', 'branch'])->orderBy('scheduled_at')->take(5)->get();

        $recentRequests = QuoteRequestGarage::whereIn('branch_id', $branchIds)
            ->with(['quoteRequest.service', 'branch', 'quote'])->latest()->take(5)->get();

        $stats = [
            'branches' => $branchIds->count(),
            'new_requests' => $newRequestsCount,
            'upcoming' => $upcomingBookings->count(),
            'total_bookings' => Booking::whereIn('branch_id', $branchIds)->count(),
            'reviews' => $garage->reviews()->count(),
            'avg_rating' => round((float) $garage->reviews()->avg('rating'), 1),
        ];

        return view('garage.dashboard', compact('garage', 'newRequestsCount', 'upcomingBookings', 'recentRequests', 'stats'));
    }

    public function editProfile(Request $request): View
    {
        $garage = $request->user()->garage;

        return view('garage.profile', compact('garage'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $garage = $request->user()->garage;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($garage->logo) {
                Storage::disk('public')->delete($garage->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $garage->update($validated);

        return back()->with('status', 'profile-updated');
    }
}
