@php $brand = \App\Support\Settings::get('brand_name', 'Autoplug'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approve &amp; sign — {{ $brand }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-lg font-bold">Approve &amp; sign</h1>
            <a href="{{ url('/admin/plan-applications') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back</a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-6">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded">
                <ul class="list-disc ps-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-semibold text-gray-900">{{ $app->product?->name }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $app->user?->name }} • {{ $app->vehicle?->make }} {{ $app->vehicle?->model }} {{ $app->vehicle?->year }} ({{ $app->vehicle?->registration }})</p>
            <p class="text-sm text-gray-600">{{ $app->tier?->vehicle_category }} — {{ $app->tier?->term_months }} months • {{ ucfirst($app->payment_method ?? '') }}</p>

            <form method="POST" action="{{ route('plan-admin.approve', $app) }}" x-data="adminSign()" x-init="init()" class="mt-5 space-y-4">
                @csrf
                <div class="text-xs text-gray-500 bg-gray-50 rounded p-3">
                    Approving on behalf of <strong>{{ $brand }}</strong>. Recorded with this approval:
                    <div class="mt-1">Approver: <strong>{{ auth()->user()->name }}</strong> • IP: {{ request()->ip() }} • Time: server timestamp at submit</div>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Your signature</label>
                    <canvas x-ref="sig" width="500" height="160" class="w-full max-w-full border border-gray-300 rounded-lg bg-white" style="touch-action:none;"></canvas>
                    <button type="button" @click="clearSig()" class="mt-1 text-xs text-gray-500 hover:text-gray-700">Clear</button>
                    <input type="hidden" name="admin_signature" x-ref="sigInput">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="confirm" value="1" class="rounded border-gray-300 text-violet-600">
                    I confirm this application meets the plan requirements and approve it.
                </label>

                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-violet-600 text-white rounded-lg text-xs font-semibold uppercase tracking-widest hover:bg-violet-500">Approve &amp; sign</button>
            </form>
        </div>
    </main>

    <script>
        function adminSign() {
            return {
                init() { this.initSig(); },
                initSig() {
                    const canvas = this.$refs.sig;
                    if (! canvas) return;
                    const ctx = canvas.getContext('2d');
                    ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#111827';
                    let drawing = false, last = null;
                    const pos = (e) => { const r = canvas.getBoundingClientRect(); const t = e.touches ? e.touches[0] : e; return { x: (t.clientX - r.left) * (canvas.width / r.width), y: (t.clientY - r.top) * (canvas.height / r.height) }; };
                    const start = (e) => { drawing = true; last = pos(e); e.preventDefault(); };
                    const move = (e) => { if (! drawing) return; const p = pos(e); ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(p.x, p.y); ctx.stroke(); last = p; this.$refs.sigInput.value = canvas.toDataURL('image/png'); e.preventDefault(); };
                    const end = () => { drawing = false; };
                    canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', end);
                    canvas.addEventListener('touchstart', start, { passive: false }); canvas.addEventListener('touchmove', move, { passive: false }); canvas.addEventListener('touchend', end);
                },
                clearSig() { const c = this.$refs.sig; c.getContext('2d').clearRect(0, 0, c.width, c.height); this.$refs.sigInput.value = ''; },
            };
        }
    </script>
</body>
</html>
