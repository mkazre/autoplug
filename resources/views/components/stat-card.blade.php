@props(['label' => '', 'value' => '', 'icon' => null, 'color' => 'violet', 'href' => null, 'sub' => null])
@php
    $grad = [
        'violet' => 'from-violet-100 to-violet-50',
        'orange' => 'from-orange-100 to-orange-50',
        'green' => 'from-emerald-100 to-emerald-50',
        'blue' => 'from-blue-100 to-blue-50',
        'pink' => 'from-fuchsia-100 to-fuchsia-50',
        'indigo' => 'from-indigo-100 to-indigo-50',
        'yellow' => 'from-amber-100 to-amber-50',
        'slate' => 'from-slate-100 to-slate-50',
    ][$color] ?? 'from-violet-100 to-violet-50';
    $chip = [
        'violet' => 'bg-violet-500', 'orange' => 'bg-orange-500', 'green' => 'bg-emerald-500', 'blue' => 'bg-blue-500',
        'pink' => 'bg-fuchsia-500', 'indigo' => 'bg-indigo-500', 'yellow' => 'bg-amber-500', 'slate' => 'bg-slate-500',
    ][$color] ?? 'bg-violet-500';
@endphp
@if ($href)
<a href="{{ $href }}" class="block bg-gradient-to-br {{ $grad }} border border-black/5 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
@else
<div class="bg-gradient-to-br {{ $grad }} border border-black/5 rounded-2xl p-5 shadow-sm">
@endif
    <div class="flex items-start justify-between">
        <div>
            <div class="text-sm text-gray-500">{{ $label }}</div>
            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $value }}</div>
            @if ($sub)<div class="text-xs text-gray-500 mt-1">{{ $sub }}</div>@endif
        </div>
        @if ($icon)
            <div class="h-10 w-10 rounded-xl {{ $chip }} text-white flex items-center justify-center text-lg">{!! $icon !!}</div>
        @endif
    </div>
@if ($href)</a>@else</div>@endif
