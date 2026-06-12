@php $d = $data ?? []; @endphp
@if (!empty($d['content']))
    <section class="mt-12 bg-white rounded-2xl p-8 shadow-sm text-gray-700 leading-relaxed">
        {!! $d['content'] !!}
    </section>
@endif
