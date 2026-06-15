<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            @php $link = route('referral.land', $garage->referral_code); @endphp
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ copied: false }">
                <h3 class="text-lg font-medium text-gray-900">Refer customers, earn rewards</h3>
                <p class="text-sm text-gray-500 mt-1">Share your link. When someone signs up for an Autoplug plan through it and activates, you earn a reward.</p>
                <div class="mt-3 flex items-center gap-2">
                    <input type="text" readonly value="{{ $link }}" class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm bg-gray-50" x-ref="link">
                    <button type="button" @click="$refs.link.select(); document.execCommand('copy'); copied = true; setTimeout(() => copied = false, 1500)" class="px-3 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold hover:bg-violet-500" x-text="copied ? 'Copied!' : 'Copy'"></button>
                </div>
                <p class="text-xs text-gray-400 mt-2">Your code: <span class="font-mono font-semibold text-gray-700">{{ $garage->referral_code }}</span></p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Referrals</h3>
                @if ($referrals->isEmpty())
                    <p class="text-sm text-gray-500">No referrals yet.</p>
                @else
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Customer</th><th>Plan</th><th>Reward</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($referrals as $r)
                                <tr>
                                    <td class="py-2">{{ $r->referredUser?->name ?? '—' }}</td>
                                    <td>{{ $r->subscription?->product?->name ?? '—' }}</td>
                                    <td>R{{ number_format((float) $r->reward_amount, 2) }}</td>
                                    <td><span class="text-xs px-2 py-0.5 rounded-full {{ ['paid' => 'bg-green-100 text-green-800', 'approved' => 'bg-blue-100 text-blue-700'][$r->reward_status] ?? 'bg-amber-100 text-amber-700' }}">{{ ucfirst($r->reward_status) }}</span></td>
                                    <td class="text-gray-500">{{ $r->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Earnings &amp; payouts</h3>
                @php $pendingTotal = $payouts->where('status', 'pending')->sum('amount'); $paidTotal = $payouts->where('status', 'paid')->sum('amount'); @endphp
                <div class="flex gap-6 mb-3 text-sm">
                    <div><span class="text-gray-500">Pending:</span> <span class="font-semibold text-gray-900">R{{ number_format((float) $pendingTotal, 2) }}</span></div>
                    <div><span class="text-gray-500">Paid out:</span> <span class="font-semibold text-gray-900">R{{ number_format((float) $paidTotal, 2) }}</span></div>
                </div>
                @if ($payouts->isEmpty())
                    <p class="text-sm text-gray-500">No payouts yet. Approved referrals and coverage claims appear here.</p>
                @else
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Type</th><th>Amount</th><th>Status</th><th>Cycle</th><th>Date</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($payouts as $p)
                                <tr>
                                    <td class="py-2">{{ ucfirst(str_replace('_', ' ', $p->type)) }}</td>
                                    <td>R{{ number_format((float) $p->amount, 2) }}</td>
                                    <td><span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($p->status) }}</span></td>
                                    <td class="text-gray-500">{{ $p->cycle ?? '—' }}</td>
                                    <td class="text-gray-500">{{ ($p->paid_at ?? $p->created_at)?->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
