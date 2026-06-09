<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Booking') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('garage.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All bookings</a>
                <div class="flex items-center justify-between mt-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $booking->user?->name }}</h3>
                    <x-booking-status :status="$booking->status" />
                </div>
                <p class="text-sm text-gray-500">{{ $booking->branch?->name }}</p>
                <p class="text-sm text-gray-700 mt-2">Scheduled: {{ $booking->scheduled_at?->format('D, d M Y H:i') }}</p>
                @if ($booking->quote)
                    <p class="text-sm text-gray-700 mt-1">Amount: R{{ number_format($booking->quote->total_price, 2) }}</p>
                @endif

                <div class="mt-6 flex flex-wrap gap-2">
                    @if ($booking->status === 'pending')
                        <x-booking-action :booking="$booking" action="confirm" label="Confirm booking" color="indigo" />
                        <x-booking-action :booking="$booking" action="cancel" label="Decline" color="red" />
                    @elseif ($booking->status === 'confirmed')
                        <x-booking-action :booking="$booking" action="start" label="Mark in progress" color="indigo" />
                        <x-booking-action :booking="$booking" action="cancel" label="Cancel" color="red" />
                    @elseif ($booking->status === 'inprogress')
                        <x-booking-action :booking="$booking" action="complete" label="Mark completed" color="green" />
                    @else
                        <p class="text-sm text-gray-400">No further actions.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
