<?php

namespace App\Http\Controllers;

use App\Models\Garage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GarageDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $garages = Garage::approved()
            ->whereHas('branches', fn ($q) => $q->where('is_active', true))
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->with(['branches' => fn ($q) => $q->where('is_active', true)])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('name')
            ->get();

        if (Garage::hideContactEnabled()) {
            $visible = Garage::acceptedGarageIdsFor($request->user());
            $garages->each(function ($garage) use ($visible) {
                if (! in_array($garage->id, $visible, true)) {
                    $garage->branches->each(function ($b) {
                        $b->address = null;
                        $b->phone = null;
                    });
                }
            });
        }

        return view('garages.index', compact('garages', 'search'));
    }

    public function show(Request $request, Garage $garage): View
    {
        abort_unless($garage->status === 'approved', 404);

        $garage->load([
            'branches' => fn ($q) => $q->where('is_active', true),
            'branches.services.service',
            'photos',
            'reviews' => fn ($q) => $q->latest()->with('user'),
        ]);
        $garage->loadAvg('reviews', 'rating');
        $garage->loadCount('reviews');

        if (Garage::hideContactEnabled()) {
            $visible = in_array($garage->id, Garage::acceptedGarageIdsFor($request->user()), true);
            if (! $visible) {
                $garage->branches->each(function ($b) {
                    $b->address = null;
                    $b->phone = null;
                });
            }
        }

        return view('garages.show', compact('garage'));
    }
}
