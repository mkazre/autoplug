<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quote request') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('quotes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; My requests</a>
                <h3 class="text-lg font-medium text-gray-900 mt-2">{{ $quoteRequest->service?->name ?? 'General request' }}</h3>
                @if ($quoteRequest->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $quoteRequest->description }}</p>
                @endif
                @if ($quoteRequest->vehicle)
                    <p class="text-sm text-gray-500 mt-1">Vehicle: {{ $quoteRequest->vehicle->make }} {{ $quoteRequest->vehicle->model }} {{ $quoteRequest->vehicle->year }} {{ $quoteRequest->vehicle->registration }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Garages &amp; responses</h3>
                <div class="divide-y divide-gray-100">
                    @foreach ($quoteRequest->requestGarages as $rg)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">{{ $rg->branch?->garage?->name }}</div>
                                <div class="text-xs text-gray-500">{{ $rg->branch?->name }}</div>
                            </div>
                            <div class="text-right">
                                @if ($rg->quote)
                                    <div class="font-semibold text-gray-900">R{{ number_format($rg->quote->total_price, 2) }}</div>
                                    <div class="text-xs text-green-600">Quote received</div>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Awaiting quote</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
