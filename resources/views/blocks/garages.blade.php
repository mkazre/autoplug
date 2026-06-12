@php
    $d = $data ?? [];
    $count = (int) ($d['count'] ?? 6);
    $source = $d['source'] ?? 'top_rated';
    $q = \App\Models\Garage::approved()
        ->whereHas('branches', fn ($b) => $b->where('is_active', true))
        ->with([
            'branches' => fn ($b) => $b->where('is_active', true),
            'photos' => fn ($p) => $p->where('type', 'affiliation'),
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews');
    $q = match ($source) {
        'newest' => $q->latest(),
        'most_reviewed' => $q->orderByDesc('reviews_count'),
        default => $q->orderByDesc('reviews_avg_rating'),
    };
    $garages = $q->take($count)->get();
@endphp
<section class="mt-12">
    <div class="flex items-center justify-between mb-4">
        @if (!empty($d['heading']))<h2 class="text-2xl font-bold text-gray-900">{{ $d['heading'] }}</h2>@endif
        @if (($d['show_view_all'] ?? true))<a href="{{ route('garages.index') }}" class="text-violet-600 text-sm hover:underline">View all →</a>@endif
    </div>
    @if ($garages->isEmpty())
        <p class="text-sm text-gray-500">No garages to show yet.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($garages as $garage)
                <a href="{{ route('garages.show', $garage) }}" class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition block">
                    <div class="flex items-center gap-3">
                        @if ($garage->logo)
                            <img src="{{ asset('storage/'.$garage->logo) }}" alt="" class="h-12 w-12 object-cover rounded">
                        @else
                            <div class="h-12 w-12 rounded bg-violet-100 text-violet-600 flex items-center justify-center font-bold text-lg">{{ substr($garage->name, 0, 1) }}</div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">{{ $garage->name }} <x-verified-badge :garage="$garage" /></h3>
                            @if ($garage->reviews_count)
                                <p class="text-amber-500 text-sm">{!! str_repeat('★', (int) round($garage->reviews_avg_rating)) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - (int) round($garage->reviews_avg_rating)) !!}</span> <span class="text-xs text-gray-400">({{ $garage->reviews_count }})</span></p>
                            @endif
                        </div>
                    </div>
                    <x-affiliation-logos :garage="$garage" />
                </a>
            @endforeach
        </div>
    @endif
</section>
