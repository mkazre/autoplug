<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Fleet') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('fleet.partials.nav')

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Vehicles</h3>
                    <a href="{{ route('fleet.vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Add vehicle</a>
                </div>

                @if ($vehicles->isEmpty())
                    <p class="text-sm text-gray-500">No vehicles yet.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead><tr class="text-left text-gray-500"><th class="py-2 pr-4">Vehicle</th><th class="py-2 pr-4">Year</th><th class="py-2 pr-4">Reg</th><th></th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($vehicles as $v)
                                <tr>
                                    <td class="py-2 pr-4 font-medium text-gray-900">{{ $v->make }} {{ $v->model }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $v->year ?: '—' }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $v->registration ?: '—' }}</td>
                                    <td class="py-2 pr-4 text-right whitespace-nowrap">
                                        <a href="{{ route('fleet.vehicles.edit', $v) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('fleet.vehicles.destroy', $v) }}" class="inline" onsubmit="return confirm('Remove this vehicle?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ms-3 text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
