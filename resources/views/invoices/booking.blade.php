@php
    $brand = \App\Support\Settings::get('brand_name', 'Autoplug');
    $cur = \App\Support\Settings::get('currency_symbol', 'R');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { margin: 0; font-size: 22px; }
        h2 { font-size: 16px; color: #555; margin: 4px 0 16px; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { text-align: left; padding: 6px 4px; border-bottom: 1px solid #ddd; }
        .right { text-align: right; }
        .total td { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .muted { color: #777; margin-top: 28px; }
    </style>
</head>
<body>
    <h1>{{ $brand }}</h1>
    <h2>Invoice #{{ $booking->id }}</h2>

    <div class="meta">
        <p><strong>Date:</strong> {{ $booking->payment->paid_at?->format('d M Y') }}</p>
        <p><strong>Reference:</strong> {{ $booking->payment->reference }}</p>
        <p><strong>Garage:</strong> {{ $booking->branch?->garage?->name }} — {{ $booking->branch?->name }}</p>
        <p><strong>Customer:</strong> {{ $booking->user?->name }} ({{ $booking->user?->email }})</p>
        <p><strong>Scheduled:</strong> {{ $booking->scheduled_at?->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr><th>Item</th><th class="right">Price</th></tr>
        </thead>
        <tbody>
            @if (is_array($booking->quote?->items_json))
                @foreach ($booking->quote->items_json as $item)
                    <tr>
                        <td>{{ $item['description'] ?? '' }}</td>
                        <td class="right">{{ $cur }}{{ number_format((float) ($item['price'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            @endif
            <tr class="total">
                <td>Total paid</td>
                <td class="right">{{ $cur }}{{ number_format((float) $booking->payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="muted">Thank you for using {{ $brand }}.</p>
</body>
</html>
