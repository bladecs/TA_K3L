@extends('admin.layouts.app')

@section('title', 'Master Denah Gedung')
@section('hero_title', 'Master Denah Gedung')
@section('hero_description', 'Buat denah HTML dari koordinat ruangan agar data insiden, hazard, dan ruangan admin memakai referensi yang sama.')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin Denah</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Denah per Gedung dan Lantai</h2>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">Setiap area denah terhubung ke data ruangan. Saat laporan insiden memilih ruangan yang sama, area ini bisa diberi warna status.</p>
            </div>
            <a href="{{ route('admin.floorplans.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white">Buat Denah</a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Denah</th>
                            <th class="px-6 py-4 font-semibold">Gedung</th>
                            <th class="px-6 py-4 font-semibold">Lantai</th>
                            <th class="px-6 py-4 font-semibold">Area Ruangan</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($floorplans as $floorplan)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $floorplan->name }}</p>
                                    <p class="text-xs text-slate-500">Versi {{ $floorplan->version }} · {{ $floorplan->canvas_width }}x{{ $floorplan->canvas_height }}</p>
                                </td>
                                <td class="px-6 py-4 text-slate-700">{{ $floorplan->building_name }}</td>
                                <td class="px-6 py-4 text-slate-700">Lantai {{ $floorplan->floor }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $floorplan->rooms_count }} area</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $floorplan->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $floorplan->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a href="{{ route('admin.floorplans.show', $floorplan) }}" class="font-semibold text-[var(--primary-color)]">Lihat</a>
                                        <a href="{{ route('admin.floorplans.edit', $floorplan) }}" class="font-semibold text-[var(--primary-color)]">Edit</a>
                                        <form action="{{ route('admin.floorplans.destroy', $floorplan) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada data denah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">{{ $floorplans->links() }}</div>
        </div>
    </section>
@endsection
