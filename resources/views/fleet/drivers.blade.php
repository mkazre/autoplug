<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Fleet') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('fleet.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">Add a driver</h3>
                <form method="POST" action="{{ route('fleet.drivers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('Phone (optional)')" />
                        <x-text-input id="phone" name="phone" type="tel" class="block mt-1 w-full" :value="old('phone')" />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('Temporary password')" />
                        <x-text-input id="password" name="password" type="text" class="block mt-1 w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-primary-button>Add driver</x-primary-button>
                    </div>
                </form>

                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Drivers</h3>
                @if ($drivers->isEmpty())
                    <p class="text-sm text-gray-500">No drivers yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($drivers as $d)
                            <div class="flex items-center justify-between py-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $d->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $d->email }} @if($d->phone) • {{ $d->phone }} @endif</div>
                                </div>
                                <form method="POST" action="{{ route('fleet.drivers.destroy', $d) }}" onsubmit="return confirm('Remove this driver from the fleet?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
