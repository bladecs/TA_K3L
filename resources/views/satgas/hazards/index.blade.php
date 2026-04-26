@extends('satgas.layouts.app')

@section('title', 'Review Hazard Report')
@section('hero_eyebrow', 'Review Hazard')
@section('hero_title', 'Pantau temuan potensi bahaya')
@section('hero_description', 'Gunakan dashboard review ini untuk menyaring hazard report masuk, memantau status penanganan, dan membuka detail temuan.')

@section('content')
    @php
        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <section class="space-y-6">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-700">Satgas Hazard Review</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Daftar Hazard Report</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                Area ini dipakai Satgas untuk meninjau temuan potensi bahaya, memberi respons awal, dan menandai penanganan selesai.
            </p>
        </div>

        <form action="{{ route('satgas.hazards.index') }}" method="GET" data-auto-submit-form data-live-submit
            data-live-target="[data-live-region='satgas-hazards-table']"
            class="rounded-[1.5rem] bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px]">
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cari Hazard</span>
                    <input type="search" name="q" value="{{ $selectedQuery ?? '' }}"
                        placeholder="No laporan, judul, pelapor, atau lokasi"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-cyan-500 focus:bg-white">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</span>
                    <select name="status"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-cyan-500 focus:bg-white">
                        <option value="">Semua Status</option>
                        <option value="submitted" @selected(($selectedStatus ?? '') === 'submitted')>Submitted</option>
                        <option value="reviewed" @selected(($selectedStatus ?? '') === 'reviewed')>Reviewed</option>
                        <option value="resolved" @selected(($selectedStatus ?? '') === 'resolved')>Resolved</option>
                    </select>
                </label>
            </div>
        </form>

        <section class="grid gap-4 md:grid-cols-3">
            @foreach (['submitted' => 'Submitted', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $status => $label)
                <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-4xl font-bold text-slate-900">{{ $summaryCounts[$status] }}</p>
                </article>
            @endforeach
        </section>

        <div data-live-region="satgas-hazards-table" class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">No. Laporan</th>
                            <th class="px-6 py-4 font-semibold">Pelapor</th>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Lokasi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->reporter?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $report->location?->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('satgas.hazards.show', $report) }}" class="font-semibold text-cyan-700 hover:text-cyan-800">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada hazard report.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div data-live-pagination data-live-target="[data-live-region='satgas-hazards-table']" class="border-t border-slate-200 px-6 py-4">
                @if (filled($selectedQuery ?? null) || filled($selectedStatus ?? null))
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-sm text-slate-500">Hasil difilter secara realtime.</p>
                        <a href="{{ route('satgas.hazards.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">
                            Reset Filter
                        </a>
                    </div>
                @endif
                {{ $reports->links() }}
            </div>
        </div>
    </section>
@endsection
