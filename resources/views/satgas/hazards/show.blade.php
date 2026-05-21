@extends('satgas.layouts.app')

@section('title', 'Detail Hazard Report')
@section('hero_eyebrow', 'Detail Hazard')
@section('hero_title', 'Ruang review dan penanganan hazard')
@section('hero_description', 'Periksa detail temuan, ubah status penanganan, dan dokumentasikan respons satgas pada hazard report yang masuk.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
    @php
        $statusBadge = match ($hazardReport->status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                @include('partials.flash')
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Review Hazard</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $hazardReport->title }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Laporan dari <span class="font-semibold text-slate-900">{{ $hazardReport->reporter?->name ?? $hazardReport->reporter_name ?? '-' }}</span>
                    dengan nomor <span class="font-semibold text-slate-900">{{ $hazardReport->report_number }}</span>.
                </p>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Temuan</h3>
                <div class="mt-6 grid gap-5 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis Temuan</p>
                        <p class="mt-2 text-slate-800">{{ str_replace('-', ' ', $hazardReport->hazard_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</p>
                        <p class="mt-2 text-slate-800">{{ $hazardReport->location?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Titik Detail</p>
                        <p class="mt-2 text-slate-800">{{ $hazardReport->specific_location ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lantai / Ruangan</p>
                        <p class="mt-2 text-slate-800">
                            {{ $hazardReport->building_floor ? 'Lantai '.$hazardReport->building_floor : '-' }}
                            @if ($hazardReport->campusRoom)
                                - {{ $hazardReport->campusRoom->name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge }}">
                                {{ str_replace('_', ' ', $hazardReport->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-8 space-y-5 text-sm leading-7 text-slate-700">
                    <div>
                        <p class="font-semibold text-slate-900">Informasi Tambahan</p>
                        <p class="mt-2">{{ $hazardReport->notes ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Respons Satgas Terakhir</p>
                        <p class="mt-2">{{ $hazardReport->response_note ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Pinpoint GIS Hazard</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">
                            Klik area pada peta satelit untuk menentukan titik rawan yang akan terlihat oleh pengguna umum.
                        </p>
                    </div>
                    <a href="{{ route('satgas.hazards.map') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                        <span class="material-symbols-outlined text-[20px]">map</span>
                        Peta GIS
                    </a>
                </div>

                <form action="{{ route('satgas.hazards.update-pinpoint', $hazardReport) }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <input id="hazard-map-source" name="map_source" type="hidden" value="{{ old('map_source', $hazardReport->map_source ?? 'satellite') }}">

                    <div class="grid gap-3 rounded-[1.25rem] bg-slate-50 p-3 sm:grid-cols-2">
                        <button id="satellite-pick-button" type="button"
                            class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-4 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                            <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                            <span id="satellite-pick-label">Pilih Titik Satelit</span>
                        </button>
                        <button id="floorplan-pick-button" type="button"
                            class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-4 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                            <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                            <span id="floorplan-pick-label">Pilih Titik Denah</span>
                        </button>
                    </div>

                    <div class="grid gap-4 xl:grid-cols-2">
                        <div class="overflow-hidden rounded-[1.5rem] ring-1 ring-slate-200">
                            <div class="flex flex-wrap items-center justify-between gap-3 bg-white px-4 py-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Map Satelit</p>
                                    <p id="satellite-mode-help" class="text-xs text-slate-500">Aktifkan mode titik, lalu klik lokasi hazard.</p>
                                </div>
                                <span id="satellite-selected-badge" class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-slate-600">GPS</span>
                            </div>
                            <div id="hazard-pinpoint-map" class="h-[420px] w-full"></div>
                        </div>

                        <div class="overflow-hidden rounded-[1.5rem] ring-1 ring-slate-200">
                            <div class="flex flex-wrap items-center justify-between gap-3 bg-white px-4 py-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Denah Kampus</p>
                                    <p id="floorplan-mode-help" class="text-xs text-slate-500">Aktifkan mode titik, lalu klik area pada denah.</p>
                                </div>
                                <span id="floorplan-selected-badge" class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-slate-600">Denah</span>
                            </div>
                            @include('partials.floorplan-gedung-teori-lt2', ['id' => 'hazard-floorplan-map'])
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-800">Latitude</span>
                            <input id="hazard-latitude" name="latitude" type="text" value="{{ old('latitude', $hazardReport->latitude) }}"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                            @error('latitude')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-800">Longitude</span>
                            <input id="hazard-longitude" name="longitude" type="text" value="{{ old('longitude', $hazardReport->longitude) }}"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                            @error('longitude')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-800">Posisi X Denah</span>
                            <input id="hazard-floorplan-x" name="floorplan_x" type="text" value="{{ old('floorplan_x', $hazardReport->floorplan_x) }}"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                            @error('floorplan_x')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-800">Posisi Y Denah</span>
                            <input id="hazard-floorplan-y" name="floorplan_y" type="text" value="{{ old('floorplan_y', $hazardReport->floorplan_y) }}"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                            @error('floorplan_y')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-800">Level risiko</span>
                            <select name="risk_level"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'kritis' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('risk_level', $hazardReport->risk_level ?? 'sedang') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('risk_level')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                        Terakhir dipetakan oleh {{ $hazardReport->mapper?->name ?? '-' }} pada {{ optional($hazardReport->mapped_at)->format('d M Y H:i') ?? '-' }}.
                    </div>

                    <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--primary-deep)]">
                        Simpan Pinpoint
                    </button>
                </form>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Update Status Hazard</h3>
                @if (! empty($statusOptions))
                    <form action="{{ route('satgas.hazards.update-status', $hazardReport) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="status" class="mb-2 block text-sm font-semibold text-slate-800">Status berikutnya</label>
                            <select id="status" name="status"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="response_note" class="mb-2 block text-sm font-semibold text-slate-800">Catatan respons</label>
                            <textarea id="response_note" name="response_note" rows="5"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                placeholder="Jelaskan respons atau tindakan penanganan hazard.">{{ old('response_note') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--primary-deep)]">
                            Simpan Status
                        </button>
                    </form>
                @else
                    <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                        Hazard report ini sudah berada di status akhir dan tidak memerlukan perubahan status lanjutan.
                    </div>
                @endif
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Metadata Penanganan</h3>
                <div class="mt-6 space-y-4 text-sm">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Dikirim</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->submitted_at)->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Ditinjau</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->reviewed_at)->format('d M Y H:i') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->reviewer?->name ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-900">Diselesaikan</p>
                        <p class="mt-1 text-slate-600">{{ optional($hazardReport->resolved_at)->format('d M Y H:i') ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->resolver?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Lampiran</h3>
                <div class="mt-6 space-y-3">
                    @forelse ($hazardReport->attachments as $attachment)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            <span class="font-medium">{{ $attachment->file_name }}</span>
                            <span class="material-symbols-outlined text-[var(--primary-color)]">attach_file</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Tidak ada lampiran pada hazard report ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('hazard-pinpoint-map');
            const floorplanElement = document.getElementById('hazard-floorplan-map');
            const mapSourceInput = document.getElementById('hazard-map-source');
            const latitudeInput = document.getElementById('hazard-latitude');
            const longitudeInput = document.getElementById('hazard-longitude');
            const floorplanXInput = document.getElementById('hazard-floorplan-x');
            const floorplanYInput = document.getElementById('hazard-floorplan-y');
            const satelliteBadge = document.getElementById('satellite-selected-badge');
            const floorplanBadge = document.getElementById('floorplan-selected-badge');
            const satellitePickButton = document.getElementById('satellite-pick-button');
            const floorplanPickButton = document.getElementById('floorplan-pick-button');
            const satellitePickLabel = document.getElementById('satellite-pick-label');
            const floorplanPickLabel = document.getElementById('floorplan-pick-label');
            const satelliteModeHelp = document.getElementById('satellite-mode-help');
            const floorplanModeHelp = document.getElementById('floorplan-mode-help');

            if (!mapElement || !floorplanElement || !mapSourceInput || !latitudeInput || !longitudeInput || !floorplanXInput || !floorplanYInput || !satellitePickButton || !floorplanPickButton || !satellitePickLabel || !floorplanPickLabel || typeof L === 'undefined') {
                return;
            }

            let activePicker = null;

            const pointIcon = L.divIcon({
                className: '',
                html: '<span style="display:flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:9999px;background:#d93f33;border:4px solid white;box-shadow:0 12px 26px rgba(15,23,42,.38);color:white;font-size:18px;font-weight:800;">!</span>',
                iconSize: [34, 34],
                iconAnchor: [17, 17],
            });

            const floorplanWidth = Number(floorplanElement.dataset.floorplanWidth || 4080);
            const floorplanHeight = Number(floorplanElement.dataset.floorplanHeight || 3060);
            const floorplanLayer = floorplanElement.querySelector('.floorplan-marker-layer');
            const floorplanPin = document.createElement('button');

            floorplanPin.type = 'button';
            floorplanPin.className = 'pointer-events-auto absolute z-30 flex h-9 w-9 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-4 border-white bg-rose-600 text-lg font-black text-white shadow-[0_12px_26px_rgba(15,23,42,.38)] transition hover:scale-110 focus:outline-none focus:ring-4 focus:ring-rose-200';
            floorplanPin.textContent = '!';
            floorplanPin.title = 'Titik hazard pada denah';
            floorplanLayer.appendChild(floorplanPin);

            const positionFloorplanPin = (x, y) => {
                floorplanPin.style.left = `${(Number(x) / floorplanWidth) * 100}%`;
                floorplanPin.style.top = `${(Number(y) / floorplanHeight) * 100}%`;
            };

            const floorplanPointFromEvent = (event) => {
                const canvas = floorplanElement.firstElementChild;
                const rect = canvas.getBoundingClientRect();

                return {
                    x: Math.max(0, Math.min(floorplanWidth, ((event.clientX - rect.left) / rect.width) * floorplanWidth)),
                    y: Math.max(0, Math.min(floorplanHeight, ((event.clientY - rect.top) / rect.height) * floorplanHeight)),
                };
            };

            const setSource = (source) => {
                mapSourceInput.value = source;
                satelliteBadge.className = source === 'satellite'
                    ? 'rounded-full bg-[var(--primary-color)] px-3 py-1 text-[11px] font-bold uppercase text-white'
                    : 'rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-slate-600';
                floorplanBadge.className = source === 'floorplan'
                    ? 'rounded-full bg-[var(--primary-color)] px-3 py-1 text-[11px] font-bold uppercase text-white'
                    : 'rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-slate-600';
            };

            const setPickingMode = (source) => {
                activePicker = activePicker === source ? null : source;

                const satelliteActive = activePicker === 'satellite';
                const floorplanActive = activePicker === 'floorplan';

                satellitePickButton.className = satelliteActive
                    ? 'inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-rose-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-rose-700'
                    : 'inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-4 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]';
                floorplanPickButton.className = floorplanActive
                    ? 'inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-rose-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-rose-700'
                    : 'inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-4 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]';

                satellitePickLabel.textContent = satelliteActive ? 'Klik Titik di Satelit' : 'Pilih Titik Satelit';
                floorplanPickLabel.textContent = floorplanActive ? 'Klik Titik di Denah' : 'Pilih Titik Denah';

                satelliteModeHelp.textContent = satelliteActive
                    ? 'Klik sekali pada map untuk menaruh titik. Drag marker jika perlu koreksi.'
                    : 'Aktifkan mode titik, lalu klik lokasi hazard.';
                floorplanModeHelp.textContent = floorplanActive
                    ? 'Klik sekali pada denah untuk menaruh titik.'
                    : 'Aktifkan mode titik, lalu klik area pada denah.';

                [map].filter(Boolean).forEach((leafletMap) => {
                    if (activePicker) {
                        leafletMap.dragging.disable();
                        leafletMap.touchZoom.disable();
                        leafletMap.doubleClickZoom.disable();
                        leafletMap.scrollWheelZoom.disable();
                        leafletMap.boxZoom.disable();
                        leafletMap.keyboard.disable();
                        leafletMap.getContainer().style.cursor = 'crosshair';
                    } else {
                        leafletMap.dragging.enable();
                        leafletMap.touchZoom.enable();
                        leafletMap.doubleClickZoom.enable();
                        leafletMap.scrollWheelZoom.enable();
                        leafletMap.boxZoom.enable();
                        leafletMap.keyboard.enable();
                        leafletMap.getContainer().style.cursor = '';
                    }
                });

                floorplanElement.classList.toggle('cursor-crosshair', floorplanActive);
            };

            const campusCenter = [-6.8761, 107.62063];
            const savedPoint = [
                Number(latitudeInput.value || campusCenter[0]),
                Number(longitudeInput.value || campusCenter[1]),
            ];
            const map = L.map(mapElement).setView(savedPoint, latitudeInput.value && longitudeInput.value ? 19 : 18);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 20,
            }).addTo(map);

            L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Labels &copy; Esri',
                maxZoom: 20,
            }).addTo(map);

            const marker = L.marker(savedPoint, { draggable: true, icon: pointIcon }).addTo(map);

            const syncInputs = (latlng) => {
                setSource('satellite');
                latitudeInput.value = latlng.lat.toFixed(7);
                longitudeInput.value = latlng.lng.toFixed(7);
            };

            marker.on('dragend', () => syncInputs(marker.getLatLng()));
            map.on('click', (event) => {
                if (activePicker !== 'satellite') {
                    return;
                }

                marker.setLatLng(event.latlng);
                syncInputs(event.latlng);
                setPickingMode('satellite');
            });

            const syncFloorplanInputs = (point) => {
                setSource('floorplan');
                floorplanXInput.value = point.x.toFixed(3);
                floorplanYInput.value = point.y.toFixed(3);
                positionFloorplanPin(point.x, point.y);
            };

            positionFloorplanPin(
                Number(floorplanXInput.value || floorplanWidth / 2),
                Number(floorplanYInput.value || floorplanHeight / 2),
            );

            floorplanElement.addEventListener('click', (event) => {
                if (activePicker !== 'floorplan') {
                    return;
                }

                syncFloorplanInputs(floorplanPointFromEvent(event));
                setPickingMode('floorplan');
            });

            satellitePickButton.addEventListener('click', () => setPickingMode('satellite'));
            floorplanPickButton.addEventListener('click', () => setPickingMode('floorplan'));
            setSource(mapSourceInput.value || 'satellite');
        })();
    </script>
@endpush
