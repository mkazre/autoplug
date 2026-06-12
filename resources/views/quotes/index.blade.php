<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My quote requests') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Requests</h3>
                    <a href="{{ route('search') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">New search</a>
                </div>

                @if ($requests->isEmpty())
                    <p class="text-sm text-gray-500">You haven't requested any quotes yet. <a href="{{ route('search') }}" class="text-violet-600 underline">Find a garage</a>.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($requests as $r)
                            <a href="{{ route('quotes.show', $r) }}" class="flex items-center justify-between py-3 hover:bg-gray-50 px-2 rounded">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $r->service?->name ?? 'General request' }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->created_at->diffForHumans() }} • sent to {{ $r->request_garages_count }} garage(s)</div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ ucfirst($r->status) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
