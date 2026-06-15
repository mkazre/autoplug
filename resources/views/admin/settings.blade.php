@php
    $get = fn ($k, $d = null) => \App\Support\Settings::get($k, $d);
    $bool = fn ($k, $d = false) => \App\Support\Settings::bool($k, $d);
    $dhm = fn ($m) => [intdiv((int) $m, 1440), intdiv((int) $m % 1440, 60), (int) $m % 60];
    [$aD, $aH, $aM] = $dhm($get('quote_accept_window_minutes', 2880));
    [$gD, $gH, $gM] = $dhm($get('garage_response_window_minutes', 2880));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Platform settings — {{ $get('brand_name', 'Autoplug') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-lg font-bold">Platform settings</h1>
            <a href="{{ url('/admin') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to admin</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 text-sm rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded">
                <ul class="list-disc ps-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('platform.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">General</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Brand name</label>
                        <input name="brand_name" value="{{ old('brand_name', $get('brand_name')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Currency symbol</label>
                        <input name="currency_symbol" value="{{ old('currency_symbol', $get('currency_symbol')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Primary color</label>
                        <input name="primary_color" type="color" value="{{ old('primary_color', $get('primary_color')) }}" class="mt-1 w-full h-10 border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">Branding</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm text-gray-700">Logo (PNG/JPG/SVG)</label>
                        @if ($get('logo'))
                            <img src="{{ asset('storage/'.$get('logo')) }}" alt="Logo" class="h-12 mt-2 mb-1">
                        @endif
                        <input name="logo" type="file" accept="image/*" class="mt-1 block text-sm text-gray-600">
                        <label class="block text-sm text-gray-700 mt-3">Logo height (px)</label>
                        <input name="logo_height" type="number" min="10" max="300" value="{{ old('logo_height', $get('logo_height', 40)) }}" class="mt-1 w-32 border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Favicon (PNG/ICO/SVG, small)</label>
                        @if ($get('favicon'))
                            <img src="{{ asset('storage/'.$get('favicon')) }}" alt="Favicon" class="h-8 mt-2 mb-1">
                        @endif
                        <input name="favicon" type="file" accept=".png,.ico,.svg,image/*" class="mt-1 block text-sm text-gray-600">
                    </div>
                </div>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">Search</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Default radius (km)</label>
                        <input name="default_radius" type="number" value="{{ old('default_radius', $get('default_radius')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Min radius (km)</label>
                        <input name="radius_min" type="number" value="{{ old('radius_min', $get('radius_min')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Max radius (km)</label>
                        <input name="radius_max" type="number" value="{{ old('radius_max', $get('radius_max')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    </div>
                </div>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">Quote timers</h2>
                <p class="text-sm text-gray-500 mb-4">How long quotes and requests stay live. Set in days, hours and minutes. Leave all at zero to fall back to 48 hours.</p>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer acceptance window</label>
                        <p class="text-xs text-gray-500 mb-2">Time a customer has to accept a garage's quote before that quote locks.</p>
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs text-gray-500">Days</label>
                                <input name="accept_days" type="number" min="0" max="365" value="{{ old('accept_days', $aD) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Hours</label>
                                <input name="accept_hours" type="number" min="0" max="23" value="{{ old('accept_hours', $aH) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Minutes</label>
                                <input name="accept_minutes" type="number" min="0" max="59" value="{{ old('accept_minutes', $aM) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Garage response window</label>
                        <p class="text-xs text-gray-500 mb-2">Time a garage has to submit a quote after receiving a request.</p>
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs text-gray-500">Days</label>
                                <input name="garage_days" type="number" min="0" max="365" value="{{ old('garage_days', $gD) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Hours</label>
                                <input name="garage_hours" type="number" min="0" max="23" value="{{ old('garage_hours', $gH) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Minutes</label>
                                <input name="garage_minutes" type="number" min="0" max="59" value="{{ old('garage_minutes', $gM) }}" class="mt-1 w-24 border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <label class="flex items-start gap-2 mt-5 pt-4 border-t border-gray-100">
                    <input type="hidden" name="lock_quote_after_accept" value="0">
                    <input type="checkbox" name="lock_quote_after_accept" value="1" class="mt-0.5 rounded border-gray-300 text-violet-600" @checked($bool('lock_quote_after_accept', true))>
                    <span class="text-sm text-gray-700">Lock the garage quote form once a customer accepts a quote for the request <span class="block text-xs text-gray-500">Prevents a garage editing or adding items to an already-awarded quote.</span></span>
                </label>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">Service &amp; Maintenance Plans</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Reminder lead (days before due)</label>
                        <input name="plan_reminder_lead_days" type="number" min="0" max="30" value="{{ old('plan_reminder_lead_days', $get('plan_reminder_lead_days', 3)) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Grace period (days)</label>
                        <input name="plan_grace_days" type="number" min="0" max="90" value="{{ old('plan_grace_days', $get('plan_grace_days', 7)) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Suspend after N missed</label>
                        <input name="plan_suspend_after_missed" type="number" min="1" max="12" value="{{ old('plan_suspend_after_missed', $get('plan_suspend_after_missed', 2)) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Referral reward type</label>
                        @php $rrt = $get('plan_referral_reward_type', 'fixed'); @endphp
                        <select name="plan_referral_reward_type" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="fixed" @selected($rrt === 'fixed')>Fixed amount (R)</option>
                            <option value="percentage" @selected($rrt === 'percentage')>Percentage of first payment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Referral reward value</label>
                        <input name="plan_referral_reward_value" type="number" step="0.01" min="0" value="{{ old('plan_referral_reward_value', $get('plan_referral_reward_value', 100)) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Payout cycle</label>
                        @php $pc = $get('plan_payout_cycle', 'monthly'); @endphp
                        <select name="plan_payout_cycle" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="weekly" @selected($pc === 'weekly')>Weekly</option>
                            <option value="monthly" @selected($pc === 'monthly')>Monthly</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">Privacy</h2>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="hide_contact_until_accepted" value="0">
                    <input type="checkbox" name="hide_contact_until_accepted" value="1" class="rounded border-gray-300 text-violet-600"@checked($bool('hide_contact_until_accepted', true))>
                    <span class="text-sm text-gray-700">Hide garage phone, email &amp; address from customers until they have an accepted quote</span>
                </label>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">PayFast</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Merchant ID</label>
                        <input name="payfast_merchant_id" value="{{ old('payfast_merchant_id', $get('payfast_merchant_id', config('payfast.merchant_id'))) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Merchant key</label>
                        <input name="payfast_merchant_key" value="{{ old('payfast_merchant_key', $get('payfast_merchant_key', config('payfast.merchant_key'))) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Passphrase</label>
                        <input name="payfast_passphrase" value="{{ old('payfast_passphrase', $get('payfast_passphrase')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 mt-3">
                    <input type="hidden" name="payfast_sandbox" value="0">
                    <input type="checkbox" name="payfast_sandbox" value="1" class="rounded border-gray-300 text-violet-600" @checked($bool('payfast_sandbox', config('payfast.sandbox')))>
                    <span class="text-sm text-gray-700">Sandbox mode</span>
                </label>
            </section>

            <section class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-medium text-gray-900 mb-4">SMS (Africa's Talking)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700">Username</label>
                        <input name="at_username" value="{{ old('at_username', $get('at_username', config('africastalking.username'))) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">API key</label>
                        <input name="at_api_key" value="{{ old('at_api_key', $get('at_api_key')) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 mt-3">
                    <input type="hidden" name="at_sandbox" value="0">
                    <input type="checkbox" name="at_sandbox" value="1" class="rounded border-gray-300 text-violet-600" @checked($bool('at_sandbox', config('africastalking.sandbox')))>
                    <span class="text-sm text-gray-700">Sandbox mode</span>
                </label>
            </section>

            <div>
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">Save settings</button>
            </div>
        </form>
    </main>
</body>
</html>
