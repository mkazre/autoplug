@props(['status'])
@php
    $map = [
        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
        'confirmed' => ['Confirmed', 'bg-blue-100 text-blue-800'],
        'inprogress' => ['In progress', 'bg-indigo-100 text-indigo-800'],
        'completed' => ['Completed', 'bg-green-100 text-green-800'],
        'cancelled' => ['Cancelled', 'bg-gray-100 text-gray-600'],
    ];
    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700'];
@endphp
<span class="inline-flex px-2 py-0.5 rounded-full text-xs {{ $classes }}">{{ $label }}</span>
