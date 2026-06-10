@php
    $logo = \App\Support\Settings::get('logo');
    $brand = \App\Support\Settings::get('brand_name', 'Autoplug');
    $h = \App\Support\Settings::int('logo_height', 40);
@endphp
@if ($logo)
    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $brand }}" style="height: {{ $h }}px; width:auto;" {{ $attributes }}>
@else
    <span {{ $attributes->merge(['class' => 'text-xl font-bold text-gray-800']) }}>{{ $brand }}</span>
@endif
