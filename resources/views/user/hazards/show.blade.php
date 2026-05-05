@extends('user.layouts.app')

@section('title', 'Detail Hazard Report')

@section('page')
    @php
        $statusBadge = match ($hazardReport->status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'reviewed' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <main class="w-full bg-white pb-14 pt-30">
        <section class="mx-auto grid w-full max-w-[1600px] gap-6 px-4 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <div class="space-y-6">
                <div class="rounded-[1.45rem] bg-white px-10 py-10 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Hazard Report</p>
                    <h1 class="mt-3 text-5xl font-bold text-[var(--primary-color)]">{{ $hazardReport->title }}</h1>
                    <p class="mt-4 text-lg font-semibold text-slate-600">Nomor laporan {{ $hazardReport->report_number }}</p>
                </div>

                <div class="rounded-[1.45rem] bg-white px-10 py-10 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <h2 class="text-2xl font-bold text-[var(--primary-color)]">Ringkasan Temuan</h2>
                    <div class="mt-6 grid gap-5 text-sm md:grid-cols-2">
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Jenis Temuan</p>
                            <p class="mt-2 text-base text-slate-700">{{ str_replace('-', ' ', $hazardReport->hazard_type) }}</p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Lokasi</p>
                            <p class="mt-2 text-base text-slate-700">{{ $hazardReport->location?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Detail Titik</p>
                            <p class="mt-2 text-base text-slate-700">{{ $hazardReport->specific_location ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Status</p>
                            <p class="mt-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge }}">
                                    {{ str_replace('_', ' ', $hazardReport->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Koordinat GIS</p>
                            <p class="mt-2 text-base text-slate-700">
                                @if ($hazardReport->latitude && $hazardReport->longitude)
                                    {{ $hazardReport->latitude }}, {{ $hazardReport->longitude }}
                                @else
                                    Belum dipetakan Satgas
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="font-semibold uppercase tracking-[0.2em] text-slate-400">Level Risiko</p>
                            <p class="mt-2 text-base text-slate-700">{{ $hazardReport->risk_level ? ucfirst($hazardReport->risk_level) : '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div>
                            <p class="text-lg font-bold text-[var(--primary-color)]">Catatan Pelapor</p>
                            <p class="mt-2 text-sm leading-7 text-slate-700">{{ $hazardReport->notes ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-[var(--primary-color)]">Respons Satgas</p>
                            <p class="mt-2 text-sm leading-7 text-slate-700">{{ $hazardReport->response_note ?: 'Belum ada respons satgas.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-[1.45rem] bg-white px-10 py-10 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <h2 class="text-2xl font-bold text-[var(--primary-color)]">Timeline Penanganan</h2>
                    <div class="mt-6 space-y-4 text-sm">
                        <div class="rounded-2xl bg-[#f8fbff] px-4 py-4">
                            <p class="font-semibold text-[var(--primary-color)]">Dikirim</p>
                            <p class="mt-1 text-slate-600">{{ optional($hazardReport->submitted_at)->format('d M Y H:i') ?? '-' }} WIB</p>
                        </div>
                        <div class="rounded-2xl bg-[#f8fbff] px-4 py-4">
                            <p class="font-semibold text-[var(--primary-color)]">Ditinjau</p>
                            <p class="mt-1 text-slate-600">{{ optional($hazardReport->reviewed_at)->format('d M Y H:i') ?? '-' }} WIB</p>
                            <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->reviewer?->name ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#f8fbff] px-4 py-4">
                            <p class="font-semibold text-[var(--primary-color)]">Diselesaikan</p>
                            <p class="mt-1 text-slate-600">{{ optional($hazardReport->resolved_at)->format('d M Y H:i') ?? '-' }} WIB</p>
                            <p class="mt-1 text-xs text-slate-500">oleh {{ $hazardReport->resolver?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.45rem] bg-white px-10 py-10 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
                    <h2 class="text-2xl font-bold text-[var(--primary-color)]">Lampiran</h2>
                    <div class="mt-6 space-y-3">
                        @forelse ($hazardReport->attachments as $attachment)
                            <div class="flex items-center justify-between rounded-2xl bg-[#f8fbff] px-4 py-4 text-sm text-slate-700">
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
    </main>
@endsection
