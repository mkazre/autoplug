<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $recentRequests = new Collection();
        $upcomingBookings = new Collection();
        $stats = [];

        if ($user->hasRole('car_owner')) {
            $recentRequests = $user->quoteRequests()
                ->with('service')->withCount('requestGarages')->latest()->take(5)->get();

            $upcomingBookings = $user->bookings()
                ->whereIn('status', ['pending', 'confirmed', 'inprogress'])
                ->with('branch.garage')->orderBy('scheduled_at')->take(5)->get();

            $stats = [
                'requests' => $user->quoteRequests()->count(),
                'bookings' => $user->bookings()->count(),
                'upcoming' => $user->bookings()->whereIn('status', ['pending', 'confirmed', 'inprogress'])->count(),
                'vehicles' => $user->vehicles()->count(),
            ];
        }

        return view('dashboard', compact('recentRequests', 'upcomingBookings', 'stats'));
    }
}
