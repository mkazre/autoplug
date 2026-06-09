@php
    $tab = fn (string $pattern) => request()->routeIs($pattern)
        ? 'border-indigo-500 text-indigo-600'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
@endphp
<nav class="bg-white shadow-sm sm:rounded-lg px-4">
    <div class="flex space-x-6">
        <a href="{{ route('garage.dashboard') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('garage.dashboard') }}">Overview</a>
        <a href="{{ route('garage.profile.edit') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('garage.profile.edit') }}">Profile</a>
        <a href="{{ route('garage.branches.index') }}" class="inline-flex items-center px-1 py-4 border-b-2 text-sm font-medium {{ $tab('garage.branches.*') }}">Branches</a>
    </div>
</nav>
