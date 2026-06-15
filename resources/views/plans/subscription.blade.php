<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My plan') }} — {{ $sub->product?->name }}</h2>
    </x-slot>

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
                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['active' => 'bg-green-100 text-green-800', 'suspended' => 'bg-red-100 text-red-700', 'completed' => 'bg-gray-100 text-gray-600', 'cancelled' => 'bg-gray-100 text-gray-600'][$sub->status] ?? 'bg-amber-100 text-amber-700' }}">{{ ucfirst($sub->status) }}</span>
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
                @if ($sub->status === 'suspended')
                    <div class="mt-3 p-3 bg-red-50 text-red-700 text-sm rounded">Your plan is suspended for missed payments. Settle the overdue installment(s) below to reactivate cover.</div>
                @endif
            </div>

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
                                <td>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['paid' => 'bg-green-100 text-green-800', 'overdue' => 'bg-red-100 text-red-700', 'failed' => 'bg-red-100 text-red-700'][$inst->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($inst->status) }}</span>
                                </td>
                                <td class="text-right">
                                    @if (in_array($inst->status, ['pending', 'overdue']) && in_array($sub->status, ['active', 'suspended']))
                                        <form method="POST" action="{{ route('plans.installments.pay', $inst) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-violet-600 text-white rounded-md text-xs font-semibold hover:bg-violet-500">Pay</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($sub->payments->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Payment history</h3>
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b"><th class="py-2">Date</th><th>Amount</th><th>Reference</th><th>Status</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($sub->payments as $p)
                                <tr>
                                    <td class="py-2">{{ ($p->paid_at ?? $p->created_at)?->format('d M Y H:i') }}</td>
                                    <td>R{{ number_format((float) $p->amount, 2) }}</td>
                                    <td class="text-gray-500">{{ $p->reference }}</td>
                                    <td><span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($p->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
