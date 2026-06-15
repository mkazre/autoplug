@php
    $brand = \App\Support\Settings::get('brand_name', 'Autoplug');
    $canRequest = auth()->check() && auth()->user()->hasRole('car_owner');
@endphp
<x-public-layout :title="'Find a garage — '.$brand">
    <style>
        .ap-pin { display:inline-flex; align-items:center; gap:6px; background:var(--ap-color,#7c3aed); color:#fff; padding:4px 10px 4px 6px; border-radius:9999px; font-size:12px; font-weight:600; line-height:1; box-shadow:0 2px 6px rgba(0,0,0,.3); white-space:nowrap; max-width:170px; }
        .ap-pin-dot { width:8px; height:8px; border-radius:9999px; background:#fff; flex:none; }
        .ap-pin-label { overflow:hidden; text-overflow:ellipsis; }
    </style>

    <h1 class="text-2xl font-bold mb-3 text-gray-900">Find a garage near you</h1>

    {{-- Map on top --}}
    <div id="map" class="w-full rounded-2xl shadow-sm" style="height: 500px;"></div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('search') }}" id="search-form" x-data="{ r: {{ $radius }} }"
          class="bg-white shadow-sm rounded-2xl p-4 mt-6">
        <input type="hidden" name="lat" id="lat" value="{{ $lat }}">
        <input type="hidden" name="lng" id="lng" value="{{ $lng }}">
        <input type="hidden" name="searched" id="searched" value="{{ request('searched') }}">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label for="address" class="block text-sm font-medium text-gray-700">Location</label>
                <input id="address" name="address" type="text" value="{{ $address }}" placeholder="Suburb, city or address"
                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm h-10" autocomplete="off"
                       oninput="document.getElementById('lat').value=''; document.getElementById('lng').value='';">
            </div>

            <div class="md:col-span-3">
                <label for="service_id" class="block text-sm font-medium text-gray-700">Service (optional)</label>
                <select id="service_id" name="service_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm h-10">
                    <option value="">Any service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected((string) $serviceId === (string) $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="radius" class="block text-sm font-medium text-gray-700">Radius: <span x-text="r"></span> km</label>
                <input id="radius" name="radius" type="range" min="{{ $radiusMin }}" max="{{ $radiusMax }}" step="5" x-model="r"
                       :style="`background: linear-gradient(to right, #7c3aed 0%, #7c3aed ${((r-{{ $radiusMin }})/({{ $radiusMax }}-{{ $radiusMin }}))*100}%, #e5e7eb ${((r-{{ $radiusMin }})/({{ $radiusMax }}-{{ $radiusMin }}))*100}%, #e5e7eb 100%)`"
                       class="mt-3 w-full cursor-pointer">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-transparent select-none">Search</label>
                <button type="submit" class="mt-1 w-full inline-flex justify-center items-center h-10 px-4 bg-violet-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Search</button>
            </div>
        </div>

        <button type="button" onclick="useMyLocation()" class="mt-3 text-xs text-violet-600 hover:text-violet-800">📍 Use my current location</button>
    </form>

    @if ($error)
        <div class="mt-4 p-3 bg-amber-50 text-amber-700 text-sm rounded-lg">{{ $error }}</div>
    @endif

    {{-- Results --}}
    <div class="mt-8">
        @if ($lat && $lng)
            <h2 class="text-lg font-bold text-gray-900 mb-3">{{ $branches->count() }} garage(s) within {{ $radius }} km</h2>
        @endif

        @if ($branches->isNotEmpty() && ! $canRequest)
            <div class="mb-4 p-3 bg-violet-50 text-violet-700 text-sm rounded-lg">
                @auth Switch to a car-owner account to request quotes. @else <a href="{{ route('login') }}" class="underline font-medium">Log in</a> as a car owner to request quotes. @endauth
            </div>
        @endif

        @if ($canRequest && $branches->isNotEmpty())
            <form method="POST" action="{{ route('quotes.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="lat" value="{{ $lat }}">
                <input type="hidden" name="lng" value="{{ $lng }}">
                <input type="hidden" name="radius" value="{{ $radius }}">
                <input type="hidden" name="service_id" value="{{ $serviceId }}">

                <div class="bg-white shadow-sm rounded-2xl p-5 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Request quotes</h3>
                    <label class="block text-sm text-gray-700">Describe the job</label>
                    <textarea name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="e.g. Car pulls left when braking">{{ old('description') }}</textarea>
                    <div class="mt-3" x-data="{ vid: '{{ $vehicles->first()?->id ?? 'new' }}' }">
                        <label class="block text-sm text-gray-700">Vehicle</label>
                        <select name="vehicle_id" x-model="vid" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->make }} {{ $v->model }} {{ $v->year }} {{ $v->registration }}</option>
                            @endforeach
                            <option value="new">+ Add a new vehicle</option>
                        </select>
                        <div x-show="vid === 'new'" class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                            <input name="vehicle_make" value="{{ old('vehicle_make') }}" placeholder="Make" class="border-gray-300 rounded-lg shadow-sm text-sm">
                            <input name="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="Model" class="border-gray-300 rounded-lg shadow-sm text-sm">
                            <input name="vehicle_year" value="{{ old('vehicle_year') }}" placeholder="Year" class="border-gray-300 rounded-lg shadow-sm text-sm">
                            <input name="vehicle_reg" value="{{ old('vehicle_reg') }}" placeholder="Reg" class="border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                    </div>
                    <div class="mt-3" x-data="{ files: [], max: 5,
                            add(e){ this.files = this.files.concat(Array.from(e.target.files)).slice(0, this.max); this.sync(e.target); },
                            remove(i, input){ this.files.splice(i, 1); this.sync(input); },
                            sync(input){ let dt = new DataTransfer(); this.files.forEach(f => dt.items.add(f)); input.files = dt.files; },
                            url(f){ return URL.createObjectURL(f); } }">
                        <label class="block text-sm text-gray-700">Add photos (optional, up to 5)</label>
                        <input type="file" name="images[]" accept="image/*" multiple x-ref="imgs" @change="add($event)"
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        <p class="text-xs text-gray-400 mt-1" x-show="files.length"><span x-text="files.length"></span> of 5 selected</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <template x-for="(f, i) in files" :key="i">
                                <div class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200">
                                    <img :src="url(f)" class="w-full h-full object-cover" alt="">
                                    <button type="button" @click="remove(i, $refs.imgs)" class="absolute top-0 right-0 bg-black/50 text-white text-xs w-5 h-5 leading-5 text-center">&times;</button>
                                </div>
                            </template>
                        </div>
                        <x-input-error :messages="$errors->get('images')" class="mt-2" />
                        <x-input-error :messages="$errors->get('images.0')" class="mt-1" />
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Tick the garages below, then send.</p>
                    <x-input-error :messages="$errors->get('branch_ids')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($branches as $branch)
                        <label class="bg-white shadow-sm rounded-2xl p-5 hover:shadow-md transition cursor-pointer flex items-start gap-3">
                            <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" class="mt-1 rounded border-gray-300 text-violet-600">
                            <span class="flex-1">
                                <span class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900 inline-flex items-center gap-2">{{ $branch->garage->name }} <x-verified-badge :garage="$branch->garage" /></span>
                                    <span class="shrink-0 inline-flex px-2.5 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-medium">{{ round($branch->distance, 1) }} km</span>
                                </span>
                                <span class="block text-sm text-gray-600 mt-0.5">{{ $branch->name }}@if ($branch->address) — {{ $branch->address }}@endif</span>
                                @if ($branch->services->isNotEmpty())
                                    <span class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($branch->services->take(6) as $gs)
                                            <span class="inline-flex px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">{{ $gs->service?->name }}@if ($gs->price) — R{{ number_format($gs->price, 0) }}@endif</span>
                                        @endforeach
                                    </span>
                                @endif
                                <x-affiliation-logos :garage="$branch->garage" />
                            </span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="mt-4 inline-flex items-center px-5 py-2.5 bg-violet-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-500">Send request to selected garages</button>
            </form>
        @elseif ($branches->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($branches as $branch)
                    <div class="bg-white shadow-sm rounded-2xl p-5 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 flex items-center gap-2">{{ $branch->garage->name }} <x-verified-badge :garage="$branch->garage" /></h3>
                                <p class="text-sm text-gray-600">{{ $branch->name }}@if ($branch->address) — {{ $branch->address }}@endif</p>
                            </div>
                            <span class="shrink-0 inline-flex px-2.5 py-0.5 rounded-full bg-violet-100 text-violet-700 text-xs font-medium">{{ round($branch->distance, 1) }} km</span>
                        </div>
                        @if ($branch->services->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($branch->services->take(6) as $gs)
                                    <span class="inline-flex px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">{{ $gs->service?->name }}@if ($gs->price) — R{{ number_format($gs->price, 0) }}@endif</span>
                                @endforeach
                            </div>
                        @endif
                        <x-affiliation-logos :garage="$branch->garage" />
                    </div>
                @endforeach
            </div>
        @elseif ($lat && $lng)
            <p class="text-sm text-gray-500">No garages found in this area. Try a larger radius.</p>
        @else
            <p class="text-sm text-gray-500">Enter a location above and search to see nearby garages.</p>
        @endif
    </div>

    <script>
        window.branchMarkers = @json($markers);
        window.searchCenter = @if ($lat && $lng) { lat: {{ $lat }}, lng: {{ $lng }} } @else null @endif;

        let map;

        function apContent(label, color) {
            const el = document.createElement('div');
            el.className = 'ap-pin';
            el.style.setProperty('--ap-color', color);
            const dot = document.createElement('span');
            dot.className = 'ap-pin-dot';
            const txt = document.createElement('span');
            txt.className = 'ap-pin-label';
            txt.textContent = label;
            el.appendChild(dot);
            el.appendChild(txt);
            return el;
        }

        function initMap() {
            const fallback = { lat: -26.2041, lng: 28.0473 };
            const center = window.searchCenter || fallback;
            map = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: window.searchCenter ? 11 : 6,
                mapId: 'DEMO_MAP_ID',
            });

            const AdvancedMarker = google.maps.marker.AdvancedMarkerElement;

            if (window.searchCenter) {
                new AdvancedMarker({ map: map, position: window.searchCenter, content: apContent('You', '#2563eb'), title: 'Your location' });
            }

            (window.branchMarkers || []).forEach(function (b) {
                const marker = new AdvancedMarker({ map: map, position: { lat: b.lat, lng: b.lng }, content: apContent(b.garage, '#7c3aed'), title: b.name });
                const info = new google.maps.InfoWindow({ content: '<strong>' + b.garage + '</strong><br>' + b.name + '<br>' + b.distance + ' km' });
                marker.addListener('click', function () { info.open({ map: map, anchor: marker }); });
            });

            const input = document.getElementById('address');
            if (input && google.maps.places && google.maps.places.Autocomplete) {
                const ac = new google.maps.places.Autocomplete(input, { fields: ['geometry'] });
                ac.addListener('place_changed', function () {
                    const place = ac.getPlace();
                    if (place.geometry) {
                        document.getElementById('lat').value = place.geometry.location.lat();
                        document.getElementById('lng').value = place.geometry.location.lng();
                    }
                });
            }

            const _addr = document.getElementById('address').value.trim();
            if (_addr && !document.getElementById('lat').value && !document.getElementById('searched').value) {
                document.getElementById('searched').value = '1';
                new google.maps.Geocoder().geocode({ address: _addr }, function (results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('lat').value = results[0].geometry.location.lat();
                        document.getElementById('lng').value = results[0].geometry.location.lng();
                    }
                    document.getElementById('search-form').submit();
                });
            }
        }

        function useMyLocation() {
            if (!navigator.geolocation) { alert('Geolocation is not supported by your browser.'); return; }
            navigator.geolocation.getCurrentPosition(function (pos) {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('lng').value = pos.coords.longitude;
                document.getElementById('search-form').submit();
            }, function () { alert('Could not get your location.'); });
        }

        document.getElementById('search-form').addEventListener('submit', function (e) {
            document.getElementById('searched').value = '1';
            const addr = document.getElementById('address').value.trim();
            const lat = document.getElementById('lat').value;
            if (addr && !lat && window.google && window.google.maps) {
                e.preventDefault();
                new google.maps.Geocoder().geocode({ address: addr }, function (results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('lat').value = results[0].geometry.location.lat();
                        document.getElementById('lng').value = results[0].geometry.location.lng();
                    }
                    document.getElementById('search-form').submit();
                });
            }
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('maps.key') }}&libraries=places,marker&loading=async&callback=initMap" async defer></script>
</x-public-layout>
