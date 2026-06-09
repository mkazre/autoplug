<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">Quote requests</h3>

                @if ($requests->isEmpty())
                    <p class="text-sm text-gray-500">No quote requests yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($requests as $rg)
                            <a href="{{ route('garage.requests.show', $rg) }}" class="flex items-center justify-between py-3 px-2 hover:bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $rg->quoteRequest?->service?->name ?? 'General request' }}</div>
                                    <div class="text-xs text-gray-500">{{ $rg->branch?->name }} • {{ $rg->created_at->diffForHumans() }}</div>
                                </div>
                                @if ($rg->quote)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800">Quoted — R{{ number_format($rg->quote->total_price, 2) }}</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">New</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
