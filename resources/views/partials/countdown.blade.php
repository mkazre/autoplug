{{-- Live countdown to $expires (Carbon). Optional: $expiredLabel, $expiredClass, $class --}}
<span x-data="{
        end: {{ $expires->getTimestamp() * 1000 }},
        now: Date.now(),
        get expired() { return this.now >= this.end; },
        fmt() {
            let s = Math.max(0, Math.floor((this.end - this.now) / 1000));
            let d = Math.floor(s / 86400); s -= d * 86400;
            let h = Math.floor(s / 3600); s -= h * 3600;
            let m = Math.floor(s / 60); s -= m * 60;
            let out = [];
            if (d) out.push(d + 'd');
            if (h || d) out.push(h + 'h');
            out.push(m + 'm');
            if (!d) out.push(s + 's');
            return out.join(' ');
        }
     }"
     x-init="setInterval(() => now = Date.now(), 1000)">
    <template x-if="!expired"><span class="{{ $class ?? 'font-medium' }}" x-text="fmt()"></span></template>
    <template x-if="expired"><span class="{{ $expiredClass ?? 'text-red-600 font-medium' }}">{{ $expiredLabel ?? 'Expired' }}</span></template>
</span>
