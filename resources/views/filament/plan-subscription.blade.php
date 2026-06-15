<div style="font-size:13px;color:#374151;">
    <div style="margin-bottom:10px;">
        <strong>{{ $sub->product?->name }}</strong> — {{ $sub->user?->name }} ({{ $sub->user?->email }})<br>
        Status: {{ ucfirst($sub->status) }} • Paid R{{ number_format((float) $sub->total_paid, 2) }} • Balance R{{ number_format((float) $sub->current_balance, 2) }}
    </div>

    <div style="font-weight:600;color:#6d28d9;margin-top:8px;">Installments</div>
    <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <tr style="color:#6b7280;text-align:left;"><th style="padding:4px 6px;">#</th><th>Due</th><th>Amount</th><th>Status</th><th>Paid</th></tr>
        @foreach ($sub->installments as $i)
            <tr style="border-top:1px solid #eee;"><td style="padding:4px 6px;">{{ $i->installment_number }}</td><td>{{ $i->due_date?->format('d M Y') }}</td><td>R{{ number_format((float) $i->amount_due, 2) }}</td><td>{{ ucfirst($i->status) }}</td><td>{{ $i->paid_at?->format('d M Y') }}</td></tr>
        @endforeach
    </table>

    <div style="font-weight:600;color:#6d28d9;margin-top:14px;">Payments</div>
    <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <tr style="color:#6b7280;text-align:left;"><th style="padding:4px 6px;">Date</th><th>Amount</th><th>Reference</th><th>Status</th></tr>
        @forelse ($sub->payments as $p)
            <tr style="border-top:1px solid #eee;"><td style="padding:4px 6px;">{{ ($p->paid_at ?? $p->created_at)?->format('d M Y H:i') }}</td><td>R{{ number_format((float) $p->amount, 2) }}</td><td>{{ $p->reference }}</td><td>{{ ucfirst($p->status) }}</td></tr>
        @empty
            <tr><td colspan="4" style="color:#9ca3af;padding:4px 6px;">No payments yet.</td></tr>
        @endforelse
    </table>
</div>
