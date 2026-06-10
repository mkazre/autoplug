<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.settings', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'default_radius' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $user = $request->user();
        $prefs = $user->preferences ?? [];
        $prefs['notify_email'] = $request->boolean('notify_email');
        $prefs['notify_sms'] = $request->boolean('notify_sms');

        if ($request->filled('default_radius')) {
            $prefs['default_radius'] = (int) $request->input('default_radius');
        } else {
            unset($prefs['default_radius']);
        }

        $user->update(['preferences' => $prefs]);

        return back()->with('status', 'Preferences saved.');
    }
}
