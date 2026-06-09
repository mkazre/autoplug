<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FleetVehicleController extends Controller
{
    public function index(Request $request): View
    {
        $fleet = $this->fleet($request);
        $vehicles = $fleet->vehicles()->latest()->get();

        return view('fleet.vehicles.index', compact('vehicles'));
    }

    public function create(Request $request): View
    {
        $this->fleet($request);

        return view('fleet.vehicles.form', ['vehicle' => new Vehicle()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $fleet = $this->fleet($request);
        $fleet->vehicles()->create($this->validated($request) + ['user_id' => $fleet->owner_id]);

        return redirect()->route('fleet.vehicles.index')->with('status', 'Vehicle added.');
    }

    public function edit(Request $request, Vehicle $vehicle): View
    {
        $this->authorizeVehicle($request, $vehicle);

        return view('fleet.vehicles.form', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $vehicle->update($this->validated($request));

        return redirect()->route('fleet.vehicles.index')->with('status', 'Vehicle updated.');
    }

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $vehicle->delete();

        return redirect()->route('fleet.vehicles.index')->with('status', 'Vehicle removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'registration' => ['nullable', 'string', 'max:30'],
        ]);
    }

    private function fleet(Request $request): Fleet
    {
        $fleet = $request->user()->ownedFleet;
        abort_unless($fleet, 403);

        return $fleet;
    }

    private function authorizeVehicle(Request $request, Vehicle $vehicle): void
    {
        $fleet = $this->fleet($request);
        abort_unless((int) $vehicle->fleet_id === (int) $fleet->id, 403);
    }
}
