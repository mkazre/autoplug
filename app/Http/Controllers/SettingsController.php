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
        ]);

        unset($data['logo'], $data['favicon']);

        foreach ($data as $key => $value) {
            Settings::set($key, $value ?? '');
        }

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
