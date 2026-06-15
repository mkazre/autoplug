@php
    use Illuminate\Support\Facades\Storage;
    $sig = $app->signature;
    $sigUri = ($sig && Storage::disk('local')->exists($sig->signature_path))
        ? 'data:image/png;base64,'.base64_encode(Storage::disk('local')->get($sig->signature_path))
        : null;
    $v = $app->vehicle;
    $label = fn ($t) => ucfirst(str_replace('_', ' ', $t));
    $cardStyle = 'border:1px solid #e5e7eb;border-radius:12px;padding:14px;background:#fff;';
    $capStyle = 'display:flex;align-items:center;gap:6px;color:#8b5cf6;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;';
    $sv = 'width:15px;height:15px;flex:none;';
@endphp
<div style="font-size:13px;color:#374151;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="{{ $cardStyle }}">
            <div style="{{ $capStyle }}">
                <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Customer
            </div>
            <div style="margin-top:6px;font-weight:600;color:#111827;">{{ $app->user?->name }}</div>
            <div style="color:#6b7280;">{{ $app->user?->email }}</div>
            @if ($app->user?->phone)<div style="color:#6b7280;">{{ $app->user->phone }}</div>@endif
        </div>

        <div style="{{ $cardStyle }}">
            <div style="{{ $capStyle }}">
                <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25m-9.75 0h9.75"/></svg>
                Vehicle
            </div>
            <div style="margin-top:6px;font-weight:600;color:#111827;">{{ $v?->make }} {{ $v?->model }} {{ $v?->year }}</div>
            <div style="color:#6b7280;">{{ $v?->registration }} • {{ number_format((int) $v?->odometer_km) }} km</div>
            <div style="color:#6b7280;">First reg {{ optional($v?->first_registered_on)->format('d M Y') }}</div>
        </div>

        <div style="{{ $cardStyle }}">
            <div style="{{ $capStyle }}">
                <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                Plan
            </div>
            <div style="margin-top:6px;font-weight:600;color:#111827;">{{ $app->product?->name }}</div>
            <div style="color:#6b7280;">{{ $app->tier?->vehicle_category }} • {{ $app->tier?->term_months }} months</div>
        </div>

        <div style="{{ $cardStyle }}">
            <div style="{{ $capStyle }}">
                <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                Payment
            </div>
            <div style="margin-top:6px;font-weight:600;color:#111827;">{{ ucfirst($app->payment_method ?? '—') }}</div>
            <div style="color:#6b7280;">{{ $app->tier ? 'R'.number_format((float) ($app->payment_method === 'installments' ? $app->tier->monthly_price : $app->tier->upfront_price), 2).($app->payment_method === 'installments' ? '/mo' : ' once-off') : '' }}</div>
        </div>
    </div>

    <div style="margin-top:14px;">
        <div style="{{ $capStyle }}">
            <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Documents
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
            @forelse ($app->documents as $doc)
                <div style="display:flex;align-items:center;gap:10px;border:1px solid #e5e7eb;border-radius:10px;padding:10px;">
                    <div style="width:38px;height:38px;border-radius:8px;background:#eef2ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;flex:none;">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;color:#111827;">{{ $label($doc->document_type) }}</div>
                        <div style="display:flex;gap:8px;margin-top:5px;">
                            <a href="{{ route('plans.documents.show', $doc) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:8px;background:#ede9fe;color:#6d28d9;font-size:12px;font-weight:600;text-decoration:none;">
                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Preview
                            </a>
                            <a href="{{ route('plans.documents.show', $doc) }}?download=1" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:8px;background:#f3f4f6;color:#374151;font-size:12px;font-weight:600;text-decoration:none;">
                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="color:#9ca3af;">No documents uploaded.</div>
            @endforelse
        </div>
    </div>

    @if ($sig)
        <div style="margin-top:14px;{{ $cardStyle }}">
            <div style="{{ $capStyle }}">
                <svg style="{{ $sv }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Customer signature
            </div>
            <div style="color:#6b7280;margin-top:6px;">{{ $sig->signatory_name }} • {{ $sig->signed_at?->format('d M Y H:i') }} • IP {{ $sig->ip_address }} • T&amp;Cs {{ $sig->terms_version }}</div>
            @if ($sigUri)<img src="{{ $sigUri }}" alt="Signature" style="height:80px;margin-top:8px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;">@endif
        </div>
    @endif
</div>
