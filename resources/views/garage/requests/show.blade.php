<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quote request') }}</h2>
    </x-slot>

    @php
        $windowClosed = $qrg->status !== 'quoted' && $qrg->expires_at && $qrg->expires_at->isPast();
        $won = $qrg->status === 'accepted';
        $customer = $qrg->quoteRequest?->user;
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('garage.requests.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All requests</a>
                <h3 class="text-lg font-medium text-gray-900 mt-2">{{ $qrg->quoteRequest?->service?->name ?? 'General request' }}</h3>
                <p class="text-sm text-gray-500">Branch: {{ $qrg->branch?->name }}</p>
                @if ($qrg->quoteRequest?->description)
                    <p class="text-sm text-gray-600 mt-2">{{ $qrg->quoteRequest->description }}</p>
                @endif
                @if ($qrg->quoteRequest?->vehicle)
                    <p class="text-sm text-gray-500 mt-1">Vehicle: {{ $qrg->quoteRequest->vehicle->make }} {{ $qrg->quoteRequest->vehicle->model }} {{ $qrg->quoteRequest->vehicle->year }} {{ $qrg->quoteRequest->vehicle->registration }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 {{ $won ? 'ring-1 ring-green-300' : '' }}">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Customer contact</h3>
                @if ($won)
                    <p class="text-xs text-green-700 mb-3 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        You won this job — here are the customer's details.
                    </p>
                    <dl class="text-sm text-gray-700 space-y-1">
                        <div class="flex gap-3"><dt class="w-16 text-gray-500">Name</dt><dd class="font-medium">{{ $customer?->name }}</dd></div>
                        @if ($customer?->phone)
                            <div class="flex gap-3"><dt class="w-16 text-gray-500">Phone</dt><dd><a href="tel:{{ $customer->phone }}" class="text-violet-600 hover:underline">{{ $customer->phone }}</a></dd></div>
                        @endif
                        <div class="flex gap-3"><dt class="w-16 text-gray-500">Email</dt><dd><a href="mailto:{{ $customer?->email }}" class="text-violet-600 hover:underline">{{ $customer?->email }}</a></dd></div>
                    </dl>
                @else
                    <div class="flex items-start gap-2 text-sm text-gray-500">
                        <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <p>The customer's name, phone and email are revealed here only once they accept your quote.</p>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded">{{ session('error') }}</div>
                @endif

                @if ($qrg->expires_at && ! $qrg->quote)
                    <div class="mb-4 p-3 rounded text-sm {{ $qrg->expires_at->isPast() ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800' }}">
                        @if ($qrg->expires_at->isPast())
                            Response window closed — you can no longer quote this request.
                        @else
                            Respond within: @include('partials.countdown', ['expires' => $qrg->expires_at, 'expiredLabel' => 'Closed'])
                        @endif
                    </div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $qrg->quote ? 'Update your quote' : 'Submit a quote' }}</h3>

                @if ($windowClosed)
                    <p class="text-sm text-gray-500">This request's response window has closed, so it can no longer be quoted.</p>
                @else
                    <form method="POST" action="{{ route('garage.requests.quote.store', $qrg) }}"
                          x-data="{ items: @js($qrg->quote->items_json ?? [['description' => '', 'price' => '']]) }">
                        @csrf

                        <template x-for="(item, i) in items" :key="i">
                            <div class="flex gap-2 mb-2">
                                <input type="text" :name="`items[${i}][description]`" x-model="item.description" placeholder="Item / labour description" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="number" step="0.01" min="0" :name="`items[${i}][price]`" x-model="item.price" placeholder="Price" class="w-32 border-gray-300 rounded-md shadow-sm text-sm">
                                <button type="button" @click="items.splice(i, 1)" x-show="items.length > 1" class="text-red-600 px-2">&times;</button>
                            </div>
                        </template>

                        <button type="button" @click="items.push({ description: '', price: '' })" class="text-sm text-violet-600 hover:text-violet-800">+ Add item</button>

                        <div class="mt-2 text-right text-sm font-medium text-gray-900">
                            Total: R<span x-text="items.reduce((s, i) => s + (parseFloat(i.price) || 0), 0).toFixed(2)"></span>
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mt-2" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="valid_until" :value="__('Valid until')" />
                                <x-text-input id="valid_until" name="valid_until" type="date" class="block mt-1 w-full" :value="old('valid_until', optional($qrg->quote?->valid_until)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('notes', $qrg->quote?->notes) }}</textarea>
                        </div>

                        <div class="mt-6">
                            <x-primary-button>{{ $qrg->quote ? 'Update quote' : 'Send quote' }}</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
