@php $brand = \App\Support\Settings::get('brand_name', 'Autoplug'); @endphp
<x-public-layout :title="$brand">
    <section class="bg-gradient-to-br from-violet-600 to-fuchsia-600 rounded-3xl text-white p-10 sm:p-16 shadow-lg">
        <div class="max-w-2xl">
            <h1 class="text-3xl sm:text-5xl font-bold leading-tight">Find a trusted garage near you</h1>
            <p class="mt-4 text-violet-100 text-lg">Search nearby garages, compare quotes, book, and pay online — all in one place.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('search') }}" class="px-6 py-3 bg-white text-violet-700 font-semibold rounded-lg hover:bg-violet-50 transition">Find a garage</a>
                <a href="{{ route('garages.index') }}" class="px-6 py-3 bg-white/15 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/25 transition">Browse garages</a>
            </div>
        </div>
    </section>

    <section class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="text-3xl">🔍</div>
            <h3 class="font-semibold mt-3 text-gray-900">Search</h3>
            <p class="text-sm text-gray-600 mt-1">Enter your location and find garages within your chosen radius.</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="text-3xl">💬</div>
            <h3 class="font-semibold mt-3 text-gray-900">Compare quotes</h3>
            <p class="text-sm text-gray-600 mt-1">Request quotes from multiple garages and compare them side by side.</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="text-3xl">✅</div>
            <h3 class="font-semibold mt-3 text-gray-900">Book &amp; pay</h3>
            <p class="text-sm text-gray-600 mt-1">Pick a slot, book, and pay securely online.</p>
        </div>
    </section>

    @if ($featured->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Top-rated garages</h2>
                <a href="{{ route('garages.index') }}" class="text-violet-600 text-sm hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($featured as $garage)
                    <a href="{{ route('garages.show', $garage) }}" class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition block">
                        <div class="flex items-center gap-3">
                            @if ($garage->logo)
                                <img src="{{ asset('storage/'.$garage->logo) }}" alt="" class="h-12 w-12 object-cover rounded">
                            @else
                                <div class="h-12 w-12 rounded bg-violet-100 text-violet-600 flex items-center justify-center font-bold text-lg">{{ substr($garage->name, 0, 1) }}</div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $garage->name }}</h3>
                                @if ($garage->reviews_count)
                                    <p class="text-amber-500 text-sm">{!! str_repeat('★', (int) round($garage->reviews_avg_rating)) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - (int) round($garage->reviews_avg_rating)) !!}</span> <span class="text-xs text-gray-400">({{ $garage->reviews_count }})</span></p>
                                @else
                                    <p class="text-xs text-gray-400">No reviews yet</p>
                                @endif
                            </div>
                        </div>
                        @if ($garage->description)
                            <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $garage->description }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-public-layout>
