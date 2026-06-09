<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Branches</h3>
                    <a href="{{ route('garage.branches.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Add branch</a>
                </div>

                @if ($branches->isEmpty())
                    <p class="text-gray-500 text-sm">No branches yet. Add your first location.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2 pr-4">Name</th>
                                    <th class="py-2 pr-4">Address</th>
                                    <th class="py-2 pr-4">Lat / Lng</th>
                                    <th class="py-2 pr-4">Active</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($branches as $branch)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-gray-900">{{ $branch->name }}</td>
                                        <td class="py-2 pr-4 text-gray-600">{{ $branch->address ?: '—' }}</td>
                                        <td class="py-2 pr-4 text-gray-600">{{ $branch->lat && $branch->lng ? $branch->lat.', '.$branch->lng : '—' }}</td>
                                        <td class="py-2 pr-4">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $branch->is_active ? 'Yes' : 'No' }}</span>
                                        </td>
                                        <td class="py-2 pr-4 text-right whitespace-nowrap">
                                            <a href="{{ route('garage.branches.services.index', $branch) }}" class="text-gray-600 hover:text-gray-900">Services</a>
                                            <a href="{{ route('garage.branches.edit', $branch) }}" class="ms-3 text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('garage.branches.destroy', $branch) }}" class="inline" onsubmit="return confirm('Delete this branch?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ms-3 text-red-600 hover:text-red-900">Delete</button>
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
