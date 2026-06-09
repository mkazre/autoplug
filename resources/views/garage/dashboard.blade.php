<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">{{ $garage->name }}</h3>
                    @php
                        $badge = [
                            'pending'  => ['Pending review', 'bg-yellow-100 text-yellow-800'],
                            'approved' => ['Approved & listed', 'bg-green-100 text-green-800'],
                            'rejected' => ['Not approved', 'bg-red-100 text-red-800'],
                        ][$garage->status] ?? [$garage->status, 'bg-gray-100 text-gray-800'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge[1] }}">{{ $badge[0] }}</span>
                </div>
                @if ($garage->admin_notes)
                    <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700"><strong>Note from admin:</strong> {{ $garage->admin_notes }}</div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Branches</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $garage->branches()->count() }}</div>
                </div>
                <a href="{{ route('garage.requests.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:bg-gray-50">
                    <div class="text-sm text-gray-500">New requests</div>
                    <div class="text-2xl font-semibold {{ $newRequestsCount > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $newRequestsCount }}</div>
                </a>
                <a href="{{ route('garage.bookings.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:bg-gray-50">
                    <div class="text-sm text-gray-500">Upcoming bookings</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $upcomingBookings->count() }}</div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-3">Recent requests</h3>
                    @forelse ($recentRequests as $rg)
                        <a href="{{ route('garage.requests.show', $rg) }}" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-1 rounded">
                            <span class="text-sm text-gray-800">{{ $rg->quoteRequest?->service?->name ?? 'General request' }}</span>
                            @if ($rg->quote)
                                <span class="text-xs text-green-600">Quoted R{{ number_format($rg->quote->total_price, 2) }}</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">New</span>
                            @endif
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">No requests yet.</p>
                    @endforelse
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-3">Upcoming bookings</h3>
                    @forelse ($upcomingBookings as $b)
                        <a href="{{ route('garage.bookings.show', $b) }}" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 hover:bg-gray-50 px-1 rounded">
                            <span class="text-sm text-gray-800">{{ $b->user?->name }} — {{ $b->scheduled_at?->format('d M H:i') }}</span>
                            <x-booking-status :status="$b->status" />
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">No upcoming bookings.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
