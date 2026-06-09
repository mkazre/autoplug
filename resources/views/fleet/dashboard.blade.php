<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $fleet->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('fleet.partials.nav')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('fleet.vehicles.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:bg-gray-50">
                    <div class="text-sm text-gray-500">Vehicles</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $fleet->vehicles()->count() }}</div>
                </a>
                <a href="{{ route('fleet.drivers.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:bg-gray-50">
                    <div class="text-sm text-gray-500">Drivers</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $fleet->members()->where('id', '!=', $fleet->owner_id)->count() }}</div>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">Manage your company's vehicle pool and drivers here. As the fleet manager you can also search, request quotes and book services from your normal dashboard.</p>
            </div>
        </div>
    </div>
</x-app-layout>
