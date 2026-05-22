@php
    $riskBadge = fn (?string $risk): string => match ($risk) {
        'rendah' => 'bg-emerald-100 text-emerald-800',
        'sedang' => 'bg-amber-100 text-amber-800',
        'tinggi' => 'bg-orange-100 text-orange-800',
        'kritis' => 'bg-rose-100 text-rose-800',
        default => 'bg-slate-100 text-slate-700',
    };

    $hazardMapCounts = [
        'total' => $hazardMarkers->count(),
        'high' => $hazardMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count(),
        'active' => $hazardMarkers->where('status', '!=', 'resolved')->count(),
    ];
    $incidentMapCounts = [
        'total' => $incidentMarkers->count(),
        'high' => $incidentMarkers->whereIn('severity_level', ['high', 'critical'])->count(),
        'active' => $incidentMarkers->whereNotIn('status', ['resolved', 'closed', 'rejected'])->count(),
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

<section id="peta-gis-hazard" class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="rounded-[1.45rem] bg-white px-8 py-8 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">GIS Kampus</p>
                    <h2 class="mt-3 text-4xl font-bold text-[var(--primary-color)] lg:text-5xl">Peta Titik Rawan & Insiden</h2>
                </div>
                <div class="inline-flex rounded-full bg-[var(--blue-low-opacity)] p-1">
                    <button type="button" data-public-gis-mode="hazard" class="inline-flex min-h-10 items-center gap-2 rounded-full bg-white px-4 text-sm font-bold text-[var(--primary-color)] shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">warning</span>
                        Titik Rawan
                    </button>
                    <button type="button" data-public-gis-mode="incident" class="inline-flex min-h-10 items-center gap-2 rounded-full px-4 text-sm font-bold text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">emergency_home</span>
                        Titik Insiden
                    </button>
                </div>
            </div>
            <p class="mt-4 max-w-4xl text-base font-semibold leading-8 text-slate-600">
                Pilih tampilan titik rawan atau titik insiden untuk melihat persebaran lokasi kejadian pada citra satelit area Kampus Polman Bandung.
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Titik</p>
                <p id="public-gis-total-count" class="mt-2 text-3xl font-bold text-[var(--primary-color)]">{{ $hazardMapCounts['total'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p id="public-gis-high-label" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Risiko Tinggi</p>
                <p id="public-gis-high-count" class="mt-2 text-3xl font-bold text-orange-600">{{ $hazardMapCounts['high'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Belum Selesai</p>
                <p id="public-gis-active-count" class="mt-2 text-3xl font-bold text-rose-600">{{ $hazardMapCounts['active'] }}</p>
            </article>
        </div>
    </div>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
        <div class="relative overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <div id="public-gis-position-status" class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-white/95 px-4 py-2 text-xs font-bold text-slate-600 shadow-[0_12px_28px_rgba(15,23,42,0.18)] ring-1 ring-slate-200" style="z-index: 500; max-width: calc(100% - 2rem);">
                <span class="material-symbols-outlined text-[18px] text-[var(--primary-color)]">my_location</span>
                Mengaktifkan tracking GPS...
            </div>
            <div id="public-hazard-map" class="h-[72vh] min-h-[560px] w-full"></div>
        </div>

        <aside class="rounded-[1.45rem] bg-white p-5 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p id="public-gis-list-eyebrow" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Daftar Titik Rawan</p>
                    <h3 id="public-gis-list-title" class="mt-1 text-2xl font-bold text-slate-900">Area Terpetakan</h3>
                </div>
                <span class="material-symbols-outlined text-[var(--primary-color)]">satellite_alt</span>
            </div>

            <div id="public-gis-list" class="mt-5 max-h-[64vh] space-y-3 overflow-y-auto pr-1">
                <div class="rounded-[1.1rem] bg-slate-50 px-4 py-6 text-sm leading-7 text-slate-500">
                    Memuat data GIS...
                </div>
            </div>
        </aside>
    </section>

    <section class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Denah Kampus</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">Titik rawan pada denah gedung</h3>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span id="public-gps-building-status" class="text-sm font-semibold text-slate-500">{{ $floorplanMarkers->count() }} titik</span>
                <button id="public-use-gps-button" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-4 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                    <span class="material-symbols-outlined text-[20px]">my_location</span>
                    Gunakan GPS
                </button>
            </div>
        </div>
        @include('partials.floorplan-building-viewer', ['id' => 'public-floorplan-viewer'])
    </section>
</section>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('public-hazard-map');
            const floorplanViewer = document.getElementById('public-floorplan-viewer');
            const gpsButton = document.getElementById('public-use-gps-button');
            const gpsStatus = document.getElementById('public-gps-building-status');
            const positionStatus = document.getElementById('public-gis-position-status');
            const gisModeButtons = document.querySelectorAll('[data-public-gis-mode]');
            const gisList = document.getElementById('public-gis-list');
            const gisListEyebrow = document.getElementById('public-gis-list-eyebrow');
            const gisListTitle = document.getElementById('public-gis-list-title');
            const gisTotalCount = document.getElementById('public-gis-total-count');
            const gisHighLabel = document.getElementById('public-gis-high-label');
            const gisHighCount = document.getElementById('public-gis-high-count');
            const gisActiveCount = document.getElementById('public-gis-active-count');
            const hazardMarkers = @json($hazardMarkers);
            const incidentMarkers = @json($incidentMarkers);
            const gisSummaries = {
                hazard: @json($hazardMapCounts),
                incident: @json($incidentMapCounts),
            };
            const floorplanMarkers = @json($floorplanMarkers);
            const campusBuildings = @json($campusBuildingPolygons);
            const campusBoundary = [
                [-6.87715887735517, 107.61984870172961],
                [-6.87776142492383, 107.61981835600203],
                [-6.877674808757875, 107.62141909313236],
                [-6.878032571080371, 107.62151392353108],
                [-6.878484174399816, 107.62131657771924],
                [-6.8788463291320125, 107.62168135810514],
                [-6.878484174399816, 107.62201931640384],
                [-6.878036806408215, 107.6221373335875],
                [-6.877024901063348, 107.62203540965614],
                [-6.876455037630919, 107.6218530194632],
                [-6.875885173514988, 107.62182083295856],
                [-6.875879847678909, 107.6215150611645],
                [-6.876401779331286, 107.621203924953],
                [-6.876375150179223, 107.62074794947064],
                [-6.876391127670642, 107.6202436942313],
                [-6.876380476009756, 107.62023296539644],
                [-6.876790564781358, 107.62022760097899],
                [-6.876827845561228, 107.6201042193779],
                [-6.8768225197357085, 107.62013104146509],
                [-6.877152720804798, 107.62017932122203],
                [-6.877158046626614, 107.61985209175822],
                [-6.877152720804798, 107.61984672734077],
            ];

            if (typeof L === 'undefined') {
                return;
            }

            const colors = {
                rendah: '#159947',
                sedang: '#e7aa14',
                tinggi: '#ef6a22',
                kritis: '#d93f33',
                low: '#159947',
                medium: '#e7aa14',
                high: '#ef6a22',
                critical: '#d93f33',
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

            const createUserPositionIcon = () => L.divIcon({
                className: '',
                html: `
                    <span style="display:flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:9999px;background:#0a4db3;border:4px solid #fff;box-shadow:0 12px 28px rgba(10,77,179,.38);">
                        <span style="display:block;width:10px;height:10px;border-radius:9999px;background:#fff;"></span>
                    </span>
                `,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
            });

            const escapeHtml = (value) => String(value ?? '-')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const statusLabel = (status) => String(status ?? '-').replaceAll('_', ' ');

            const isPointInsidePolygon = ([lat, lng], polygon) => {
                let inside = false;

                for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                    const [latI, lngI] = polygon[i];
                    const [latJ, lngJ] = polygon[j];
                    const intersects = ((lngI > lng) !== (lngJ > lng))
                        && (lat < ((latJ - latI) * (lng - lngI)) / (lngJ - lngI) + latI);

                    if (intersects) {
                        inside = !inside;
                    }
                }

                return inside;
            };

            const setPositionStatus = (message, tone = 'waiting') => {
                if (!positionStatus) {
                    return;
                }

                const color = tone === 'inside'
                    ? '#159947'
                    : (tone === 'outside' || tone === 'error' ? '#d93f33' : 'var(--primary-color)');

                positionStatus.innerHTML = `
                    <span class="material-symbols-outlined text-[18px]" style="color:${color};">my_location</span>
                    <span>${escapeHtml(message)}</span>
                `;
            };

            const modeConfig = {
                hazard: {
                    markers: hazardMarkers,
                    eyebrow: 'Daftar Titik Rawan',
                    title: 'Area Terpetakan',
                    highLabel: 'Risiko Tinggi',
                    empty: 'Belum ada titik hazard yang dipetakan Satgas.',
                    badgeKey: 'risk_level',
                    popupDetail: (marker) => `Risiko: ${escapeHtml(marker.risk_level)}<br>Status: ${escapeHtml(statusLabel(marker.status))}`,
                    listMeta: (marker) => `${escapeHtml(marker.location)} - ${escapeHtml(marker.specific_location)}`,
                    listBadge: (marker) => escapeHtml(marker.risk_level),
                    iconKey: (marker) => marker.risk_level,
                },
                incident: {
                    markers: incidentMarkers,
                    eyebrow: 'Daftar Titik Insiden',
                    title: 'Kejadian Terpetakan',
                    highLabel: 'Insiden Berat',
                    empty: 'Belum ada titik insiden yang memiliki koordinat GIS atau data ruangan.',
                    badgeKey: 'severity_level',
                    popupDetail: (marker) => `Kategori: ${escapeHtml(marker.category)}<br>Keparahan: ${escapeHtml(marker.severity_level)}<br>Status: ${escapeHtml(statusLabel(marker.status))}`,
                    listMeta: (marker) => `${escapeHtml(marker.location)} - ${escapeHtml(marker.specific_location)}`,
                    listBadge: (marker) => escapeHtml(marker.severity_level),
                    iconKey: (marker) => marker.severity_level,
                },
            };

            const badgeClass = (mode, marker) => {
                const key = marker[modeConfig[mode].badgeKey];

                if (['kritis', 'critical'].includes(key)) return 'bg-rose-100 text-rose-800';
                if (['tinggi', 'high'].includes(key)) return 'bg-orange-100 text-orange-800';
                if (['sedang', 'medium'].includes(key)) return 'bg-amber-100 text-amber-800';
                if (['rendah', 'low'].includes(key)) return 'bg-emerald-100 text-emerald-800';

                return 'bg-slate-100 text-slate-700';
            };

            const renderGisList = (mode) => {
                if (!gisList) {
                    return;
                }

                const config = modeConfig[mode];

                if (config.markers.length === 0) {
                    gisList.innerHTML = `
                        <div class="rounded-[1.1rem] bg-slate-50 px-4 py-6 text-sm leading-7 text-slate-500">
                            ${config.empty}
                        </div>
                    `;
                    return;
                }

                gisList.innerHTML = config.markers.map((marker) => `
                    <article class="rounded-[1.1rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900">${escapeHtml(marker.title)}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">${config.listMeta(marker)}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-3 py-1 text-[11px] font-bold uppercase ${badgeClass(mode, marker)}">
                                ${config.listBadge(marker)}
                            </span>
                        </div>
                        <p class="mt-3 text-xs font-semibold text-[var(--primary-color)]">${escapeHtml(marker.report_number)}</p>
                    </article>
                `).join('');
            };

            const updateGisSummary = (mode) => {
                const summary = gisSummaries[mode] || { total: 0, high: 0, active: 0 };
                const config = modeConfig[mode];

                if (gisTotalCount) gisTotalCount.textContent = summary.total;
                if (gisHighCount) gisHighCount.textContent = summary.high;
                if (gisActiveCount) gisActiveCount.textContent = summary.active;
                if (gisHighLabel) gisHighLabel.textContent = config.highLabel;
                if (gisListEyebrow) gisListEyebrow.textContent = config.eyebrow;
                if (gisListTitle) gisListTitle.textContent = config.title;
            };

            const updateGisButtons = (mode) => {
                gisModeButtons.forEach((button) => {
                    const isActive = button.dataset.publicGisMode === mode;
                    button.className = isActive
                        ? 'inline-flex min-h-10 items-center gap-2 rounded-full bg-white px-4 text-sm font-bold text-[var(--primary-color)] shadow-sm'
                        : 'inline-flex min-h-10 items-center gap-2 rounded-full px-4 text-sm font-bold text-slate-600';
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
            };

            const addCampusBuildings = (map, bounds) => {
                campusBuildings.forEach((building) => {
                    const polygon = L.polygon(building.coordinates, {
                        color: building.color,
                        fillColor: building.color,
                        fillOpacity: 0.28,
                        opacity: 0.95,
                        weight: 3,
                    }).addTo(map);

                    polygon.bindPopup(`<strong>${building.name}</strong><br>Area gedung kampus`);
                    building.coordinates.forEach((coordinate) => bounds.push(coordinate));
                });
            };

            const addBuildingLegend = (map) => {
                const legend = L.control({ position: 'bottomleft' });

                legend.onAdd = () => {
                    const container = L.DomUtil.create('div', 'campus-building-legend');
                    container.style.background = 'rgba(255,255,255,.94)';
                    container.style.borderRadius = '16px';
                    container.style.boxShadow = '0 14px 34px rgba(15,23,42,.22)';
                    container.style.padding = '12px 14px';
                    container.style.fontFamily = 'Poppins, sans-serif';
                    container.style.fontSize = '12px';
                    container.style.lineHeight = '1.4';
                    container.innerHTML = `
                        <strong style="display:block;margin-bottom:8px;color:#0f172a;">Polygon Gedung</strong>
                        ${campusBuildings.map((building) => `
                            <span style="display:flex;align-items:center;gap:8px;margin-top:6px;color:#334155;">
                                <span style="display:inline-block;width:14px;height:14px;border-radius:4px;background:${building.color};opacity:.85;"></span>
                                ${building.name}
                            </span>
                        `).join('')}
                    `;
                    L.DomEvent.disableClickPropagation(container);
                    return container;
                };

                legend.addTo(map);
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
                const markerLayer = L.layerGroup().addTo(map);
                const userPositionLayer = L.layerGroup().addTo(map);
                let userPositionMarker = null;

                addCampusBuildings(map, bounds);
                addBuildingLegend(map);
                L.polygon(campusBoundary, {
                    color: '#0a4db3',
                    fillColor: '#0a4db3',
                    fillOpacity: 0.08,
                    opacity: 0.75,
                    weight: 2,
                    dashArray: '6 8',
                }).addTo(map);

                const renderMapMarkers = (mode, shouldFit = false) => {
                    const config = modeConfig[mode];
                    const markerBounds = [...bounds];

                    markerLayer.clearLayers();
                    config.markers.forEach((marker) => {
                        L.marker([marker.latitude, marker.longitude], { icon: createIcon(config.iconKey(marker)) })
                            .addTo(markerLayer)
                            .bindPopup(`
                                <strong>${escapeHtml(marker.title)}</strong><br>
                                ${escapeHtml(marker.location)}<br>
                                ${config.popupDetail(marker)}
                            `);

                        markerBounds.push([marker.latitude, marker.longitude]);
                    });

                    if (shouldFit && markerBounds.length > 0) {
                        map.fitBounds(markerBounds, { padding: [48, 48], maxZoom: 19 });
                    }
                };

                const setGisMode = (mode, shouldFit = true) => {
                    renderMapMarkers(mode, shouldFit);
                    renderGisList(mode);
                    updateGisSummary(mode);
                    updateGisButtons(mode);
                };

                gisModeButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        setGisMode(button.dataset.publicGisMode || 'hazard');
                    });
                });

                setGisMode('hazard', true);

                const updateUserPosition = (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = Math.round(position.coords.accuracy || 0);
                    const isInsidePolman = isPointInsidePolygon([lat, lng], campusBoundary);

                    if (!isInsidePolman) {
                        userPositionLayer.clearLayers();
                        userPositionMarker = null;
                        setPositionStatus('Posisi GPS di luar area Polman.', 'outside');
                        return;
                    }

                    const latLng = [lat, lng];

                    if (!userPositionMarker) {
                        userPositionMarker = L.marker(latLng, { icon: createUserPositionIcon(), zIndexOffset: 1000 })
                            .addTo(userPositionLayer)
                            .bindPopup('Posisi Anda di area Polman');
                    } else {
                        userPositionMarker.setLatLng(latLng);
                    }

                    setPositionStatus(`Posisi Anda terdeteksi di area Polman${accuracy ? ` (${accuracy} m)` : ''}.`, 'inside');
                };

                const startPositionTracking = () => {
                    if (!navigator.geolocation) {
                        setPositionStatus('GPS tidak didukung browser ini.', 'error');
                        return;
                    }

                    setPositionStatus('Menunggu izin dan sinyal GPS...', 'waiting');
                    navigator.geolocation.watchPosition(updateUserPosition, () => {
                        userPositionLayer.clearLayers();
                        userPositionMarker = null;
                        setPositionStatus('GPS belum aktif atau izin lokasi ditolak.', 'error');
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 5000,
                        timeout: 15000,
                    });
                };

                startPositionTracking();
            }

            const selectFloor = (viewer, floor) => {
                if (!viewer) {
                    return;
                }

                viewer.dataset.activeFloor = String(floor);
                viewer.querySelectorAll('[data-floor-tab]').forEach((tab) => {
                    const active = tab.dataset.targetFloor === String(floor);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    tab.className = active
                        ? 'inline-flex min-h-11 items-center justify-center rounded-full bg-[var(--primary-color)] px-4 text-sm font-bold text-white shadow-[0_10px_24px_rgba(10,77,179,0.24)] transition'
                        : 'inline-flex min-h-11 items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-200';
                });

                viewer.querySelectorAll('[data-floor-panel]').forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.panelFloor !== String(floor));
                });
            };

            const renderFloorplanMarkers = (viewer, markerData) => {
                const floorplans = viewer?.querySelectorAll('.floorplan-html') ?? [];

                floorplans.forEach((floorplan) => {
                    const layer = floorplan?.querySelector('.floorplan-marker-layer');
                    const width = Number(floorplan?.dataset.floorplanWidth || 4080);
                    const height = Number(floorplan?.dataset.floorplanHeight || 3060);
                    const buildingKey = floorplan?.dataset.buildingKey || 'gedung-teori';
                    const floor = Number(floorplan?.dataset.floor || 2);

                    if (!layer) {
                        return;
                    }

                    layer.innerHTML = '';

                    markerData
                        .filter((marker) => (marker.building_key || 'gedung-teori') === buildingKey && Number(marker.floor || 2) === floor)
                        .forEach((marker) => {
                            const color = colors[marker.risk_level] || '#0a4db3';
                            const pin = document.createElement('button');
                            pin.type = 'button';
                            pin.className = 'pointer-events-auto absolute z-30 h-6 w-6 -translate-x-1/2 -translate-y-1/2 rounded-full border-[3px] border-white shadow-[0_10px_24px_rgba(15,23,42,.35)] transition hover:scale-125 focus:scale-125 focus:outline-none focus:ring-4 focus:ring-sky-200';
                            pin.style.left = `${(Number(marker.x) / width) * 100}%`;
                            pin.style.top = `${(Number(marker.y) / height) * 100}%`;
                            pin.style.background = color;
                            pin.title = `${marker.title} - Risiko: ${marker.risk_level}`;
                            pin.setAttribute('aria-label', pin.title);
                            pin.addEventListener('click', () => {
                                alert(`${marker.title}\n${marker.location}\nRisiko: ${marker.risk_level}\nStatus: ${marker.status}`);
                            });
                            layer.appendChild(pin);
                        });
                });
            };

            const buildingAt = (lat, lng) => campusBuildings.find((building) => isPointInsidePolygon([lat, lng], building.coordinates));

            const useGpsForFloorplan = () => {
                if (!navigator.geolocation) {
                    gpsStatus.textContent = 'GPS tidak didukung browser.';
                    return;
                }

                gpsStatus.textContent = 'Mendeteksi posisi...';
                navigator.geolocation.getCurrentPosition((position) => {
                    const detected = buildingAt(position.coords.latitude, position.coords.longitude);

                    if (!detected) {
                        gpsStatus.textContent = 'Posisi belum masuk polygon gedung.';
                        return;
                    }

                    const floor = detected.default_floor || detected.floors?.[0] || 2;
                    gpsStatus.textContent = `${detected.name} terdeteksi`;

                    if (detected.key === floorplanViewer?.dataset.buildingKey) {
                        floorplanViewer.querySelector('[data-floorplan-building-label]').textContent = detected.name;
                        selectFloor(floorplanViewer, floor);
                    }
                }, () => {
                    gpsStatus.textContent = 'Izin GPS ditolak atau gagal dibaca.';
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 15000,
                    timeout: 10000,
                });
            };

            floorplanViewer?.querySelectorAll('[data-floor-tab]').forEach((tab) => {
                tab.addEventListener('click', () => selectFloor(floorplanViewer, tab.dataset.targetFloor));
            });
            gpsButton?.addEventListener('click', useGpsForFloorplan);
            renderFloorplanMarkers(floorplanViewer, floorplanMarkers);
        })();
    </script>
@endpush
