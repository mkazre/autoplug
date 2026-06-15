@php
    $brand = \App\Support\Settings::get('brand_name', 'Autoplug');
    $cur = \App\Support\Settings::get('currency_symbol', 'R');
    $app = $subscription->application;
    $sig = $app?->signature;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { margin: 0; font-size: 22px; }
        h2 { font-size: 15px; color: #555; margin: 4px 0 14px; }
        h3 { font-size: 13px; margin: 16px 0 6px; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 5px 4px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .right { text-align: right; }
        .muted { color: #777; margin-top: 24px; font-size: 10px; }
        .sig { margin-top: 18px; }
        .sig img { height: 90px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>{{ $brand }}</h1>
    <h2>Plan Contract #{{ $subscription->id }}</h2>

    <div class="meta">
        <p><strong>Plan:</strong> {{ $subscription->product?->name }} ({{ ucfirst($subscription->product?->type) }})</p>
        <p><strong>Holder:</strong> {{ $subscription->user?->name }} ({{ $subscription->user?->email }})</p>
        <p><strong>Vehicle:</strong> {{ $subscription->vehicle?->make }} {{ $subscription->vehicle?->model }} {{ $subscription->vehicle?->year }} {{ $subscription->vehicle?->registration }}</p>
        <p><strong>Mileage at start:</strong> {{ number_format((int) $subscription->km_at_start) }} km</p>
        <p><strong>Cover:</strong> {{ $subscription->tier?->vehicle_category }} — {{ $subscription->tier?->term_months }} months</p>
        <p><strong>Term:</strong> {{ $subscription->start_date?->format('d M Y') }} to {{ $subscription->end_date?->format('d M Y') }}</p>
        <p><strong>Payment:</strong> {{ ucfirst($app?->payment_method ?? '') }} • Total {{ $cur }}{{ number_format((float) $subscription->current_balance, 2) }}</p>
        <p><strong>T&amp;Cs version:</strong> {{ $sig?->terms_version ?? $app?->terms_version }}</p>
    </div>

    <h3>Payment schedule</h3>
    <table>
        <thead><tr><th>#</th><th>Due date</th><th class="right">Amount</th></tr></thead>
        <tbody>
            @foreach ($subscription->installments()->orderBy('installment_number')->get() as $inst)
                <tr>
                    <td>{{ $inst->installment_number }}</td>
                    <td>{{ $inst->due_date?->format('d M Y') }}</td>
                    <td class="right">{{ $cur }}{{ number_format((float) $inst->amount_due, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sig">
        <h3>Signed</h3>
        <p>{{ $sig?->signatory_name }} — {{ $sig?->signed_at?->format('d M Y H:i') }} (IP {{ $sig?->ip_address }})</p>
        @if ($sigDataUri)<img src="{{ $sigDataUri }}" alt="Signature">@endif
    </div>

    <p class="muted">This contract is governed by the {{ $brand }} plan Terms &amp; Conditions (version {{ $sig?->terms_version ?? $app?->terms_version }}) agreed by the holder at the time of signing. Generated {{ now()->format('d M Y H:i') }}.</p>
</body>
</html>
