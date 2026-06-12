@props(['booking', 'action', 'label', 'color' => 'indigo'])
@php
    $colors = [
        'indigo' => 'bg-violet-600 hover:bg-violet-500 text-white',
        'green' => 'bg-green-600 hover:bg-green-500 text-white',
        'red' => 'bg-white border border-red-300 text-red-700 hover:bg-red-50',
    ];
@endphp
<form method="POST" action="{{ route('garage.bookings.status', $booking) }}" @if($action === 'cancel') onsubmit="return confirm('Are you sure?');" @endif>
    @csrf
    <input type="hidden" name="action" value="{{ $action }}">
    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest {{ $colors[$color] ?? $colors['indigo'] }}">{{ $label }}</button>
</form>
