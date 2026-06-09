<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-green-600 font-medium">Thanks! Your payment is being processed.</p>
                <p class="text-sm text-gray-500 mt-2">We'll confirm it automatically once PayFast notifies us. You can check the status under your booking.</p>
                <a href="{{ route('bookings.index') }}" class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest">My bookings</a>
            </div>
        </div>
    </div>
</x-app-layout>
