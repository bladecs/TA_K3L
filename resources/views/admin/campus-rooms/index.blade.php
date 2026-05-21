@extends('admin.layouts.app')

@section('title', 'Master Ruangan Gedung')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Admin GIS</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Ruangan per Gedung</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Data ini dipakai form insiden dan hazard setelah GPS mendeteksi polygon gedung.</p>
            </div>
            <a href="{{ route('admin.campus-rooms.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white">Tambah Ruangan</a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Gedung</th>
                            <th class="px-6 py-4 font-semibold">Lantai</th>
                            <th class="px-6 py-4 font-semibold">Ruangan</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $room->building_name }}</td>
                                <td class="px-6 py-4 text-slate-700">Lantai {{ $room->floor }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $room->name }}{{ $room->code ? ' / '.$room->code : '' }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $room->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $room->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.campus-rooms.edit', $room) }}" class="font-semibold text-[var(--primary-color)]">Edit</a>
                                        <form action="{{ route('admin.campus-rooms.destroy', $room) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="font-semibold text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data ruangan gedung.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">{{ $rooms->links() }}</div>
        </div>
    </section>
@endsection
