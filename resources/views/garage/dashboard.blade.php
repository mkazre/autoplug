<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Garage') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('garage.partials.nav')

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">{{ $garage->name }}</h3>

                @php
                    $badge = [
                        'pending'  => ['Pending review', 'bg-yellow-100 text-yellow-800'],
                        'approved' => ['Approved & listed', 'bg-green-100 text-green-800'],
                        'rejected' => ['Not approved', 'bg-red-100 text-red-800'],
                    ][$garage->status] ?? [$garage->status, 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge[1] }} mt-2">{{ $badge[0] }}</span>

                @if ($garage->admin_notes)
                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                        <strong>Note from admin:</strong> {{ $garage->admin_notes }}
                    </div>
                @endif

                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded">
                        <dt class="text-sm text-gray-500">Branches</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $garage->branches()->count() }}</dd>
                    </div>
                    <div class="p-4 bg-gray-50 rounded">
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ ucfirst($garage->status) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
