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
                @if ($booking->user?->phone)
                    <p class="text-sm text-gray-700 mt-2">Phone: <a href="tel:{{ $booking->user->phone }}" class="text-violet-600 hover:underline">{{ $booking->user->phone }}</a></p>
                @endif
                <p class="text-sm text-gray-700 mt-1">Email: <a href="mailto:{{ $booking->user?->email }}" class="text-violet-600 hover:underline">{{ $booking->user?->email }}</a></p>
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

            @if ($subscription)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 ring-1 ring-violet-200">
                    <div class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-100 text-violet-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                        Plan Coverage Available — {{ $subscription->product?->name }}
                    </div>

                    @if ($claims->isNotEmpty())
                        <div class="mt-3 divide-y divide-gray-100">
                            @foreach ($claims as $c)
                                <div class="py-2 flex items-center justify-between text-sm">
                                    <span class="text-gray-700">R{{ number_format((float) $c->amount_claimed, 2) }} — {{ \Illuminate\Support\Str::limit(collect((array) $c->items_json)->pluck('item_name')->join(', '), 50) }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ ['approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-700'][$c->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($c->status) }}@if ($c->status === 'approved') (R{{ number_format((float) $c->amount_approved, 2) }})@endif</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('garage.bookings.redeem', $booking) }}" x-data="{ items: [{ item_name: '', qty: 1, cost: '' }] }" class="mt-4">
                        @csrf
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Submit a coverage claim</h4>
                        <select name="redemption_type" class="border-gray-300 rounded-md shadow-sm text-sm mb-2">
                            <option value="service">Service</option>
                            <option value="part_replacement">Part replacement</option>
                        </select>
                        <textarea name="description" rows="2" placeholder="Work performed (optional)" class="block w-full border-gray-300 rounded-md shadow-sm text-sm mb-2"></textarea>

                        <datalist id="benefit-items">
                            @foreach ($subscription->product->benefitItems as $bi)
                                <option value="{{ $bi->item_name }}"></option>
                            @endforeach
                        </datalist>

                        <template x-for="(item, i) in items" :key="i">
                            <div class="flex gap-2 mb-2">
                                <input type="text" list="benefit-items" :name="`items[${i}][item_name]`" x-model="item.item_name" placeholder="Item" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="number" min="1" step="1" :name="`items[${i}][qty]`" x-model="item.qty" placeholder="Qty" class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="number" min="0" step="0.01" :name="`items[${i}][cost]`" x-model="item.cost" placeholder="Cost" class="w-28 border-gray-300 rounded-md shadow-sm text-sm">
                                <button type="button" @click="items.splice(i, 1)" x-show="items.length > 1" class="text-red-600 px-2">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="items.push({ item_name: '', qty: 1, cost: '' })" class="text-sm text-violet-600 hover:text-violet-800">+ Add item</button>
                        <div class="mt-2 text-right text-sm font-medium text-gray-900">Claim total: R<span x-text="items.reduce((s, i) => s + (parseFloat(i.cost) || 0), 0).toFixed(2)"></span></div>
                        <div class="mt-3"><x-primary-button>Submit claim</x-primary-button></div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
