@extends('user.layouts.app')

@section('title', 'Peta GIS Hazard')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('page')
    @php
        $riskBadge = fn (?string $risk): string => match ($risk) {
            'rendah' => 'bg-emerald-100 text-emerald-800',
            'sedang' => 'bg-amber-100 text-amber-800',
            'tinggi' => 'bg-orange-100 text-orange-800',
            'kritis' => 'bg-rose-100 text-rose-800',
            default => 'bg-slate-100 text-slate-700',
        };
    @endphp

    <main class="w-full bg-white pb-14 pt-30">
        <section class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 lg:px-8">
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
                <div class="rounded-[1.45rem] bg-white px-8 py-8 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">GIS Hazard</p>
                    <h1 class="mt-3 text-4xl font-bold text-[var(--primary-color)] lg:text-5xl">Peta Titik Rawan Kampus</h1>
                    <p class="mt-4 max-w-4xl text-base font-semibold leading-8 text-slate-600">
                        Titik pada peta ditentukan oleh Satgas berdasarkan review hazard report dan citra satelit area Kampus Polman Bandung.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Titik</p>
                        <p class="mt-2 text-3xl font-bold text-[var(--primary-color)]">{{ $summaryCounts['total'] }}</p>
                    </article>
                    <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Risiko Tinggi</p>
                        <p class="mt-2 text-3xl font-bold text-orange-600">{{ $summaryCounts['tinggi'] }}</p>
                    </article>
                    <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Belum Selesai</p>
                        <p class="mt-2 text-3xl font-bold text-rose-600">{{ $summaryCounts['aktif'] }}</p>
                    </article>
                </div>
            </div>

            <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                <div class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <div id="public-hazard-map" class="h-[72vh] min-h-[560px] w-full"></div>
                </div>

                <aside class="rounded-[1.45rem] bg-white p-5 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Daftar Titik</p>
                            <h2 class="mt-1 text-2xl font-bold text-slate-900">Area Terpetakan</h2>
                        </div>
                        <span class="material-symbols-outlined text-[var(--primary-color)]">satellite_alt</span>
                    </div>

                    <div class="mt-5 max-h-[64vh] space-y-3 overflow-y-auto pr-1">
                        @forelse ($hazardMarkers as $marker)
                            <article class="rounded-[1.1rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $marker['title'] }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ $marker['location'] }} - {{ $marker['specific_location'] }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-3 py-1 text-[11px] font-bold uppercase {{ $riskBadge($marker['risk_level']) }}">
                                        {{ $marker['risk_level'] }}
                                    </span>
                                </div>
                                <p class="mt-3 text-xs font-semibold text-[var(--primary-color)]">{{ $marker['report_number'] }}</p>
                            </article>
                        @empty
                            <div class="rounded-[1.1rem] bg-slate-50 px-4 py-6 text-sm leading-7 text-slate-500">
                                Belum ada titik hazard yang dipetakan Satgas.
                            </div>
                        @endforelse
                    </div>
                </aside>
            </section>

            <section class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Denah Kampus</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Titik rawan pada foto denah</h2>
                    </div>
                    <span class="text-sm font-semibold text-slate-500">{{ $floorplanMarkers->count() }} titik</span>
                </div>
                <div id="public-floorplan-map" class="h-[72vh] min-h-[560px] w-full bg-slate-100"></div>
            </section>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('public-hazard-map');
            const floorplanElement = document.getElementById('public-floorplan-map');
            const markers = @json($hazardMarkers);
            const floorplanMarkers = @json($floorplanMarkers);

            if (typeof L === 'undefined') {
                return;
            }

            const colors = {
                rendah: '#159947',
                sedang: '#e7aa14',
                tinggi: '#ef6a22',
                kritis: '#d93f33',
            };

            const createIcon = (riskLevel, size = 22) => {
                const color = colors[riskLevel] || '#0a4db3';
                return L.divIcon({
                    className: '',
                    html: `<span style="display:block;width:${size}px;height:${size}px;border-radius:9999px;background:${color};border:3px solid white;box-shadow:0 10px 24px rgba(15,23,42,.35);"></span>`,
                    iconSize: [size, size],
                    iconAnchor: [size / 2, size / 2],
                });
            };

            if (mapElement) {
                const campusCenter = [-6.8761, 107.62063];
                const map = L.map(mapElement, { zoomControl: true }).setView(campusCenter, 18);

                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri',
                    maxZoom: 20,
                }).addTo(map);

                L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Labels &copy; Esri',
                    maxZoom: 20,
                }).addTo(map);

                const bounds = [];

                markers.forEach((marker) => {
                    L.marker([marker.latitude, marker.longitude], { icon: createIcon(marker.risk_level) })
                        .addTo(map)
                        .bindPopup(`
                            <strong>${marker.title}</strong><br>
                            ${marker.location}<br>
                            Risiko: ${marker.risk_level}<br>
                            Status: ${marker.status}
                        `);

                    bounds.push([marker.latitude, marker.longitude]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [48, 48], maxZoom: 19 });
                }
            }

            if (floorplanElement) {
                const width = 4080;
                const height = 3060;
                const bounds = [[0, 0], [height, width]];
                const floorplanMap = L.map(floorplanElement, {
                    crs: L.CRS.Simple,
                    minZoom: -2,
                    maxZoom: 2,
                    zoomSnap: 0.25,
                });

                L.imageOverlay('{{ asset('img/campus-denah/20260430_144208.jpg') }}', bounds).addTo(floorplanMap);
                floorplanMap.fitBounds(bounds);

                floorplanMarkers.forEach((marker) => {
                    L.marker([marker.y, marker.x], { icon: createIcon(marker.risk_level, 24) })
                        .addTo(floorplanMap)
                        .bindPopup(`
                            <strong>${marker.title}</strong><br>
                            ${marker.location}<br>
                            Risiko: ${marker.risk_level}<br>
                            Status: ${marker.status}
                        `);
                });
            }
        })();
    </script>
@endpush
