@php
    $sig = $app->signature;
    $sigUri = null;
    if ($sig && \Illuminate\Support\Facades\Storage::disk('local')->exists($sig->signature_path)) {
        $sigUri = 'data:image/png;base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($sig->signature_path));
    }
@endphp
<div class="space-y-3 text-sm">
    <div><span class="text-gray-500">Customer:</span> {{ $app->user?->name }} ({{ $app->user?->email }}{{ $app->user?->phone ? ', '.$app->user->phone : '' }})</div>
    <div><span class="text-gray-500">Vehicle:</span> {{ $app->vehicle?->make }} {{ $app->vehicle?->model }} {{ $app->vehicle?->year }} {{ $app->vehicle?->registration }} — {{ number_format((int) $app->vehicle?->odometer_km) }} km, first reg {{ optional($app->vehicle?->first_registered_on)->format('Y-m-d') }}</div>
    <div><span class="text-gray-500">Plan:</span> {{ $app->product?->name }} — {{ $app->tier?->vehicle_category }} ({{ $app->tier?->term_months }} mo, {{ ucfirst($app->payment_method ?? '') }})</div>
    <div>
        <span class="text-gray-500">Documents:</span>
        <ul class="list-disc ps-5">
            @forelse ($app->documents as $doc)
                <li><a href="{{ route('plans.documents.show', $doc) }}" target="_blank" class="text-primary-600 underline">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</a></li>
            @empty
                <li class="text-gray-400">No documents</li>
            @endforelse
        </ul>
    </div>
    @if ($sig)
        <div>
            <span class="text-gray-500">Signature:</span> {{ $sig->signatory_name }} — {{ $sig->signed_at?->format('Y-m-d H:i') }}, IP {{ $sig->ip_address }}, T&amp;Cs {{ $sig->terms_version }}
            @if ($sigUri)<div class="mt-1"><img src="{{ $sigUri }}" alt="Signature" class="border rounded bg-white" style="max-height:110px"></div>@endif
        </div>
    @endif
</div>
