@php $editing = $branch->exists; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit branch' : 'Add branch' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ $editing ? route('garage.branches.update', $branch) : route('garage.branches.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif

                    <div>
                        <x-input-label for="name" :value="__('Branch name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $branch->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Address')" />
                        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $branch->address)" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="lat" :value="__('Latitude')" />
                            <x-text-input id="lat" name="lat" type="text" class="block mt-1 w-full" :value="old('lat', $branch->lat)" placeholder="-26.2041" />
                            <x-input-error :messages="$errors->get('lat')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="lng" :value="__('Longitude')" />
                            <x-text-input id="lng" name="lng" type="text" class="block mt-1 w-full" :value="old('lng', $branch->lng)" placeholder="28.0473" />
                            <x-input-error :messages="$errors->get('lng')" class="mt-2" />
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Tip: find coordinates by right-clicking a spot in Google Maps. A map picker will be added later.</p>

                    <div class="mt-4">
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $branch->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mt-4 flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-violet-600 shadow-sm"
                               @checked(old('is_active', $editing ? $branch->is_active : true)) />
                        <label for="is_active" class="ms-2 text-sm text-gray-700">Active (visible in search)</label>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ $editing ? 'Update branch' : 'Create branch' }}</x-primary-button>
                        <a href="{{ route('garage.branches.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
