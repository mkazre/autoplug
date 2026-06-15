<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Apply') }} — {{ $planProduct->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg">
                    <ul class="list-disc ps-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('plans.store', $planProduct) }}" enctype="multipart/form-data" x-data="planApply()" x-init="init()">
                @csrf

                <div class="flex items-center gap-2 mb-4 text-xs text-gray-500">
                    <template x-for="(label, i) in steps" :key="i">
                        <span class="px-2 py-1 rounded-full" :class="step === (i + 1) ? 'bg-violet-600 text-white' : 'bg-gray-100'" x-text="(i + 1) + '. ' + label"></span>
                    </template>
                </div>

                <!-- Step 1: Vehicle -->
                <div x-show="step === 1" class="bg-white shadow-sm rounded-2xl p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Vehicle</h3>
                    <select name="vehicle_id" x-model="vehicleId" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        <option value="new">+ Add a new vehicle</option>
                        @foreach ($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->make }} {{ $v->model }} {{ $v->year }} {{ $v->registration }}</option>
                        @endforeach
                    </select>
                    <div x-show="vehicleId === 'new'" class="grid grid-cols-2 gap-2">
                        <input name="vehicle_make" value="{{ old('vehicle_make') }}" placeholder="Make" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="Model" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="vehicle_year" value="{{ old('vehicle_year') }}" placeholder="Year" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        <input name="vehicle_reg" value="{{ old('vehicle_reg') }}" placeholder="Registration" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm text-gray-700">Current mileage (km)</label>
                            <input name="odometer_km" type="number" min="0" value="{{ old('odometer_km') }}" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">First registered on</label>
                            <input name="first_registered_on" type="date" value="{{ old('first_registered_on') }}" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Eligibility: up to {{ number_format((int) $planProduct->max_km) }} km &amp; {{ (int) $planProduct->max_age_years }} years@if ($planProduct->requires_full_history); full service history required @endif.</p>
                    <div class="text-right"><button type="button" @click="step = 2" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold">Next</button></div>
                </div>

                <!-- Step 2: Plan / tier -->
                <div x-show="step === 2" class="bg-white shadow-sm rounded-2xl p-6 space-y-3">
                    <h3 class="font-semibold text-gray-900">Choose your cover</h3>
                    @forelse ($planProduct->pricingTiers as $t)
                        <label class="block border rounded-lg p-3 cursor-pointer hover:border-violet-400">
                            <input type="radio" name="pricing_tier_id" value="{{ $t->id }}" class="text-violet-600">
                            <span class="font-medium text-gray-900">{{ $t->vehicle_category }}</span>
                            <span class="text-sm text-gray-600"> — {{ $t->term_months }} months • R{{ number_format((float) $t->monthly_price, 2) }}/mo • R{{ number_format((float) $t->upfront_price, 2) }} upfront</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">No pricing tiers configured for this plan yet.</p>
                    @endforelse
                    <div class="flex justify-between"><button type="button" @click="step = 1" class="text-sm text-gray-500">Back</button><button type="button" @click="step = 3" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold">Next</button></div>
                </div>

                <!-- Step 3: Documents -->
                <div x-show="step === 3" class="bg-white shadow-sm rounded-2xl p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Documents</h3>
                    <p class="text-xs text-gray-500">PDF or image, max 8 MB each. Stored privately.</p>
                    <div><label class="block text-sm text-gray-700">Vehicle registration papers</label><input type="file" name="registration_papers" accept=".pdf,image/*" class="mt-1 block text-sm"></div>
                    <div><label class="block text-sm text-gray-700">ID document</label><input type="file" name="id_document" accept=".pdf,image/*" class="mt-1 block text-sm"></div>
                    <div><label class="block text-sm text-gray-700">Proof of address</label><input type="file" name="proof_of_address" accept=".pdf,image/*" class="mt-1 block text-sm"></div>
                    @if ($planProduct->type === 'maintenance')
                        <div><label class="block text-sm text-gray-700">Proof of service history</label><input type="file" name="service_history" accept=".pdf,image/*" class="mt-1 block text-sm"></div>
                    @endif
                    <div class="flex justify-between"><button type="button" @click="step = 2" class="text-sm text-gray-500">Back</button><button type="button" @click="step = 4" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold">Next</button></div>
                </div>

                <!-- Step 4: Terms -->
                <div x-show="step === 4" class="bg-white shadow-sm rounded-2xl p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Terms &amp; Conditions ({{ $planProduct->terms_version }})</h3>
                    @if ($planProduct->terms_doc_path)
                        <a href="{{ asset('storage/'.$planProduct->terms_doc_path) }}" target="_blank" class="text-violet-600 underline text-sm">Read the full Terms &amp; Conditions (PDF)</a>
                    @else
                        <p class="text-sm text-gray-500">By proceeding you agree to Autoplug's plan terms.</p>
                    @endif
                    <label class="flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="agree_terms" value="1" class="rounded border-gray-300 text-violet-600"> I have read and agree to the Terms &amp; Conditions.</label>
                    <div class="flex justify-between"><button type="button" @click="step = 3" class="text-sm text-gray-500">Back</button><button type="button" @click="step = 5" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold">Next</button></div>
                </div>

                <!-- Step 5: Signature -->
                <div x-show="step === 5" class="bg-white shadow-sm rounded-2xl p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Sign</h3>
                    <div><label class="block text-sm text-gray-700">Full name</label><input name="signatory_name" value="{{ old('signatory_name') }}" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Signature</label>
                        <canvas x-ref="sig" width="500" height="180" class="w-full max-w-full border border-gray-300 rounded-lg bg-white" style="touch-action:none;"></canvas>
                        <button type="button" @click="clearSig()" class="mt-1 text-xs text-gray-500 hover:text-gray-700">Clear signature</button>
                        <input type="hidden" name="signature" x-ref="sigInput">
                    </div>
                    <div class="flex justify-between"><button type="button" @click="step = 4" class="text-sm text-gray-500">Back</button><button type="button" @click="step = 6" class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-semibold">Next</button></div>
                </div>

                <!-- Step 6: Payment + submit -->
                <div x-show="step === 6" class="bg-white shadow-sm rounded-2xl p-6 space-y-4">
                    <h3 class="font-semibold text-gray-900">Payment</h3>
                    <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio" name="payment_method" value="upfront" class="text-violet-600"> Pay the full amount upfront</label>
                    <label class="flex items-center gap-2 text-sm text-gray-700"><input type="radio" name="payment_method" value="installments" class="text-violet-600"> Monthly installments</label>
                    <p class="text-xs text-gray-500">You'll receive a secure payment link once your application is approved.</p>
                    <div class="flex justify-between"><button type="button" @click="step = 5" class="text-sm text-gray-500">Back</button><button type="submit" class="px-5 py-2.5 bg-violet-600 text-white rounded-lg text-sm font-semibold uppercase tracking-widest hover:bg-violet-500">Submit application</button></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function planApply() {
            return {
                step: 1,
                vehicleId: 'new',
                steps: ['Vehicle', 'Cover', 'Docs', 'Terms', 'Sign', 'Pay'],
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
</x-app-layout>
