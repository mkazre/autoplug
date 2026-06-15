<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Application') }} — {{ $application->product?->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-2xl p-6">
                <a href="{{ route('plans.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All plans</a>
                @php
                    $steps = ['submitted' => 'Submitted', 'under_review' => 'Under review', 'approved' => 'Approved', 'active' => 'Active'];
                    $order = ['submitted', 'under_review', 'approved', 'active'];
                    $current = array_search($application->status, $order);
                @endphp
                <div class="flex items-center justify-between mt-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $application->product?->name }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $application->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                </div>

                @if ($application->status === 'rejected')
                    <div class="mt-3 p-3 bg-red-50 text-red-700 text-sm rounded">Not approved.@if ($application->reject_reason) Reason: {{ $application->reject_reason }}@endif</div>
                @else
                    <div class="mt-4 flex items-center gap-2">
                        @foreach ($order as $i => $key)
                            <div class="flex-1 text-center">
                                <div class="h-1.5 rounded-full {{ $current !== false && $i <= $current ? 'bg-violet-600' : 'bg-gray-200' }}"></div>
                                <span class="text-[11px] text-gray-500">{{ $steps[$key] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <dl class="mt-5 text-sm text-gray-700 space-y-1">
                    <div class="flex gap-3"><dt class="w-32 text-gray-500">Vehicle</dt><dd>{{ $application->vehicle?->make }} {{ $application->vehicle?->model }} {{ $application->vehicle?->year }} {{ $application->vehicle?->registration }}</dd></div>
                    <div class="flex gap-3"><dt class="w-32 text-gray-500">Cover</dt><dd>{{ $application->tier?->vehicle_category }} — {{ $application->tier?->term_months }} months</dd></div>
                    <div class="flex gap-3"><dt class="w-32 text-gray-500">Payment</dt><dd>{{ ucfirst($application->payment_method ?? '—') }}</dd></div>
                    <div class="flex gap-3"><dt class="w-32 text-gray-500">Submitted</dt><dd>{{ $application->submitted_at?->format('d M Y H:i') }}</dd></div>
                </dl>

                @if ($application->documents->isNotEmpty())
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-1">Documents</h4>
                        <ul class="text-sm space-y-1">
                            @foreach ($application->documents as $doc)
                                <li><a href="{{ route('plans.documents.show', $doc) }}" target="_blank" class="text-violet-600 hover:underline">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($application->subscription)
                    @php $ssub = $application->subscription; @endphp
                    <div class="mt-5 space-y-1">
                        @if ($ssub->status === 'pending')
                            <p class="text-sm text-amber-700 font-medium">Approved — activate your cover by making your first payment.</p>
                        @elseif ($ssub->status === 'active')
                            <p class="text-sm text-green-700 font-medium">Your plan is active.</p>
                        @else
                            <p class="text-sm text-gray-600 font-medium">Plan status: {{ ucfirst($ssub->status) }}.</p>
                        @endif
                        <a href="{{ route('plans.subscriptions.show', $ssub) }}" class="block text-sm text-violet-600 hover:underline">Manage plan &amp; payments &rarr;</a>
                        @if ($ssub->contract_path)<a href="{{ route('plans.contract', $ssub) }}" class="block text-sm text-violet-600 hover:underline">Download signed contract (PDF)</a>@endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
