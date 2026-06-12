@php
    $d = $data ?? [];
    $defaults = [
        'minor_service' => ['Minor Service', '🛠️'],
        'major_service' => ['Major Service', '🔧'],
        'tyres' => ['Tyres', '🛞'],
        'brakes' => ['Brakes', '🛑'],
        'repair' => ['Repair', '⚙️'],
        'other' => ['Other', '✨'],
    ];

    $cards = $d['cards'] ?? [];
    if (empty($cards)) {
        foreach ($defaults as $k => $v) {
            $cards[] = ['category' => $k, 'label' => $v[0], 'icon' => $v[1], 'image' => null, 'image_size' => 48];
        }
    }

    $counts = \Illuminate\Support\Facades\DB::table('garage_services')
        ->join('services', 'garage_services.service_id', '=', 'services.id')
        ->join('branches', 'garage_services.branch_id', '=', 'branches.id')
        ->join('garages', 'branches.garage_id', '=', 'garages.id')
        ->where('garages.status', 'approved')
        ->select('services.category', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT garages.id) as c'))
        ->groupBy('services.category')
        ->pluck('c', 'category');

    $id = 'svc-'.uniqid();
    $cm = (int) ($d['cols_mobile'] ?? 2);
    $ct = (int) ($d['cols_tablet'] ?? 3);
    $ctl = (int) ($d['cols_tablet_landscape'] ?? 4);
    $cl = (int) ($d['cols_laptop'] ?? 6);
    $cd = (int) ($d['cols_desktop'] ?? 6);
@endphp
<section class="mt-12">
    @if (!empty($d['heading']))<h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $d['heading'] }}</h2>@endif
    <style>
        #{{ $id }} { display: grid; gap: 1rem; grid-template-columns: repeat({{ $cm }}, minmax(0, 1fr)); }
        @@media (min-width: 640px)  { #{{ $id }} { grid-template-columns: repeat({{ $ct }}, minmax(0, 1fr)); } }
        @@media (min-width: 768px)  { #{{ $id }} { grid-template-columns: repeat({{ $ctl }}, minmax(0, 1fr)); } }
        @@media (min-width: 1024px) { #{{ $id }} { grid-template-columns: repeat({{ $cl }}, minmax(0, 1fr)); } }
        @@media (min-width: 1280px) { #{{ $id }} { grid-template-columns: repeat({{ $cd }}, minmax(0, 1fr)); } }
    </style>
    <div id="{{ $id }}">
        @foreach ($cards as $card)
            @php
                $cat = $card['category'] ?? 'other';
                $label = !empty($card['label']) ? $card['label'] : ($defaults[$cat][0] ?? ucfirst(str_replace('_', ' ', $cat)));
                $img = $card['image'] ?? null;
                $size = (int) ($card['image_size'] ?? 48);
                $icon = !empty($card['icon']) ? $card['icon'] : ($defaults[$cat][1] ?? '✨');
            @endphp
            <a href="{{ route('search') }}" class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition text-center">
                @if ($img)
                    <img src="{{ asset('storage/'.$img) }}" alt="" style="height: {{ $size }}px; width:auto;" class="object-contain mx-auto">
                @else
                    <div class="text-3xl">{{ $icon }}</div>
                @endif
                <div class="font-semibold text-gray-900 mt-2">{{ $label }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $counts[$cat] ?? 0 }} garage(s)</div>
            </a>
        @endforeach
    </div>
</section>
