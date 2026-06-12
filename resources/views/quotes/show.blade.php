<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quote request') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('quotes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; My requests</a>
                <div class="flex items-center justify-between mt-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $quoteRequest->service?->name ?? 'General request' }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $quoteRequest->status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst($quoteRequest->status) }}</span>
                </div>
                @if ($quoteRequest->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $quoteRequest->description }}</p>
                @endif
                @if ($quoteRequest->vehicle)
                    <p class="text-sm text-gray-500 mt-1">Vehicle: {{ $quoteRequest->vehicle->make }} {{ $quoteRequest->vehicle->model }} {{ $quoteRequest->vehicle->year }} {{ $quoteRequest->vehicle->registration }}</p>
                @endif
            </div>

            <h3 class="text-lg font-medium text-gray-900">Compare quotes</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($quoteRequest->requestGarages as $rg)
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border {{ $rg->quote && $rg->quote->status === 'accepted' ? 'border-green-400' : 'border-transparent' }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $rg->branch?->garage?->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $rg->branch?->name }}</p>
                            </div>
                            @if ($rg->quote && $rg->quote->status === 'accepted')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800">Accepted ✓</span>
                            @elseif ($rg->quote && $rg->quote->status === 'rejected')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Not chosen</span>
                            @endif
                        </div>

                        @if ($rg->quote)
                            <div class="mt-3 text-2xl font-bold text-gray-900">R{{ number_format($rg->quote->total_price, 2) }}</div>

                            @if (is_array($rg->quote->items_json))
                                <ul class="mt-3 divide-y divide-gray-100 text-sm">
                                    @foreach ($rg->quote->items_json as $item)
                                        <li class="flex justify-between py-1">
                                            <span class="text-gray-700">{{ $item['description'] ?? '' }}</span>
                                            <span class="text-gray-900">R{{ number_format((float) ($item['price'] ?? 0), 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if ($rg->quote->valid_until)
                                <p class="text-xs text-gray-500 mt-2">Valid until {{ $rg->quote->valid_until->format('d M Y') }}</p>
                            @endif
                            @if ($rg->quote->notes)
                                <p class="text-sm text-gray-600 mt-2">{{ $rg->quote->notes }}</p>
                            @endif

                            @if ($quoteRequest->status !== 'closed')
                                @if ($rg->quote->expires_at)
                                    <div class="mt-4"
                                         x-data="{ end: {{ $rg->quote->expires_at->getTimestamp() * 1000 }}, now: Date.now(),
                                                   get expired() { return this.now >= this.end; },
                                                   fmt() { let s = Math.max(0, Math.floor((this.end - this.now) / 1000)); let d = Math.floor(s/86400); s -= d*86400; let h = Math.floor(s/3600); s -= h*3600; let m = Math.floor(s/60); s -= m*60; let o = []; if (d) o.push(d+'d'); if (h||d) o.push(h+'h'); o.push(m+'m'); if (!d) o.push(s+'s'); return o.join(' '); } }"
                                         x-init="setInterval(() => now = Date.now(), 1000)">
                                        <p class="text-xs mb-2" :class="expired ? 'text-red-600' : 'text-gray-500'">
                                            <template x-if="!expired"><span>Accept within <span class="font-semibold" x-text="fmt()"></span></span></template>
                                            <template x-if="expired"><span class="font-semibold">This quote has expired.</span></template>
                                        </p>
                                        <form method="POST" action="{{ route('quotes.accept', [$quoteRequest, $rg->quote]) }}" x-show="!expired">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-violet-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Accept this quote</button>
                                        </form>
                                        <div x-show="expired" style="display:none" class="w-full text-center px-4 py-2 bg-gray-100 rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest">Expired — request a new quote</div>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('quotes.accept', [$quoteRequest, $rg->quote]) }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-violet-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Accept this quote</button>
                                    </form>
                                @endif
                            @elseif ($rg->quote->status === 'accepted')
                                @if ($rg->quote->activeBooking)
                                    <a href="{{ route('bookings.show', $rg->quote->activeBooking) }}" class="mt-4 block text-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">View booking</a>
                                @else
                                    <a href="{{ route('bookings.create', ['quote' => $rg->quote->id]) }}" class="mt-4 block text-center px-4 py-2 bg-violet-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Book this service</a>
                                @endif
                            @endif
                        @else
                            <div class="mt-3">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Awaiting quote</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
