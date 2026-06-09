<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @role('garage_owner')
                    <p class="mb-4 text-gray-700">Manage your garage profile, branches, services and photos.</p>
                    <a href="{{ route('garage.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Manage my garage</a>
                @else
                    <p class="mb-4 text-gray-700">Find a garage near you, compare quotes, and book a service.</p>
                    <div class="flex gap-3">
                        <a href="{{ route('search') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Find a garage</a>
                        <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">My quote requests</a>
                    </div>
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>
