<?php

namespace App\Http\Controllers;

use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'default_radius' => ['required', 'integer', 'min:1', 'max:200'],
            'radius_min' => ['required', 'integer', 'min:1', 'max:200'],
            'radius_max' => ['required', 'integer', 'min:1', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'logo_height' => ['nullable', 'integer', 'min:10', 'max:300'],
            'payfast_merchant_id' => ['nullable', 'string', 'max:50'],
            'payfast_merchant_key' => ['nullable', 'string', 'max:100'],
            'payfast_passphrase' => ['nullable', 'string', 'max:100'],
            'at_username' => ['nullable', 'string', 'max:100'],
            'at_api_key' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,svg', 'max:512'],
            'accept_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'accept_hours' => ['nullable', 'integer', 'min:0', 'max:23'],
            'accept_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'garage_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'garage_hours' => ['nullable', 'integer', 'min:0', 'max:23'],
            'garage_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
        ]);

        unset(
            $data['logo'], $data['favicon'],
            $data['accept_days'], $data['accept_hours'], $data['accept_minutes'],
            $data['garage_days'], $data['garage_hours'], $data['garage_minutes'],
        );

        foreach ($data as $key => $value) {
            Settings::set($key, $value ?? '');
        }

        // Quote timer windows, stored as total minutes (fall back to 48h if left blank/zero).
        $acceptMinutes = (int) $request->input('accept_days', 0) * 1440
            + (int) $request->input('accept_hours', 0) * 60
            + (int) $request->input('accept_minutes', 0);
        $garageMinutes = (int) $request->input('garage_days', 0) * 1440
            + (int) $request->input('garage_hours', 0) * 60
            + (int) $request->input('garage_minutes', 0);
        Settings::set('quote_accept_window_minutes', $acceptMinutes > 0 ? $acceptMinutes : 2880);
        Settings::set('garage_response_window_minutes', $garageMinutes > 0 ? $garageMinutes : 2880);

        Settings::set('hide_contact_until_accepted', $request->boolean('hide_contact_until_accepted'));
        Settings::set('payfast_sandbox', $request->boolean('payfast_sandbox'));
        Settings::set('at_sandbox', $request->boolean('at_sandbox'));

        if ($request->hasFile('logo')) {
            Settings::set('logo', $request->file('logo')->store('branding', 'public'));
        }
        if ($request->hasFile('favicon')) {
            Settings::set('favicon', $request->file('favicon')->store('branding', 'public'));
        }

        Settings::forgetCache();

        return back()->with('status', 'Settings saved.');
    }
}
