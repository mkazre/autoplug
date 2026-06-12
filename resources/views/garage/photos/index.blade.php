<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 text-sm text-red-600">{{ session('error') }}</div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">Upload a photo</h3>
                <form method="POST" action="{{ route('garage.photos.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <x-input-label for="photo" :value="__('Image')" />
                        <input id="photo" name="photo" type="file" accept="image/*" required class="mt-1 block w-full text-sm text-gray-600">
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="workshop">Workshop</option>
                            <option value="product">Product</option>
                            <option value="affiliation">Affiliation / association logo</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="branch_id" :value="__('Branch (optional)')" />
                        <select id="branch_id" name="branch_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Garage-wide</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <x-primary-button>Upload</x-primary-button>
                        <span class="text-xs text-gray-400 ms-2">JPG/PNG, max 4MB.</span>
                    </div>
                </form>

                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Gallery</h3>
                @if ($photos->isEmpty())
                    <p class="text-sm text-gray-500">No photos yet.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($photos as $photo)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/'.$photo->photo_url) }}" alt="Photo" class="h-32 w-full object-cover">
                                <div class="p-2 text-xs text-gray-600 flex items-center justify-between">
                                    <span>
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-gray-100">{{ ucfirst($photo->type) }}</span>
                                        <span class="block mt-1 text-gray-400">{{ $photo->branch?->name ?? 'Garage-wide' }}</span>
                                    </span>
                                    <form method="POST" action="{{ route('garage.photos.destroy', $photo) }}" onsubmit="return confirm('Delete this photo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
