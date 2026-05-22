@csrf

@php
    $oldRooms = old('rooms');
    $rows = collect($oldRooms ?? $roomMappings->map(function ($mapping) {
        return [
            'campus_room_id' => $mapping->campus_room_id,
            'shape_type' => $mapping->shape_type,
            'coordinates' => json_encode($mapping->geometry),
            'label' => $mapping->label,
            'default_fill_color' => $mapping->default_fill_color,
            'incident_fill_color' => $mapping->incident_fill_color,
            'hazard_fill_color' => $mapping->hazard_fill_color,
            'sort_order' => $mapping->sort_order,
        ];
    })->all());

    if ($rows->isEmpty()) {
        $rows = collect([[
            'campus_room_id' => '',
            'shape_type' => 'polygon',
            'coordinates' => '[[80,80],[260,80],[260,190],[80,190]]',
            'label' => '',
            'default_fill_color' => '#e5e7eb',
            'incident_fill_color' => '#ef4444',
            'hazard_fill_color' => '#f59e0b',
            'sort_order' => 0,
        ]]);
    }
@endphp

<div class="space-y-7">
    @if ($errors->any())
        <div class="rounded-3xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700">
            <p class="font-semibold">Data belum bisa disimpan.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <div>
            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama denah</label>
            <input id="name" name="name" type="text" value="{{ old('name', $floorplan->name) }}" placeholder="Contoh: Denah Gedung Teori Lantai 2" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        </div>

        <div>
            <label for="location_id" class="mb-2 block text-sm font-semibold text-slate-700">Lokasi master</label>
            <select id="location_id" name="location_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                <option value="">Tanpa lokasi</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected((string) old('location_id', $floorplan->location_id) === (string) $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="building_key" class="mb-2 block text-sm font-semibold text-slate-700">Gedung</label>
            <select id="building_key" name="building_key" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                @foreach ($buildings as $building)
                    <option value="{{ $building['key'] }}" @selected(old('building_key', $floorplan->building_key) === $building['key'])>{{ $building['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="floor" class="mb-2 block text-sm font-semibold text-slate-700">Lantai</label>
            <input id="floor" name="floor" type="number" min="1" max="99" value="{{ old('floor', $floorplan->floor) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        </div>

        <div>
            <label for="version" class="mb-2 block text-sm font-semibold text-slate-700">Versi</label>
            <input id="version" name="version" type="number" min="1" value="{{ old('version', $floorplan->version) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        </div>

        <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-[var(--primary-color)]" @checked(old('is_active', $floorplan->is_active ?? true))>
            <span class="text-sm font-semibold text-slate-700">Denah aktif</span>
        </label>

        <div>
            <label for="canvas_width" class="mb-2 block text-sm font-semibold text-slate-700">Lebar canvas</label>
            <input id="canvas_width" name="canvas_width" type="number" min="100" value="{{ old('canvas_width', $floorplan->canvas_width) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        </div>

        <div>
            <label for="canvas_height" class="mb-2 block text-sm font-semibold text-slate-700">Tinggi canvas</label>
            <input id="canvas_height" name="canvas_height" type="number" min="100" value="{{ old('canvas_height', $floorplan->canvas_height) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_430px]">
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Area Denah</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Koordinat Ruangan</h3>
                </div>
                <button type="button" id="add-room-row" class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Tambah Area</button>
            </div>

            <div id="room-rows" class="space-y-4">
                @foreach ($rows as $index => $row)
                    <div class="room-row rounded-3xl border border-slate-200 bg-slate-50 p-5" data-row-index="{{ $index }}">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="font-semibold text-slate-900">Area #{{ $index + 1 }}</p>
                            <button type="button" class="remove-room-row text-sm font-semibold text-rose-700">Hapus</button>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Ruangan admin</label>
                                <select name="rooms[{{ $index }}][campus_room_id]" class="room-field room-select w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                                    <option value="">Pilih ruangan</option>
                                    @foreach ($campusRooms as $room)
                                        <option value="{{ $room->id }}" data-building="{{ $room->building_key }}" data-floor="{{ $room->floor }}" @selected((string) ($row['campus_room_id'] ?? '') === (string) $room->id)>
                                            {{ $room->building_name }} · Lt {{ $room->floor }} · {{ $room->name }}{{ $room->code ? ' / '.$room->code : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Label tampil</label>
                                <input name="rooms[{{ $index }}][label]" type="text" value="{{ $row['label'] ?? '' }}" class="room-field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe shape</label>
                                <select name="rooms[{{ $index }}][shape_type]" class="room-field shape-type w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                                    <option value="polygon" @selected(($row['shape_type'] ?? 'polygon') === 'polygon')>Polygon</option>
                                    <option value="rect" @selected(($row['shape_type'] ?? 'polygon') === 'rect')>Rectangle</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Urutan</label>
                                <input name="rooms[{{ $index }}][sort_order]" type="number" min="0" value="{{ $row['sort_order'] ?? $index }}" class="room-field w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
                            </div>

                            <div class="lg:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Koordinat JSON</label>
                                <textarea name="rooms[{{ $index }}][coordinates]" rows="4" class="room-field coordinates w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-mono text-sm outline-none focus:border-[var(--primary-color)]">{{ $row['coordinates'] ?? '' }}</textarea>
                                <p class="mt-2 text-xs text-slate-500">Polygon: [[x,y],[x,y],[x,y]]. Rectangle: {"x":80,"y":80,"width":180,"height":110}.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Warna normal</label>
                                <input name="rooms[{{ $index }}][default_fill_color]" type="color" value="{{ $row['default_fill_color'] ?? '#e5e7eb' }}" class="room-field h-12 w-full rounded-2xl border border-slate-200 bg-white px-3">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Warna insiden</label>
                                <input name="rooms[{{ $index }}][incident_fill_color]" type="color" value="{{ $row['incident_fill_color'] ?? '#ef4444' }}" class="room-field h-12 w-full rounded-2xl border border-slate-200 bg-white px-3">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">Warna hazard</label>
                                <input name="rooms[{{ $index }}][hazard_fill_color]" type="color" value="{{ $row['hazard_fill_color'] ?? '#f59e0b' }}" class="room-field h-12 w-full rounded-2xl border border-slate-200 bg-white px-3">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <aside class="h-fit rounded-[2rem] bg-slate-950 p-5 text-white">
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-200">Preview HTML</p>
                <h3 class="mt-2 text-xl font-semibold">Generate Denah</h3>
            </div>
            <div class="overflow-hidden rounded-3xl bg-white p-3">
                <svg id="floorplan-preview" class="h-auto w-full" viewBox="0 0 {{ old('canvas_width', $floorplan->canvas_width) }} {{ old('canvas_height', $floorplan->canvas_height) }}"></svg>
            </div>
            <p class="mt-4 text-xs leading-6 text-slate-300">Preview ini memakai koordinat yang sama dengan yang akan disimpan ke database. Warna insiden dipakai nanti oleh tampilan laporan saat `campus_room_id` cocok.</p>
        </aside>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="rounded-full bg-[var(--primary-color)] px-6 py-3 text-sm font-semibold text-white">{{ $submitLabel }}</button>
        <a href="{{ route('admin.floorplans.index') }}" class="rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const rows = document.getElementById('room-rows');
            const addButton = document.getElementById('add-room-row');
            const preview = document.getElementById('floorplan-preview');
            const widthInput = document.getElementById('canvas_width');
            const heightInput = document.getElementById('canvas_height');
            const buildingInput = document.getElementById('building_key');
            const floorInput = document.getElementById('floor');

            const renumberRows = () => {
                rows.querySelectorAll('.room-row').forEach((row, index) => {
                    row.dataset.rowIndex = index;
                    row.querySelector('p.font-semibold').textContent = `Area #${index + 1}`;
                    row.querySelectorAll('[name^="rooms["]').forEach((field) => {
                        field.name = field.name.replace(/rooms\[\d+\]/, `rooms[${index}]`);
                    });
                });
            };

            const filterRooms = () => {
                const building = buildingInput.value;
                const floor = floorInput.value;

                rows.querySelectorAll('.room-select option').forEach((option) => {
                    if (!option.value) return;
                    const isMatch = option.dataset.building === building && option.dataset.floor === floor;
                    option.hidden = !isMatch;
                });
            };

            const parseCoordinates = (value) => {
                try {
                    return JSON.parse(value);
                } catch (error) {
                    return null;
                }
            };

            const centerOf = (points) => {
                const total = points.reduce((carry, point) => [carry[0] + Number(point[0] ?? point.x), carry[1] + Number(point[1] ?? point.y)], [0, 0]);
                return [total[0] / points.length, total[1] / points.length];
            };

            const drawPreview = () => {
                const width = Number(widthInput.value || 900);
                const height = Number(heightInput.value || 520);
                preview.setAttribute('viewBox', `0 0 ${width} ${height}`);
                preview.innerHTML = `<rect width="${width}" height="${height}" fill="#f8fafc"></rect>`;

                rows.querySelectorAll('.room-row').forEach((row) => {
                    const shapeType = row.querySelector('.shape-type').value;
                    const coordinates = parseCoordinates(row.querySelector('.coordinates').value);
                    const fill = row.querySelector('input[name$="[default_fill_color]"]').value || '#e5e7eb';
                    const labelField = row.querySelector('input[name$="[label]"]');
                    const selected = row.querySelector('.room-select option:checked');
                    const label = labelField.value || selected?.textContent?.split('·').pop()?.trim() || 'Ruangan';

                    if (!coordinates) return;

                    const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    let labelX = 0;
                    let labelY = 0;

                    if (shapeType === 'rect') {
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('x', Number(coordinates.x || 0));
                        rect.setAttribute('y', Number(coordinates.y || 0));
                        rect.setAttribute('width', Number(coordinates.width || 0));
                        rect.setAttribute('height', Number(coordinates.height || 0));
                        rect.setAttribute('rx', '6');
                        rect.setAttribute('fill', fill);
                        rect.setAttribute('stroke', '#334155');
                        rect.setAttribute('stroke-width', '2');
                        group.appendChild(rect);
                        labelX = Number(coordinates.x || 0) + Number(coordinates.width || 0) / 2;
                        labelY = Number(coordinates.y || 0) + Number(coordinates.height || 0) / 2;
                    } else if (Array.isArray(coordinates) && coordinates.length >= 3) {
                        const points = coordinates.map((point) => `${Number(point[0] ?? point.x)},${Number(point[1] ?? point.y)}`).join(' ');
                        const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                        polygon.setAttribute('points', points);
                        polygon.setAttribute('fill', fill);
                        polygon.setAttribute('stroke', '#334155');
                        polygon.setAttribute('stroke-width', '2');
                        group.appendChild(polygon);
                        [labelX, labelY] = centerOf(coordinates);
                    }

                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', labelX);
                    text.setAttribute('y', labelY);
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('dominant-baseline', 'middle');
                    text.setAttribute('font-size', '14');
                    text.setAttribute('font-weight', '600');
                    text.setAttribute('fill', '#0f172a');
                    text.textContent = label;
                    group.appendChild(text);
                    preview.appendChild(group);
                });
            };

            addButton.addEventListener('click', () => {
                const source = rows.querySelector('.room-row');
                const clone = source.cloneNode(true);
                clone.querySelectorAll('input, textarea, select').forEach((field) => {
                    if (field.type === 'color') return;
                    field.value = field.classList.contains('shape-type') ? 'polygon' : '';
                });
                clone.querySelector('.coordinates').value = '[[80,80],[260,80],[260,190],[80,190]]';
                rows.appendChild(clone);
                renumberRows();
                filterRooms();
                drawPreview();
            });

            rows.addEventListener('click', (event) => {
                if (!event.target.classList.contains('remove-room-row')) return;
                if (rows.querySelectorAll('.room-row').length === 1) return;
                event.target.closest('.room-row').remove();
                renumberRows();
                drawPreview();
            });

            rows.addEventListener('input', drawPreview);
            rows.addEventListener('change', drawPreview);
            buildingInput.addEventListener('change', filterRooms);
            floorInput.addEventListener('input', filterRooms);
            widthInput.addEventListener('input', drawPreview);
            heightInput.addEventListener('input', drawPreview);

            filterRooms();
            drawPreview();
        })();
    </script>
@endpush
