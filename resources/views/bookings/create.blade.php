<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Book your service') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">{{ $quote->quoteRequestGarage->branch->garage->name }}</h3>
                <p class="text-sm text-gray-500">{{ $quote->quoteRequestGarage->branch->name }}</p>
                <div class="mt-2 text-2xl font-bold text-gray-900">R{{ number_format($quote->total_price, 2) }}</div>
                @if (is_array($quote->items_json))
                    <ul class="mt-2 text-sm text-gray-600">
                        @foreach ($quote->items_json as $item)
                            <li class="flex justify-between"><span>{{ $item['description'] ?? '' }}</span><span>R{{ number_format((float) ($item['price'] ?? 0), 2) }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="quote_id" value="{{ $quote->id }}">

                    <x-input-label for="scheduled_at" :value="__('Preferred date & time')" />
                    <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local" class="block mt-1 w-full" :value="old('scheduled_at')" required />
                    <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Request booking</x-primary-button>
                        <a href="{{ route('quotes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
