<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class FleetController extends Controller
{
    public function dashboard(Request $request): View
    {
        $fleet = $this->fleet($request);

        return view('fleet.dashboard', compact('fleet'));
    }

    public function drivers(Request $request): View
    {
        $fleet = $this->fleet($request);
        $drivers = $fleet->members()->where('id', '!=', $fleet->owner_id)->get();

        return view('fleet.drivers', compact('fleet', 'drivers'));
    }

    public function addDriver(Request $request): RedirectResponse
    {
        $fleet = $this->fleet($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'car_owner',
            'phone' => $data['phone'] ?? null,
            'fleet_id' => $fleet->id,
        ]);
        $user->assignRole('car_owner');

        return back()->with('status', 'Driver added.');
    }

    public function removeDriver(Request $request, User $user): RedirectResponse
    {
        $fleet = $this->fleet($request);
        abort_unless((int) $user->fleet_id === (int) $fleet->id && (int) $user->id !== (int) $fleet->owner_id, 403);

        $user->update(['fleet_id' => null]);

        return back()->with('status', 'Driver removed from fleet.');
    }

    private function fleet(Request $request): Fleet
    {
        $fleet = $request->user()->ownedFleet;
        abort_unless($fleet, 403);

        return $fleet;
    }
}
