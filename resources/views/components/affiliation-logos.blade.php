@props(['garage'])
@php
    $logos = $garage->relationLoaded('photos')
        ? $garage->photos->where('type', 'affiliation')
        : $garage->photos()->where('type', 'affiliation')->get();
@endphp
@if ($logos->isNotEmpty())
    <span class="flex flex-wrap items-center gap-3 mt-3">
        @foreach ($logos->take(5) as $l)
            <img src="{{ asset('storage/'.$l->photo_url) }}" alt="" class="h-6 w-auto object-contain" title="Affiliation">
        @endforeach
    </span>
@endif
