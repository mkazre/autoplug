<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find a garage — Autoplug</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-bold">Autoplug</a>
            <nav class="text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="ms-3 text-gray-600 hover:text-gray-900">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Find a garage near you</h1>

        <form method="GET" action="{{ route('search') }}" id="search-form" x-data="{ r: {{ $radius }} }"
              class="bg-white shadow-sm rounded-lg p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <input type="hidden" name="lat" id="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" id="lng" value="{{ $lng }}">

            <div class="md:col-span-5">
                <label for="address" class="block text-sm font-medium text-gray-700">Location</label>
                <input id="address" name="address" type="text" value="{{ $address }}" placeholder="Suburb, city or address"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"
                       autocomplete="off"
                       oninput="document.getElementById('lat').value=''; document.getElementById('lng').value='';">
                <button type="button" onclick="useMyLocation()" class="text-xs text-indigo-600 hover:text-indigo-800 mt-1">Use my current location</button>
            </div>

            <div class="md:col-span-3">
                <label for="service_id" class="block text-sm font-medium text-gray-700">Service (optional)</label>
                <select id="service_id" name="service_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Any service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected((string) $serviceId === (string) $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="radius" class="block text-sm font-medium text-gray-700">Radius: <span x-text="r"></span> km</label>
                <input id="radius" name="radius" type="range" min="5" max="50" step="5" x-model="r" class="mt-2 w-full">
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Search</button>
            </div>
        </form>

        @if ($error)
            <div class="mt-4 p-3 bg-amber-50 text-amber-700 text-sm rounded">{{ $error }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div>
                @if ($lat && $lng)
                    <p class="text-sm text-gray-500 mb-3">{{ $branches->count() }} garage(s) within {{ $radius }} km.</p>
                @else
                    <p class="text-sm text-gray-500 mb-3">Enter a location and search to see nearby garages.</p>
                @endif

                <div class="space-y-3">
                    @foreach ($branches as $branch)
                        <div class="bg-white shadow-sm rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $branch->garage->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $branch->name }}@if ($branch->address) — {{ $branch->address }}@endif</p>
                                    @if ($branch->phone)
                                        <p class="text-sm text-gray-500">{{ $branch->phone }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-xs">{{ round($branch->distance, 1) }} km</span>
                            </div>
                            @if ($branch->services->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($branch->services->take(6) as $gs)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">
                                            {{ $gs->service?->name }}@if ($gs->price) — R{{ number_format($gs->price, 0) }}@endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div id="map" class="w-full rounded-lg shadow-sm" style="height: 520px;"></div>
            </div>
        </div>
    </main>

    <script>
        window.branchMarkers = @json($markers);
        window.searchCenter = @if ($lat && $lng) { lat: {{ $lat }}, lng: {{ $lng }} } @else null @endif;

        let map;
        function initMap() {
            const fallback = { lat: -26.2041, lng: 28.0473 };
            const center = window.searchCenter || fallback;
            map = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: window.searchCenter ? 11 : 6,
            });

            if (window.searchCenter) {
                new google.maps.Marker({
                    position: window.searchCenter,
                    map: map,
                    title: 'Your location',
                    icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                });
            }

            (window.branchMarkers || []).forEach(function (b) {
                const marker = new google.maps.Marker({ position: { lat: b.lat, lng: b.lng }, map: map, title: b.name });
                const info = new google.maps.InfoWindow({
                    content: '<strong>' + b.garage + '</strong><br>' + b.name + '<br>' + b.distance + ' km',
                });
                marker.addListener('click', function () { info.open(map, marker); });
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
        }

        function useMyLocation() {
            if (!navigator.geolocation) { alert('Geolocation is not supported by your browser.'); return; }
            navigator.geolocation.getCurrentPosition(function (pos) {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('lng').value = pos.coords.longitude;
                document.getElementById('search-form').submit();
            }, function () { alert('Could not get your location.'); });
        }

        // If the user typed an address but no coordinates were set, geocode in-browser then submit.
        document.getElementById('search-form').addEventListener('submit', function (e) {
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('maps.key') }}&libraries=places&loading=async&callback=initMap" async defer></script>
</body>
</html>
