@php
    $apGarage = auth()->user()?->garage;
    $apResults = collect();
    if ($apGarage) {
        $apResults = \App\Models\QuoteRequestGarage::query()
            ->whereIn('branch_id', $apGarage->branches()->pluck('id'))
            ->whereIn('status', ['accepted', 'declined'])
            ->whereNull('result_seen_at')
            ->with(['quoteRequest.service', 'quote'])
            ->latest('updated_at')
            ->get();
    }
@endphp
@if ($apResults->isNotEmpty())
    <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900">Quote result{{ $apResults->count() > 1 ? 's' : '' }}</h3>
            <div class="space-y-3 mt-3 max-h-[60vh] overflow-y-auto">
                @foreach ($apResults as $r)
                    @if ($r->status === 'accepted')
                        <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                            <div class="font-semibold text-green-700">🎉 You won this job!</div>
                            <p class="text-sm text-gray-700 mt-1">{{ $r->quoteRequest?->service?->name ?? 'General request' }} — <span class="font-semibold">R{{ number_format($r->quote?->total_price ?? 0, 2) }}</span></p>
                            @if (is_array($r->quote?->items_json))
                                <ul class="mt-2 text-sm text-gray-600 list-disc ps-5">
                                    @foreach ($r->quote->items_json as $it)
                                        <li>{{ $it['description'] ?? '' }} — R{{ number_format((float) ($it['price'] ?? 0), 2) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <a href="{{ route('garage.requests.show', $r) }}" class="inline-block mt-2 text-sm text-violet-600 font-medium hover:underline">View job &amp; customer contact →</a>
                        </div>
                    @else
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="font-semibold text-gray-700">Bid not successful</div>
                            <p class="text-sm text-gray-600 mt-1">The customer chose another garage for {{ $r->quoteRequest?->service?->name ?? 'a request' }}@if ($r->quote) (you quoted R{{ number_format($r->quote->total_price, 2) }})@endif. Keep quoting!</p>
                        </div>
                    @endif
                @endforeach
            </div>
            <form method="POST" action="{{ route('garage.requests.results.ack') }}" class="mt-5 text-right">
                @csrf
                <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold hover:bg-violet-500">Close</button>
            </form>
        </div>
    </div>
@endif
