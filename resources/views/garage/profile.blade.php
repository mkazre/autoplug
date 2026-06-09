<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status') === 'profile-updated')
                    <div class="mb-4 text-sm text-green-600">Profile saved.</div>
                @endif

                <form method="POST" action="{{ route('garage.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Garage name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $garage->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $garage->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="logo" :value="__('Logo')" />
                        @if ($garage->logo)
                            <img src="{{ asset('storage/'.$garage->logo) }}" alt="Logo" class="h-16 w-16 object-cover rounded mt-2">
                        @endif
                        <input id="logo" name="logo" type="file" accept="image/*" class="mt-2 block text-sm text-gray-600">
                        <p class="text-xs text-gray-400 mt-1">PNG/JPG, max 2MB.</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
