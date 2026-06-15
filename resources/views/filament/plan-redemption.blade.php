<div style="font-size:13px;color:#374151;">
    <div><strong>{{ $r->subscription?->product?->name }}</strong> — {{ $r->subscription?->user?->name }}</div>
    <div style="color:#6b7280;">Garage: {{ $r->branch?->garage?->name }} • Booking #{{ $r->booking_id }} • {{ ucfirst(str_replace('_', ' ', (string) $r->redemption_type)) }}</div>
    @if ($r->description)<div style="margin-top:6px;">{{ $r->description }}</div>@endif
    <table style="width:100%;border-collapse:collapse;font-size:12px;margin-top:8px;">
        <tr style="color:#6b7280;text-align:left;"><th style="padding:4px 6px;">Item</th><th>Qty</th><th>Cost</th></tr>
        @foreach ((array) $r->items_json as $line)
            <tr style="border-top:1px solid #eee;"><td style="padding:4px 6px;">{{ $line['item_name'] ?? '' }}</td><td>{{ $line['qty'] ?? 1 }}</td><td>R{{ number_format((float) ($line['cost'] ?? 0), 2) }}</td></tr>
        @endforeach
        <tr style="border-top:2px solid #ddd;font-weight:bold;"><td style="padding:4px 6px;">Claimed</td><td></td><td>R{{ number_format((float) $r->amount_claimed, 2) }}</td></tr>
    </table>
</div>
