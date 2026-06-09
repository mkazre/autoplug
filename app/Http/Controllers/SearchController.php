<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::orderBy('name')->get();
        $radius = max(5, min(50, (int) $request->input('radius', 15)));
        $serviceId = $request->input('service_id');
        $address = $request->input('address');
        $lat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $lng = $request->filled('lng') ? (float) $request->input('lng') : null;

        $error = null;
        if ($lat === null && $address) {
            $error = 'Please choose a location from the dropdown suggestions, or use "Use my current location".';
        }

        $branches = collect();
        if ($lat !== null && $lng !== null) {
            $branches = Branch::query()
                ->withinRadius($lat, $lng, $radius)
                ->where('is_active', true)
                ->whereHas('garage', fn ($q) => $q->where('status', 'approved'))
                ->when($serviceId, fn ($q) => $q->whereHas('services', fn ($s) => $s->where('service_id', $serviceId)))
                ->with(['garage', 'services.service'])
                ->get();
        }

        $markers = $branches->map(fn ($b) => [
            'lat' => (float) $b->lat,
            'lng' => (float) $b->lng,
            'name' => $b->name,
            'garage' => $b->garage->name,
            'distance' => round($b->distance, 1),
        ])->values();

        return view('search', compact('services', 'radius', 'serviceId', 'address', 'lat', 'lng', 'branches', 'markers', 'error'));
    }
}
