<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @role('garage_owner')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="mb-4 text-gray-700">Manage your garage profile, branches, services, photos, requests and bookings.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('garage.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Go to my garage</a>
                        <a href="{{ route('account.settings') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Account settings</a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-stat-card label="Quote requests" :value="$stats['requests'] ?? 0" icon="📋" color="violet" :href="route('quotes.index')" />
                    <x-stat-card label="Bookings" :value="$stats['bookings'] ?? 0" icon="📅" color="blue" :href="route('bookings.index')" />
                    <x-stat-card label="Upcoming" :value="$stats['upcoming'] ?? 0" icon="⏳" color="green" />
                    <x-stat-card label="Vehicles" :value="$stats['vehicles'] ?? 0" icon="🚗" color="orange" />
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('search') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Find a garage</a>
                        <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">My quote requests</a>
                        <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">My bookings</a>
                        @if (auth()->user()->ownedFleet)
                            <a href="{{ route('fleet.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Manage fleet</a>
                        @endif
                        <a href="{{ route('account.settings') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Account settings</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-medium text-gray-900 mb-3">Recent quote requests</h3>
                        @forelse ($recentRequests as $r)
                            <a href="{{ route('quotes.show', $r) }}" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-1 rounded">
                                <span class="text-sm text-gray-800">{{ $r->service?->name ?? 'General request' }}</span>
                                <span class="text-xs text-gray-500">{{ $r->request_garages_count }} garage(s) • {{ ucfirst($r->status) }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No requests yet. <a href="{{ route('search') }}" class="text-violet-600 underline">Find a garage</a>.</p>
                        @endforelse
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-medium text-gray-900 mb-3">Upcoming bookings</h3>
                        @forelse ($upcomingBookings as $b)
                            <a href="{{ route('bookings.show', $b) }}" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-1 rounded">
                                <span class="text-sm text-gray-800">{{ $b->branch?->garage?->name }} — {{ $b->scheduled_at?->format('d M H:i') }}</span>
                                <x-booking-status :status="$b->status" />
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">No upcoming bookings.</p>
                        @endforelse
                    </div>
                </div>
            @endrole
        </div>
    </div>
</x-app-layout>
