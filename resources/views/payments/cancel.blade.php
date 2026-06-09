<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-amber-600 font-medium">Payment cancelled.</p>
                <p class="text-sm text-gray-500 mt-2">No charge was made. You can try again from your booking.</p>
                <a href="{{ route('bookings.index') }}" class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">My bookings</a>
            </div>
        </div>
    </div>
</x-app-layout>
