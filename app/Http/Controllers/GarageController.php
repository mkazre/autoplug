<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GarageController extends Controller
{
    public function dashboard(Request $request): View
    {
        $garage = $request->user()->garage;

        return view('garage.dashboard', compact('garage'));
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
