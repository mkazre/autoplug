<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; My bookings</a>
                <div class="flex items-center justify-between mt-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $booking->branch?->garage?->name }}</h3>
                    <x-booking-status :status="$booking->status" />
                </div>
                <p class="text-sm text-gray-500">{{ $booking->branch?->name }}</p>
                <p class="text-sm text-gray-700 mt-2">Scheduled: {{ $booking->scheduled_at?->format('D, d M Y H:i') }}</p>
                @if ($booking->quote)
                    <p class="text-sm text-gray-700 mt-1">Amount: R{{ number_format($booking->quote->total_price, 2) }}</p>
                @endif

                @php $paid = $booking->payment && $booking->payment->status === 'paid'; @endphp
                @if ($paid)
                    <p class="text-sm text-green-600 mt-2 font-medium">Payment received ✓ ({{ $booking->payment->paid_at?->format('d M Y H:i') }})</p>
                @elseif (in_array($booking->status, ['pending', 'confirmed']) && $booking->quote && $booking->quote->total_price > 0)
                    <form method="POST" action="{{ route('bookings.pay', $booking) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Pay R{{ number_format($booking->quote->total_price, 2) }} with PayFast</button>
                    </form>
                @endif

                @if (in_array($booking->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?');">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Cancel booking</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
