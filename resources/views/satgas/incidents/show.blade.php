@extends('satgas.layouts.app')

@section('title', 'Review Detail Insiden')
@section('hero_eyebrow', 'Detail Insiden')
@section('hero_title', 'Ruang verifikasi dan tindak lanjut insiden')
@section('hero_description', 'Baca ringkasan kejadian, lakukan verifikasi, ubah status, dan catat tindak lanjut dari satu ruang kerja satgas.')

@section('content')
    @php
        $statusBadge = match ($incidentReport->status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'verified' => 'bg-emerald-100 text-emerald-800',
            'investigating' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-indigo-100 text-indigo-700',
            'closed' => 'bg-slate-200 text-slate-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
    @endphp

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                @include('partials.flash')
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Review Satgas</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $incidentReport->title }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Laporan dari <span class="font-semibold text-slate-900">{{ $incidentReport->reporter?->name ?? '-' }}</span>
                    dengan nomor <span class="font-semibold text-slate-900">{{ $incidentReport->report_number }}</span>.
                </p>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Kejadian</h3>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->category?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->location?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal</p>
                        <p class="mt-2 text-slate-800">{{ optional($incidentReport->incident_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge }}">
                                {{ str_replace('_', ' ', $incidentReport->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-8 space-y-5 text-sm leading-7 text-slate-700">
                    <div>
                        <p class="font-semibold text-slate-900">Kronologi</p>
                        <p class="mt-2">{{ $incidentReport->chronology }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Tindakan Awal</p>
                        <p class="mt-2">{{ $incidentReport->initial_action ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Dampak</p>
                        <p class="mt-2">{{ $incidentReport->impact ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Verifikasi Laporan</h3>
                @can('verify', $incidentReport)
                    <form action="{{ route('satgas.incidents.verify', $incidentReport) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="verification_note" class="mb-2 block text-sm font-semibold text-slate-800">Catatan verifikasi</label>
                            <textarea id="verification_note" name="verification_note" rows="5"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                placeholder="Tambahkan catatan verifikasi, temuan awal, atau arahan tindak lanjut.">{{ old('verification_note') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--primary-deep)]">
                            Verifikasi Laporan
                        </button>
                    </form>
                @else
                    <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                        Laporan dengan status ini tidak memerlukan verifikasi ulang. Satgas dapat melanjutkan pemantauan melalui riwayat status dan tindak lanjut.
                    </div>
                @endcan
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Update Progress Laporan</h3>
                @if (! empty($statusOptions))
                    <form action="{{ route('satgas.incidents.update-status', $incidentReport) }}" method="POST" class="mt-6 space-y-4">
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
                            <label for="status_note" class="mb-2 block text-sm font-semibold text-slate-800">Catatan perubahan status</label>
                            <textarea id="status_note" name="status_note" rows="4"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                placeholder="Tambahkan catatan tindak lanjut atau alasan perubahan status.">{{ old('status_note') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Simpan Progress
                        </button>
                    </form>
                @else
                    <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-4 text-sm leading-7 text-slate-600">
                        Tidak ada perubahan status lanjutan yang tersedia untuk kondisi laporan saat ini.
                    </div>
                @endif
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Riwayat Status</h3>
                <div class="mt-6 space-y-4">
                    @forelse ($incidentReport->statusHistories->sortByDesc('created_at') as $history)
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <p class="text-sm font-semibold text-slate-900">{{ str_replace('_', ' ', $history->to_status) }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ optional($history->created_at)->format('d M Y H:i') }} oleh {{ $history->changer?->name ?? 'Sistem' }}
                            </p>
                            @if ($history->change_note)
                                <p class="mt-2 text-sm text-slate-700">{{ $history->change_note }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Tindak Lanjut</h3>
                @can('addFollowUp', $incidentReport)
                    <form action="{{ route('satgas.incidents.follow-ups.store', $incidentReport) }}" method="POST" class="mt-6 space-y-4 border-b border-slate-200 pb-6">
                        @csrf

                        <div>
                            <label for="action_taken" class="mb-2 block text-sm font-semibold text-slate-800">Aksi tindak lanjut</label>
                            <textarea id="action_taken" name="action_taken" rows="4"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                placeholder="Tuliskan langkah tindak lanjut yang sedang atau akan dilakukan.">{{ old('action_taken') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="action_owner_id" class="mb-2 block text-sm font-semibold text-slate-800">PIC</label>
                                <select id="action_owner_id" name="action_owner_id"
                                    class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                    <option value="">Pilih PIC</option>
                                    @foreach ($assignableUsers as $assignableUser)
                                        <option value="{{ $assignableUser->id }}" @selected(old('action_owner_id') == $assignableUser->id)>
                                            {{ $assignableUser->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="due_date" class="mb-2 block text-sm font-semibold text-slate-800">Target selesai</label>
                                <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}"
                                    class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                            </div>
                        </div>

                        <div>
                            <label for="follow_up_status" class="mb-2 block text-sm font-semibold text-slate-800">Status tindak lanjut</label>
                            <select id="follow_up_status" name="status"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                @foreach ($followUpStatusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--primary-deep)]">
                            Tambah Tindak Lanjut
                        </button>
                    </form>
                @endcan
                <div class="mt-6 space-y-4">
                    @forelse ($incidentReport->followUps->sortByDesc('created_at') as $followUp)
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $followUp->actionOwner?->name ?? 'PIC belum ditentukan' }}</p>
                                <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-white">
                                    {{ str_replace('_', ' ', $followUp->status) }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-slate-700">{{ $followUp->action_taken }}</p>
                            <div class="mt-3 flex flex-wrap gap-4 text-xs text-slate-500">
                                <span>Dibuat oleh {{ $followUp->creator?->name ?? 'Sistem' }}</span>
                                <span>Target {{ optional($followUp->due_date)->format('d M Y') ?? '-' }}</span>
                                <span>Selesai {{ optional($followUp->completed_at)->format('d M Y H:i') ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada tindak lanjut yang dicatat untuk laporan ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Lampiran</h3>
                <div class="mt-6 space-y-3">
                    @forelse ($incidentReport->attachments as $attachment)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            <span class="font-medium">{{ $attachment->file_name }}</span>
                            <span class="material-symbols-outlined text-[var(--primary-color)]">attach_file</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Tidak ada lampiran pada laporan ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
