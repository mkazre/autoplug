<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services &amp; pricing — {{ $branch->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <a href="{{ route('garage.branches.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to branches</a>

                <h3 class="text-lg font-medium text-gray-900 mt-3 mb-4">Add a service</h3>
                @if ($available->isEmpty())
                    <p class="text-sm text-gray-500">All catalogue services have been added to this branch.</p>
                @else
                    <form method="POST" action="{{ route('garage.branches.services.store', $branch) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-2">
                            <x-input-label for="service_id" :value="__('Service')" />
                            <select id="service_id" name="service_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach ($available as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="price" :value="__('Price (R)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="block mt-1 w-full" :value="old('price')" />
                        </div>
                        <div>
                            <x-primary-button>Add</x-primary-button>
                        </div>
                        <div class="sm:col-span-4">
                            <x-input-label for="notes" :value="__('Notes (optional)')" />
                            <x-text-input id="notes" name="notes" type="text" class="block mt-1 w-full" :value="old('notes')" />
                        </div>
                    </form>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Current services</h3>
                @if ($branch->services->isEmpty())
                    <p class="text-sm text-gray-500">No services added yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2 pr-4">Service</th>
                                    <th class="py-2 pr-4">Price / notes</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($branch->services as $gs)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-gray-900 align-top">
                                            {{ $gs->service?->name }}
                                            <div class="text-xs text-gray-400">{{ $gs->service?->category }}</div>
                                        </td>
                                        <td class="py-2 pr-4">
                                            <form method="POST" action="{{ route('garage.branches.services.update', [$branch, $gs]) }}" class="flex flex-wrap items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" step="0.01" min="0" name="price" value="{{ $gs->price }}" placeholder="Price" class="w-28 border-gray-300 rounded-md shadow-sm text-sm">
                                                <input type="text" name="notes" value="{{ $gs->notes }}" placeholder="Notes" class="flex-1 min-w-40 border-gray-300 rounded-md shadow-sm text-sm">
                                                <button type="submit" class="text-violet-600 hover:text-violet-900 text-sm">Save</button>
                                            </form>
                                        </td>
                                        <td class="py-2 pr-4 text-right align-top">
                                            <form method="POST" action="{{ route('garage.branches.services.destroy', [$branch, $gs]) }}" onsubmit="return confirm('Remove this service?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
