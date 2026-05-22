@php
    $id = $id ?? 'floorplan-building-viewer';
    $buildingKey = $buildingKey ?? 'gedung-teori';
    $floorplans = collect($floorplans ?? \App\Models\Floorplan::query()
        ->where('building_key', $buildingKey)
        ->where('is_active', true)
        ->whereNotNull('svg_markup')
        ->orderBy('floor')
        ->orderByDesc('version')
        ->get())
        ->unique('floor')
        ->values();
    $hasFloorplans = $floorplans->isNotEmpty();
    $buildingName = $buildingName ?? ($floorplans->first()?->building_name ?? 'Gedung');
    $activeFloor = (int) ($activeFloor ?? ($floorplans->first()?->floor ?? 0));
    $floorplanIds = $floorplans->pluck('id');
    $incidentRoomIds = $hasFloorplans
        ? \App\Models\IncidentReport::query()
            ->whereNotNull('campus_room_id')
            ->whereNotIn('status', ['resolved', 'closed', 'rejected'])
            ->whereIn('campus_room_id', \App\Models\FloorplanRoom::query()
                ->whereIn('floorplan_id', $floorplanIds)
                ->select('campus_room_id'))
            ->pluck('campus_room_id')
            ->unique()
        : collect();
    $incidentRoomColors = $incidentRoomIds->isNotEmpty()
        ? \App\Models\FloorplanRoom::query()
            ->whereIn('floorplan_id', $floorplanIds)
            ->whereIn('campus_room_id', $incidentRoomIds)
            ->get()
            ->mapWithKeys(fn ($room) => [(string) $room->campus_room_id => $room->incident_fill_color])
        : collect();
@endphp

<div
    id="{{ $id }}"
    class="floorplan-building-viewer"
    data-building-key="{{ $buildingKey }}"
    data-active-floor="{{ $activeFloor }}"
    data-has-floorplans="{{ $hasFloorplans ? 'true' : 'false' }}"
    data-incident-room-colors='@json($incidentRoomColors)'
>
    <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Gedung terdeteksi</p>
            <h3 class="mt-1 text-xl font-bold text-slate-900" data-floorplan-building-label>{{ $buildingName }}</h3>
        </div>

        @if ($hasFloorplans)
            <div class="flex max-w-full gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Pilih lantai denah">
                @foreach ($floorplans as $floorplan)
                    @php
                        $floorNumber = (int) $floorplan->floor;
                        $isActive = $floorNumber === $activeFloor;
                    @endphp
                    <button
                        type="button"
                        class="{{ $isActive ? 'bg-[var(--primary-color)] text-white shadow-[0_10px_24px_rgba(10,77,179,0.24)]' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} inline-flex min-h-11 shrink-0 items-center justify-center rounded-full px-4 text-sm font-bold transition"
                        data-floor-tab
                        data-target-floor="{{ $floorNumber }}"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    >
                        Lantai {{ $floorNumber }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    @if (! $hasFloorplans)
        <div class="flex min-h-[360px] items-center justify-center bg-white px-6 py-16 text-center sm:min-h-[460px] lg:min-h-[560px]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400">Denah belum tersedia</p>
                <h4 class="mt-3 text-2xl font-bold text-slate-900">Belum ada data denah untuk ditampilkan.</h4>
                <p class="mt-3 max-w-xl text-sm leading-7 text-slate-600">Silakan buat denah dari menu admin agar tampilan GIS dapat menampilkan area ruangan dan titik pada denah.</p>
            </div>
        </div>
    @else
        @foreach ($floorplans as $floorplan)
            @php
                $floorNumber = (int) $floorplan->floor;
                $isActive = $floorNumber === $activeFloor;
            @endphp
            <div
                class="{{ $isActive ? '' : 'hidden' }}"
                data-floor-panel
                data-panel-floor="{{ $floorNumber }}"
            >
                <div
                    id="{{ $id }}-floor-{{ $floorNumber }}"
                    class="floorplan-html relative max-h-[72vh] min-h-[360px] overflow-auto overscroll-contain bg-white sm:min-h-[460px] lg:min-h-[560px]"
                    data-building-key="{{ $floorplan->building_key }}"
                    data-floor="{{ $floorNumber }}"
                    data-floorplan-width="{{ $floorplan->canvas_width }}"
                    data-floorplan-height="{{ $floorplan->canvas_height }}"
                >
                    <div
                        class="relative m-3 min-w-[760px] bg-white sm:m-4 sm:min-w-[980px] lg:min-w-[1184px]"
                        style="aspect-ratio: {{ max((int) $floorplan->canvas_width, 1) }} / {{ max((int) $floorplan->canvas_height, 1) }};"
                    >
                        {!! $floorplan->svg_markup !!}
                        <div class="floorplan-marker-layer pointer-events-none absolute inset-0"></div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@if ($hasFloorplans)
    @push('scripts')
        <script>
            (() => {
                const viewer = document.getElementById(@json($id));

                if (!viewer) {
                    return;
                }

                const incidentRooms = JSON.parse(viewer.dataset.incidentRoomColors || '{}');

                Object.entries(incidentRooms).forEach(([roomId, color]) => {
                    viewer.querySelectorAll(`[data-room-id="${roomId}"]`).forEach((roomGroup) => {
                        const shape = roomGroup.querySelector('rect, polygon');

                        if (!shape) {
                            return;
                        }

                        shape.setAttribute('fill', color || '#ef4444');
                        shape.setAttribute('stroke', '#991b1b');
                        shape.setAttribute('stroke-width', '2');
                        roomGroup.dataset.incidentActive = 'true';
                    });
                });
            })();
        </script>
    @endpush
@endif
