<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My plan') }} — {{ $sub->product?->name }}</h2>
    </x-slot>

    @php $unpaid = $sub->installments->whereNotIn('status', ['paid']); @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-2xl p-6">
                <a href="{{ route('plans.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All plans</a>
                <div class="flex items-center justify-between mt-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $sub->product?->name }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['active' => 'bg-green-100 text-green-800', 'suspended' => 'bg-red-100 text-red-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-gray-100 text-gray-600', 'pending' => 'bg-amber-100 text-amber-700'][$sub->status] ?? 'bg-amber-100 text-amber-700' }}">{{ $sub->status === 'pending' ? 'Awaiting payment' : ucfirst($sub->status) }}</span>
                </div>
                <dl class="mt-3 text-sm text-gray-700 grid grid-cols-2 gap-1">
                    <div><dt class="text-gray-500 inline">Vehicle:</dt> {{ $sub->vehicle?->make }} {{ $sub->vehicle?->model }} {{ $sub->vehicle?->registration }}</div>
                    <div><dt class="text-gray-500 inline">Term:</dt> {{ $sub->start_date?->format('d M Y') }} – {{ $sub->end_date?->format('d M Y') }}</div>
                    <div><dt class="text-gray-500 inline">Paid:</dt> R{{ number_format((float) $sub->total_paid, 2) }}</div>
                    <div><dt class="text-gray-500 inline">Balance:</dt> R{{ number_format((float) $sub->current_balance, 2) }}</div>
                </dl>
                @if ($sub->contract_path)
                    <a href="{{ route('plans.contract', $sub) }}" class="mt-3 inline-block text-sm text-violet-600 hover:underline">Download signed contract (PDF)</a>
                @endif
                @php $nearExpiry = $sub->end_date && $sub->end_date->lte(now()->addDays(60)); @endphp
                @if ($sub->product && ($sub->status === 'completed' || $nearExpiry))
                    <a href="{{ route('plans.apply', $sub->product) }}" class="mt-3 ms-3 inline-block text-sm font-medium text-violet-600 hover:underline">Renew this plan &rarr;</a>
                @endif
                @if ($sub->status === 'pending')
                    <div class="mt-3 p-3 bg-amber-50 text-amber-800 text-sm rounded">Your cover activates as soon as your first payment is received. Pay by card below, or submit EFT/deposit proof.</div>
                @elseif ($sub->status === 'suspended')
                    <div class="mt-3 p-3 bg-red-50 text-red-700 text-sm rounded">Your plan is suspended for missed payments. Settle the overdue installment(s) below to reactivate cover.</div>
                @endif
            </div>

            @if ($benefits->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Coverage benefits</h3>
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Item</th><th>Used</th><th>Limit</th><th>Remaining</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($benefits as $b)
                                <tr>
                                    <td class="py-2">{{ $b['name'] }}</td>
                                    <td>{{ $b['status']['used'] + 0 }}</td>
                                    <td>{{ $b['status']['limit'] !== null ? $b['status']['limit'] + 0 : 'Unlimited' }}</td>
                                    <td>{{ $b['status']['remaining'] !== null ? $b['status']['remaining'] + 0 : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-2xl p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Installments</h3>
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">#</th><th>Due</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($sub->installments as $inst)
                            <tr>
                                <td class="py-2">{{ $inst->installment_number }}</td>
                                <td>{{ $inst->due_date?->format('d M Y') }}</td>
                                <td>R{{ number_format((float) $inst->amount_due, 2) }}</td>
                                <td><span class="text-xs px-2 py-0.5 rounded-full {{ ['paid' => 'bg-green-100 text-green-800', 'overdue' => 'bg-red-100 text-red-700', 'failed' => 'bg-red-100 text-red-700'][$inst->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($inst->status) }}</span></td>
                                <td class="text-right">
                                    @if (in_array($inst->status, ['pending', 'overdue']) && in_array($sub->status, ['pending', 'active', 'suspended']))
                                        <form method="POST" action="{{ route('plans.installments.pay', $inst) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-violet-600 text-white rounded-md text-xs font-semibold hover:bg-violet-500">Pay by card</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($unpaid->isNotEmpty() && in_array($sub->status, ['pending', 'active', 'suspended']))
                <div class="bg-white shadow-sm rounded-2xl p-6" x-data="{ amounts: @js($unpaid->mapWithKeys(fn ($i) => [$i->id => (float) $i->amount_due])), instId: '{{ $unpaid->first()?->id }}', amount: {{ (float) $unpaid->first()?->amount_due }} }">
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Pay by EFT / Bank deposit</h3>
                    <p class="text-xs text-gray-500 mb-3">Made a manual payment? Submit your proof and we'll verify it.</p>
                    <form method="POST" action="{{ route('plans.manual-pay') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-700">Installment</label>
                            <select name="installment_id" x-model="instId" @change="amount = amounts[instId]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach ($unpaid as $inst)
                                    <option value="{{ $inst->id }}">#{{ $inst->installment_number }} — R{{ number_format((float) $inst->amount_due, 2) }} (due {{ $inst->due_date?->format('d M Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Method</label>
                            <select name="method" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="eft">EFT / Bank transfer</option>
                                <option value="deposit">Cash / Bank deposit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Amount paid (R)</label>
                            <input name="amount" type="number" step="0.01" min="0" x-model="amount" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Date paid</label>
                            <input name="paid_on" type="date" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Transaction / reference no.</label>
                            <input name="txn_reference" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Proof of payment</label>
                            <input name="proof" type="file" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-600">
                        </div>
                        <div class="sm:col-span-2 text-right">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">Submit proof of payment</button>
                        </div>
                    </form>
                </div>
            @endif

            @if ($sub->payments->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Payment history</h3>
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($sub->payments as $p)
                                <tr>
                                    <td class="py-2">{{ ($p->paid_at ?? $p->paid_on ?? $p->created_at)?->format('d M Y') }}</td>
                                    <td>R{{ number_format((float) $p->amount, 2) }}</td>
                                    <td>{{ strtoupper($p->method ?? $p->gateway) }}</td>
                                    <td class="text-gray-500">{{ $p->txn_reference ?? $p->reference }}</td>
                                    <td><span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'paid' ? 'bg-green-100 text-green-800' : ($p->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $p->status === 'pending' && $p->gateway === 'manual' ? 'Awaiting verification' : ucfirst($p->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
