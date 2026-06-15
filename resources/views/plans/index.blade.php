<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Service & Maintenance Plans') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($products as $p)
                    <div class="bg-white shadow-sm rounded-2xl p-6 flex flex-col">
                        <span class="self-start text-xs px-2 py-0.5 rounded-full {{ $p->type === 'maintenance' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">{{ $p->type === 'maintenance' ? 'Maintenance Plan' : 'Service Plan' }}</span>
                        <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ $p->name }}</h3>
                        @if ($p->description)<p class="text-sm text-gray-600 mt-1">{{ $p->description }}</p>@endif
                        <p class="text-xs text-gray-500 mt-3">Eligibility: up to {{ number_format((int) $p->max_km) }} km &amp; {{ (int) $p->max_age_years }} years.@if ($p->requires_full_history) Full service history required.@endif</p>
                        @php $from = $p->pricingTiers->min('monthly_price'); @endphp
                        @if ($from)<p class="mt-3 text-gray-900"><span class="text-2xl font-bold">R{{ number_format((float) $from, 2) }}</span><span class="text-sm text-gray-500">/mo from</span></p>@endif
                        <a href="{{ route('plans.apply', $p) }}" class="mt-4 inline-flex justify-center items-center px-4 py-2 bg-violet-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Apply now</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No plans are available right now. Please check back soon.</p>
                @endforelse
            </div>

            @if ($subscriptions->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">My plans</h3>
                    <div class="divide-y divide-gray-100">
                        @foreach ($subscriptions as $s)
                            @php $next = $s->installments->whereIn('status', ['pending', 'overdue'])->sortBy('due_date')->first(); @endphp
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $s->product?->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $s->vehicle?->make }} {{ $s->vehicle?->model }} • ends {{ $s->end_date?->format('d M Y') }}</div>
                                    @if ($next)<div class="text-xs {{ $next->status === 'overdue' ? 'text-red-600' : 'text-gray-500' }}">Next: R{{ number_format((float) $next->amount_due, 2) }} due {{ $next->due_date?->format('d M Y') }}</div>@endif
                                </div>
                                <div class="text-right">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['active' => 'bg-green-100 text-green-800', 'suspended' => 'bg-red-100 text-red-700', 'completed' => 'bg-gray-100 text-gray-600', 'pending' => 'bg-amber-100 text-amber-700'][$s->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $s->status === 'pending' ? 'Awaiting payment' : ucfirst($s->status) }}</span>
                                    <div class="mt-1"><a href="{{ route('plans.subscriptions.show', $s) }}" class="text-sm text-violet-600 hover:underline">Manage →</a></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($applications->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">My applications</h3>
                    <div class="divide-y divide-gray-100">
                        @foreach ($applications as $a)
                            <a href="{{ route('plans.applications.show', $a) }}" class="flex items-center justify-between py-3 hover:bg-gray-50 px-2 rounded">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $a->product?->name ?? 'Plan' }}</div>
                                    <div class="text-xs text-gray-500">{{ $a->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
