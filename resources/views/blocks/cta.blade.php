@php $d = $data ?? []; @endphp
<section class="mt-12 rounded-3xl text-white p-10 sm:p-12 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6" style="background: {{ $d['bg_color'] ?? '#7c3aed' }};">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold">{{ $d['heading'] ?? '' }}</h2>
        @if (!empty($d['subtext']))<p class="mt-2 text-white/90">{{ $d['subtext'] }}</p>@endif
    </div>
    @if (!empty($d['button_text']))
        <a href="{{ $d['button_url'] ?? '#' }}" class="px-6 py-3 bg-white text-violet-700 font-semibold rounded-lg hover:bg-violet-50 transition whitespace-nowrap">{{ $d['button_text'] }}</a>
    @endif
</section>
