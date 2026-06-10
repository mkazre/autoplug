<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $fleet->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('fleet.partials.nav')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-stat-card label="Vehicles" :value="$fleet->vehicles()->count()" icon="🚗" color="violet" :href="route('fleet.vehicles.index')" />
                <x-stat-card label="Drivers" :value="$fleet->members()->where('id', '!=', $fleet->owner_id)->count()" icon="👥" color="blue" :href="route('fleet.drivers.index')" />
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">Manage your company's vehicle pool and drivers here. As the fleet manager you can also search, request quotes and book services from your normal dashboard.</p>
            </div>
        </div>
    </div>
</x-app-layout>
