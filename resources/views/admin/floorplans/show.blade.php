@extends('admin.layouts.app')

@section('title', 'Preview Denah Gedung')
@section('hero_title', $floorplan->name)
@section('hero_description', $floorplan->building_name . ' · Lantai ' . $floorplan->floor . ' · Versi ' . $floorplan->version)

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                {!! $floorplan->svg_markup ?: '<div class="p-10 text-center text-slate-500">Denah belum digenerate.</div>' !!}
            </div>
        </div>

        <aside class="space-y-4 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Mapping Ruangan</p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $floorplan->rooms->count() }} area tersimpan</h2>
            </div>

            <div class="space-y-3">
                @foreach ($floorplan->rooms as $room)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="font-semibold text-slate-900">{{ $room->label }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $room->campusRoom?->building_name }} · {{ $room->campusRoom?->name }}</p>
                        <div class="mt-3 flex gap-2 text-xs font-semibold">
                            <span class="rounded-full px-3 py-1 text-white" style="background: {{ $room->default_fill_color }}">Normal</span>
                            <span class="rounded-full px-3 py-1 text-white" style="background: {{ $room->incident_fill_color }}">Insiden</span>
                            <span class="rounded-full px-3 py-1 text-white" style="background: {{ $room->hazard_fill_color }}">Hazard</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.floorplans.edit', $floorplan) }}" class="rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white">Edit</a>
                <a href="{{ route('admin.floorplans.index') }}" class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Kembali</a>
            </div>
        </aside>
    </section>
@endsection
