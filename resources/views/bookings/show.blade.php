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
                    <p class="text-sm text-gray-700 mt-1">Amount: R{{ number_format($booking->net_amount ?? $booking->quote->total_price, 2) }}@if ($booking->discount_amount > 0) <span class="text-xs text-violet-600">(plan discount &minus;R{{ number_format($booking->discount_amount, 2) }})</span>@endif</p>
                @endif

                @php $paid = $booking->payment && $booking->payment->status === 'paid'; @endphp
                @if ($paid)
                    <p class="text-sm text-green-600 mt-2 font-medium">Payment received ✓ ({{ $booking->payment->paid_at?->format('d M Y H:i') }})</p>
                    <a href="{{ route('bookings.invoice', $booking) }}" class="inline-flex items-center mt-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Download invoice (PDF)</a>
                @elseif (in_array($booking->status, ['pending', 'confirmed']) && $booking->quote && $booking->quote->total_price > 0)
                    <form method="POST" action="{{ route('bookings.pay', $booking) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Pay R{{ number_format($booking->net_amount ?? $booking->quote->total_price, 2) }} with PayFast</button>
                    </form>
                @endif

                @if (in_array($booking->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-4" onsubmit="return confirm('Cancel this booking?');">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Cancel booking</button>
                    </form>
                @endif
            </div>

            @if ($booking->status === 'completed')
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-3">Your review</h3>
                    @if ($booking->review)
                        <div class="text-amber-500 text-lg">{!! str_repeat('★', $booking->review->rating) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - $booking->review->rating) !!}</span></div>
                        @if ($booking->review->comment)
                            <p class="text-sm text-gray-600 mt-1">{{ $booking->review->comment }}</p>
                        @endif
                    @else
                        <form method="POST" action="{{ route('bookings.review', $booking) }}" x-data="{ rating: 5 }">
                            @csrf
                            <div class="flex items-center gap-1 text-2xl">
                                <template x-for="n in 5" :key="n">
                                    <button type="button" @click="rating = n" class="focus:outline-none" :class="n <= rating ? 'text-amber-500' : 'text-gray-300'">★</button>
                                </template>
                                <input type="hidden" name="rating" :value="rating">
                            </div>
                            <textarea name="comment" rows="3" placeholder="Tell others about your experience (optional)" class="mt-3 block w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                            <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                            <div class="mt-3">
                                <x-primary-button>Submit review</x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
