<x-public-layout title="{{ $garage->name }} — Autoplug">
    <a href="{{ route('garages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; All garages</a>

    <div class="bg-white shadow-sm rounded-2xl p-6 mt-3">
        <div class="flex items-center gap-4">
            @if ($garage->logo)
                <img src="{{ asset('storage/'.$garage->logo) }}" alt="" class="h-16 w-16 object-cover rounded">
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">{{ $garage->name }} <x-verified-badge :garage="$garage" /></h1>
                @if ($garage->reviews_count)
                    <p class="text-amber-500">{!! str_repeat('★', (int) round($garage->reviews_avg_rating)) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - (int) round($garage->reviews_avg_rating)) !!}</span> <span class="text-sm text-gray-500">{{ number_format($garage->reviews_avg_rating, 1) }} ({{ $garage->reviews_count }} review(s))</span></p>
                @endif
                @if ($garage->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $garage->description }}</p>
                @endif
            </div>
        </div>
        <a href="{{ route('search') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-violet-600 text-white rounded-md text-xs uppercase tracking-widest hover:bg-violet-500">Request a quote</a>
    </div>

    @php
        $affiliations = $garage->photos->where('type', 'affiliation');
        $gallery = $garage->photos->whereIn('type', ['workshop', 'product']);
    @endphp

    @if ($affiliations->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-900 mt-8 mb-3">Affiliations &amp; accreditations</h2>
        <div class="bg-white shadow-sm rounded-2xl p-6 flex flex-wrap items-center gap-8">
            @foreach ($affiliations as $a)
                <img src="{{ asset('storage/'.$a->photo_url) }}" alt="Affiliation logo" class="h-12 w-auto object-contain" title="Affiliation">
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-semibold text-gray-900 mt-8 mb-3">Branches</h2>
    <div class="space-y-4">
        @foreach ($garage->branches as $branch)
            <div class="bg-white shadow-sm rounded-2xl p-5">
                <h3 class="font-semibold text-gray-900">{{ $branch->name }}</h3>
                @if ($branch->address)<p class="text-sm text-gray-600">{{ $branch->address }}</p>@endif
                @if ($branch->phone)<p class="text-sm text-gray-500">{{ $branch->phone }}</p>@endif

                @if ($branch->services->isNotEmpty())
                    <table class="mt-3 w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($branch->services as $gs)
                                <tr>
                                    <td class="py-1 text-gray-700">{{ $gs->service?->name }}</td>
                                    <td class="py-1 text-right text-gray-900">{{ $gs->price ? 'R'.number_format($gs->price, 2) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-xs text-gray-400 mt-2">No services listed.</p>
                @endif
            </div>
        @endforeach
    </div>

    @if ($gallery->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-900 mt-8 mb-3">Photos</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($gallery as $photo)
                <img src="{{ asset('storage/'.$photo->photo_url) }}" alt="" class="h-32 w-full object-cover rounded-2xl shadow-sm">
            @endforeach
        </div>
    @endif

    @if ($garage->reviews->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-900 mt-8 mb-3">Reviews</h2>
        <div class="space-y-3">
            @foreach ($garage->reviews as $review)
                <div class="bg-white shadow-sm rounded-2xl p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-900">{{ $review->user?->name }}</span>
                        <span class="text-amber-500">{!! str_repeat('★', $review->rating) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - $review->rating) !!}</span></span>
                    </div>
                    @if ($review->comment)
                        <p class="text-sm text-gray-600 mt-1">{{ $review->comment }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    @endif
</x-public-layout>
