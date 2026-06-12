@php
    $d = $data ?? [];
    $items = $d['items'] ?? [];
    $id = 'feat-'.uniqid();
    $cm = (int) ($d['cols_mobile'] ?? 1);
    $ct = (int) ($d['cols_tablet'] ?? 2);
    $ctl = (int) ($d['cols_tablet_landscape'] ?? 3);
    $cl = (int) ($d['cols_laptop'] ?? 4);
    $cd = (int) ($d['cols_desktop'] ?? 6);
@endphp
<section class="mt-12">
    @if (!empty($d['heading']))<h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $d['heading'] }}</h2>@endif
    <style>
        #{{ $id }} { display: grid; gap: 1.5rem; grid-template-columns: repeat({{ $cm }}, minmax(0, 1fr)); }
        @@media (min-width: 640px)  { #{{ $id }} { grid-template-columns: repeat({{ $ct }}, minmax(0, 1fr)); } }
        @@media (min-width: 768px)  { #{{ $id }} { grid-template-columns: repeat({{ $ctl }}, minmax(0, 1fr)); } }
        @@media (min-width: 1024px) { #{{ $id }} { grid-template-columns: repeat({{ $cl }}, minmax(0, 1fr)); } }
        @@media (min-width: 1280px) { #{{ $id }} { grid-template-columns: repeat({{ $cd }}, minmax(0, 1fr)); } }
    </style>
    <div id="{{ $id }}">
        @foreach ($items as $item)
            @php $img = $item['image'] ?? null; $size = (int) ($item['image_size'] ?? 48); @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                @if ($img)
                    <img src="{{ asset('storage/'.$img) }}" alt="" style="height: {{ $size }}px; width:auto;" class="object-contain">
                @else
                    <div class="text-3xl">{{ $item['icon'] ?? '⭐' }}</div>
                @endif
                <h3 class="font-semibold mt-3 text-gray-900">{{ $item['title'] ?? '' }}</h3>
                @if (!empty($item['text']))<p class="text-sm text-gray-600 mt-1">{{ $item['text'] }}</p>@endif
            </div>
        @endforeach
    </div>
</section>
