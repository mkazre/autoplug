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
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { background: #6d28d9; color: #fff; padding: 16px 20px; border-radius: 8px; }
        .header table { width: 100%; }
        .brand { font-size: 22px; font-weight: bold; }
        .doc-title { font-size: 12px; opacity: .9; margin-top: 2px; }
        .logo { height: 38px; }
        h3 { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #6d28d9; margin: 18px 0 6px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .grid td { vertical-align: top; width: 50%; padding: 2px 0; }
        .k { color: #6b7280; }
        table.sched { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.sched th, table.sched td { text-align: left; padding: 5px 4px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .right { text-align: right; }
        .sigbox { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; }
        .sigimg { height: 64px; }
        .muted { color: #6b7280; font-size: 10px; margin-top: 22px; }
    </style>
</head>
<body>
    <div class="header">
        <table><tr>
            <td><div class="brand">{{ $brand }}</div><div class="doc-title">Plan Contract #{{ $subscription->id }}</div></td>
            <td class="right">@if ($logoUri)<img src="{{ $logoUri }}" class="logo">@endif</td>
        </tr></table>
    </div>

    <h3>Plan &amp; holder</h3>
    <table class="grid"><tr>
        <td>
            <span class="k">Plan:</span> {{ $subscription->product?->name }} ({{ ucfirst((string) $subscription->product?->type) }})<br>
            <span class="k">Cover:</span> {{ $subscription->tier?->vehicle_category }} — {{ $subscription->tier?->term_months }} months<br>
            <span class="k">Term:</span> {{ $subscription->start_date?->format('d M Y') }} – {{ $subscription->end_date?->format('d M Y') }}
        </td>
        <td>
            <span class="k">Holder:</span> {{ $subscription->user?->name }}<br>
            <span class="k">Email:</span> {{ $subscription->user?->email }}<br>
            <span class="k">Payment:</span> {{ ucfirst((string) $app?->payment_method) }} • Total {{ $cur }}{{ number_format((float) $subscription->current_balance, 2) }}
        </td>
    </tr></table>

    <h3>Vehicle</h3>
    <p>{{ $subscription->vehicle?->make }} {{ $subscription->vehicle?->model }} {{ $subscription->vehicle?->year }} — {{ $subscription->vehicle?->registration }}, {{ number_format((int) $subscription->km_at_start) }} km at start.</p>

    <h3>Payment schedule</h3>
    <table class="sched"><thead><tr><th>#</th><th>Due date</th><th class="right">Amount</th></tr></thead><tbody>
        @foreach ($subscription->installments()->orderBy('installment_number')->get() as $inst)
            <tr><td>{{ $inst->installment_number }}</td><td>{{ $inst->due_date?->format('d M Y') }}</td><td class="right">{{ $cur }}{{ number_format((float) $inst->amount_due, 2) }}</td></tr>
        @endforeach
    </tbody></table>

    <h3>Signatures</h3>
    <table class="grid"><tr>
        <td style="padding-right:8px;">
            <div class="sigbox">
                <div class="k">Plan holder</div>
                <div style="font-weight:bold;">{{ $sig?->signatory_name }}</div>
                @if ($sigDataUri)<img src="{{ $sigDataUri }}" class="sigimg">@endif
                <div class="k">{{ $sig?->signed_at?->format('d M Y H:i') }} • IP {{ $sig?->ip_address }}</div>
            </div>
        </td>
        <td style="padding-left:8px;">
            <div class="sigbox">
                <div class="k">For {{ $brand }} (approved by)</div>
                <div style="font-weight:bold;">{{ $app?->approver?->name ?? '—' }}</div>
                @if ($adminSigUri)<img src="{{ $adminSigUri }}" class="sigimg">@endif
                <div class="k">{{ $app?->approved_at?->format('d M Y H:i') }} • IP {{ $app?->approved_ip }}</div>
            </div>
        </td>
    </tr></table>

    <p class="muted">Governed by the {{ $brand }} plan Terms &amp; Conditions (version {{ $sig?->terms_version ?? $app?->terms_version }}) agreed by the holder at signing. Generated {{ now()->format('d M Y H:i') }}.</p>
</body>
</html>
