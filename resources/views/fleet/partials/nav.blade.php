@php
    $tab = fn (string $pattern) => request()->routeIs($pattern)
        ? 'border-violet-500 text-violet-600'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
@endphp
<nav class="bg-white shadow-sm sm:rounded-lg px-4">
    <div class="flex space-x-6">
        <a href="{{ route('fleet.dashboard') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('fleet.dashboard') }}">Overview</a>
        <a href="{{ route('fleet.vehicles.index') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('fleet.vehicles.*') }}">Vehicles</a>
        <a href="{{ route('fleet.drivers.index') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('fleet.drivers.*') }}">Drivers</a>
    </div>
</nav>
