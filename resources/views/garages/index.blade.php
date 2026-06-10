<x-public-layout title="Garages — Autoplug">
    <h1 class="text-2xl font-bold mb-4">Browse garages</h1>

    <form method="GET" action="{{ route('garages.index') }}" class="mb-6 flex gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search by name" class="border-gray-300 rounded-md shadow-sm text-sm w-full max-w-sm">
        <button type="submit" class="px-4 py-2 bg-violet-600 text-white rounded-md text-xs uppercase tracking-widest">Search</button>
    </form>

    @if ($garages->isEmpty())
        <p class="text-gray-500">No garages found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($garages as $garage)
                <a href="{{ route('garages.show', $garage) }}" class="bg-white shadow-sm rounded-lg p-5 hover:shadow-md transition block">
                    <div class="flex items-center gap-3">
                        @if ($garage->logo)
                            <img src="{{ asset('storage/'.$garage->logo) }}" alt="" class="h-12 w-12 object-cover rounded">
                        @else
                            <div class="h-12 w-12 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-lg font-bold">{{ substr($garage->name, 0, 1) }}</div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $garage->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $garage->branches->count() }} branch(es)</p>
                            @if ($garage->reviews_count)
                                <p class="text-amber-500 text-sm leading-none mt-0.5">{!! str_repeat('★', (int) round($garage->reviews_avg_rating)) !!}<span class="text-gray-300">{!! str_repeat('★', 5 - (int) round($garage->reviews_avg_rating)) !!}</span> <span class="text-gray-400 text-xs">({{ $garage->reviews_count }})</span></p>
                            @endif
                        </div>
                    </div>
                    @if ($garage->description)
                        <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $garage->description }}</p>
                    @endif
                    @if ($garage->branches->first()?->address)
                        <p class="text-xs text-gray-400 mt-2">{{ $garage->branches->first()->address }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</x-public-layout>
