<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Vehicles') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3"><ul class="list-disc ps-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            @forelse ($vehicles as $v)
                <div class="bg-white shadow-sm rounded-2xl p-5" x-data="{ edit: false }">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $v->make }} {{ $v->model }} {{ $v->year }}</div>
                            <div class="text-sm text-gray-500">{{ $v->registration }}@if ($v->odometer_km) • {{ number_format((int) $v->odometer_km) }} km @endif @if ($v->first_registered_on)• first reg {{ $v->first_registered_on->format('M Y') }}@endif</div>
                            @if ($v->plan_subscriptions_count)<span class="mt-1 inline-block text-xs px-2 py-0.5 rounded-full bg-violet-100 text-violet-700">{{ $v->plan_subscriptions_count }} active plan(s)</span>@endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="edit = ! edit" class="text-sm text-violet-600 hover:underline">Edit</button>
                            @if (! $v->plan_subscriptions_count)
                                <form method="POST" action="{{ route('vehicles.destroy', $v) }}" onsubmit="return confirm('Remove this vehicle?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('vehicles.update', $v) }}" x-show="edit" x-cloak class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @csrf @method('PUT')
                        <input name="make" value="{{ $v->make }}" placeholder="Make" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="model" value="{{ $v->model }}" placeholder="Model" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="year" value="{{ $v->year }}" placeholder="Year" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="registration" value="{{ $v->registration }}" placeholder="Reg" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="odometer_km" type="number" value="{{ $v->odometer_km }}" placeholder="km" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="first_registered_on" type="date" value="{{ optional($v->first_registered_on)->format('Y-m-d') }}" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <div class="col-span-2 sm:col-span-3 text-right"><button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-md text-xs font-semibold">Save</button></div>
                    </form>
                </div>
            @empty
                <p class="text-sm text-gray-500">No vehicles yet — add your first below.</p>
            @endforelse

            <div class="bg-white shadow-sm rounded-2xl p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Add a vehicle</h3>
                <form method="POST" action="{{ route('vehicles.store') }}" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @csrf
                    <input name="make" placeholder="Make" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <input name="model" placeholder="Model" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <input name="year" placeholder="Year" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <input name="registration" placeholder="Reg" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <input name="odometer_km" type="number" placeholder="km" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <input name="first_registered_on" type="date" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    <div class="col-span-2 sm:col-span-3 text-right"><button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-700">Add vehicle</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
