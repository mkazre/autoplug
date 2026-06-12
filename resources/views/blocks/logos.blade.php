@php $d = $data ?? []; $logos = $d['logos'] ?? []; @endphp
@if (!empty($logos))
    <section class="mt-12">
        @if (!empty($d['heading']))<h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $d['heading'] }}</h2>@endif
        <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-wrap items-center justify-center gap-10">
            @foreach ($logos as $l)
                @php $img = $l['image'] ?? null; $link = $l['link'] ?? null; @endphp
                @if ($img)
                    @if ($link)<a href="{{ $link }}" target="_blank" rel="noopener">@endif
                        <img src="{{ asset('storage/'.$img) }}" alt="" class="h-10 w-auto object-contain grayscale hover:grayscale-0 transition">
                    @if ($link)</a>@endif
                @endif
            @endforeach
        </div>
    </section>
@endif
