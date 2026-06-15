<div style="font-size:13px;color:#374151;">
    <div><strong>R{{ number_format((float) $p->amount, 2) }}</strong> — {{ ucfirst($p->gateway) }}@if ($p->method) / {{ strtoupper($p->method) }}@endif</div>
    <div style="color:#6b7280;">Customer: {{ $p->subscription?->user?->name }} • Installment #{{ $p->installment?->installment_number }}</div>
    <div style="color:#6b7280;">Reference: {{ $p->txn_reference ?? $p->reference }}</div>
    <div style="color:#6b7280;">Paid on: {{ optional($p->paid_on)->format('d M Y') ?? $p->paid_at?->format('d M Y') ?? '—' }} • Status: {{ ucfirst($p->status) }}@if ($p->verified_at) (verified {{ $p->verified_at->format('d M Y') }})@endif</div>
    @if ($p->proof_path)
        <div style="margin-top:8px;"><a href="{{ route('plans.payment-proof', $p) }}" target="_blank" style="color:#6d28d9;text-decoration:underline;">View proof of payment</a></div>
    @endif
</div>
