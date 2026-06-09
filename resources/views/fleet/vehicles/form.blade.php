@php $editing = $vehicle->exists; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit vehicle' : 'Add vehicle' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('fleet.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ $editing ? route('fleet.vehicles.update', $vehicle) : route('fleet.vehicles.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="make" :value="__('Make')" />
                            <x-text-input id="make" name="make" type="text" class="block mt-1 w-full" :value="old('make', $vehicle->make)" required />
                            <x-input-error :messages="$errors->get('make')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="model" :value="__('Model')" />
                            <x-text-input id="model" name="model" type="text" class="block mt-1 w-full" :value="old('model', $vehicle->model)" required />
                            <x-input-error :messages="$errors->get('model')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="year" :value="__('Year')" />
                            <x-text-input id="year" name="year" type="number" class="block mt-1 w-full" :value="old('year', $vehicle->year)" />
                        </div>
                        <div>
                            <x-input-label for="registration" :value="__('Registration')" />
                            <x-text-input id="registration" name="registration" type="text" class="block mt-1 w-full" :value="old('registration', $vehicle->registration)" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ $editing ? 'Update vehicle' : 'Add vehicle' }}</x-primary-button>
                        <a href="{{ route('fleet.vehicles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
