@php
    $d = $data ?? [];
    $bgImage = $d['bg_image'] ?? null;
    $bgColor = $d['bg_color'] ?? '#7c3aed';
    $overlayColor = $d['overlay_color'] ?? '#7c3aed';
    $opacity = (int) ($d['overlay_opacity'] ?? 60) / 100;
@endphp
<section class="relative overflow-hidden rounded-3xl text-white p-10 sm:p-16 shadow-lg"
         style="@if ($bgImage) background-image:url('{{ asset('storage/'.$bgImage) }}'); background-size:cover; background-position:center; @else background:{{ $bgColor }}; @endif">
    @if ($bgImage)
        <div class="absolute inset-0" style="background: {{ $overlayColor }}; opacity: {{ $opacity }};"></div>
    @endif
    <div class="relative max-w-2xl">
        <h1 class="text-3xl sm:text-5xl font-bold leading-tight">{{ $d['headline'] ?? 'Find a trusted garage near you' }}</h1>
        @if (!empty($d['subtext']))
            <p class="mt-4 text-white/90 text-lg">{{ $d['subtext'] }}</p>
        @endif

        @if (($d['show_search'] ?? true))
            <form method="GET" action="{{ route('search') }}" class="mt-8 bg-white rounded-2xl p-4 shadow-lg">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                        <input name="address" placeholder="Suburb, city or address" required class="w-full border-gray-300 rounded-lg text-sm h-11 text-gray-900">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Service</label>
                        <select name="service_id" class="w-full border-gray-300 rounded-lg text-sm h-11 text-gray-900">
                            <option value="">Any service</option>
                            @foreach (\App\Models\Service::orderBy('name')->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full h-11 bg-violet-600 text-white rounded-lg font-semibold text-sm hover:bg-violet-500">Search</button>
                    </div>
                </div>
            </form>
        @endif

        @if (!empty($d['button1_text']) || !empty($d['button2_text']))
            <div class="mt-6 flex flex-wrap gap-3">
                @if (!empty($d['button1_text']))
                    <a href="{{ $d['button1_url'] ?? '#' }}" class="px-6 py-3 bg-white text-violet-700 font-semibold rounded-lg hover:bg-violet-50 transition">{{ $d['button1_text'] }}</a>
                @endif
                @if (!empty($d['button2_text']))
                    <a href="{{ $d['button2_url'] ?? '#' }}" class="px-6 py-3 bg-white/15 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/25 transition">{{ $d['button2_text'] }}</a>
                @endif
            </div>
        @endif

        @if (!empty($d['badge_label']))
            <div class="mt-5 inline-flex items-center gap-2 text-white/90 text-sm">{{ $d['badge_label'] }} @if (!empty($d['badge_rating']))<span class="text-amber-300 font-semibold">★ {{ $d['badge_rating'] }}</span>@endif</div>
        @endif
    </div>
</section>
