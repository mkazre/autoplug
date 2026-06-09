<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My bookings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                @if ($bookings->isEmpty())
                    <p class="text-sm text-gray-500">No bookings yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($bookings as $b)
                            <a href="{{ route('bookings.show', $b) }}" class="flex items-center justify-between py-3 px-2 hover:bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $b->branch?->garage?->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $b->scheduled_at?->format('D, d M Y H:i') }}</div>
                                </div>
                                <x-booking-status :status="$b->status" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
