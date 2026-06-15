<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = $request->user()->vehicles()->withCount(['planSubscriptions' => fn ($q) => $q->whereIn('status', ['active', 'suspended', 'pending'])])->latest()->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->vehicles()->create($this->validateData($request));

        return back()->with('status', 'Vehicle added.');
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);
        $vehicle->update($this->validateData($request));

        return back()->with('status', 'Vehicle updated.');
    }

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeVehicle($request, $vehicle);

        if ($vehicle->planSubscriptions()->whereIn('status', ['active', 'suspended', 'pending'])->exists()) {
            return back()->with('error', 'This vehicle has a plan and cannot be deleted.');
        }

        $vehicle->delete();

        return back()->with('status', 'Vehicle removed.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'registration' => ['nullable', 'string', 'max:30'],
            'odometer_km' => ['nullable', 'integer', 'min:0', 'max:2000000'],
            'first_registered_on' => ['nullable', 'date', 'before_or_equal:today'],
        ]);
    }

    private function authorizeVehicle(Request $request, Vehicle $vehicle): void
    {
        abort_unless((int) $vehicle->user_id === (int) $request->user()->id, 403);
    }
}
