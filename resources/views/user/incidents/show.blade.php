@extends('user.layouts.app')

@section('title', 'Detail Laporan Insiden')

@section('page')
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

        $statusMessage = match ($incidentReport->status) {
            'submitted' => 'Laporan Anda sudah masuk dan sedang menunggu validasi awal dari Satgas.',
            'verified' => 'Laporan sudah lolos validasi awal dan siap masuk tindak lanjut berikutnya.',
            'investigating' => 'Satgas sedang melakukan penelusuran lebih lanjut terhadap kejadian yang dilaporkan.',
            'resolved' => 'Tindakan perbaikan utama sudah dilakukan dan laporan menunggu penutupan akhir.',
            'closed' => 'Kasus sudah diselesaikan dan laporan ditutup.',
            'rejected' => 'Laporan ditolak atau perlu perbaikan data sebelum diproses kembali.',
            default => 'Status laporan masih dalam proses pembaruan.',
        };

        $yesNo = fn ($value) => $value === null ? '-' : ($value ? 'Ya' : 'Tidak');
        $victimPositions = [
            'mahasiswa' => 'Mahasiswa',
            'karyawan' => 'Karyawan',
            'publik' => 'Publik',
            'kontraktor' => 'Kontraktor',
            'pengunjung' => 'Pengunjung',
        ];
        $unsafeConditionOptions = [
            'pengamanan_tidak_memadai' => 'Pengamanan yang tidak memadai',
            'tidak_ada_pengamanan_lokasi_berbahaya' => 'Tidak ada pengamanan pada lokasi berbahaya',
            'apd_cacat' => 'Alat pelindung diri yang cacat',
            'alat_kerja_cacat' => 'Alat kerja yang cacat',
            'area_kerja_berbahaya' => 'Area kerja yang berbahaya',
            'pencahayaan_tidak_memadai' => 'Pencahayaan tidak memadai',
            'ventilasi_tidak_memadai' => 'Ventilasi tidak memadai',
            'kurang_apd' => 'Kurangnya alat pelindung diri (APD)',
            'kurang_alat_kerja' => 'Kurangnya alat kerja yang memadai',
            'pakaian_tidak_aman' => 'Pakaian yang tidak aman',
            'kurang_pelatihan' => 'Tidak ada atau kurangnya pelatihan kerja',
            'lain_lain' => 'Lain-lain',
        ];
        $unsafeActionOptions = [
            'pengoperasian_tanpa_ijin' => 'Pengoperasian tanpa ijin',
            'kecepatan_tidak_terkendali' => 'Pengoperasian dengan kecepatan tidak terkendali',
            'alat_pengaman_tidak_berfungsi' => 'Menyebabkan alat pengaman tidak berfungsi',
            'menggunakan_alat_cacat' => 'Menggunakan alat kerja yang cacat',
            'penggunaan_alat_tidak_aman' => 'Penggunaan alat kerja dengan cara tidak aman',
            'pengangkatan_tidak_aman' => 'Pengangkatan tidak aman',
            'posisi_kerja_tidak_aman' => 'Menyebabkan posisi kerja tidak aman',
            'pengalih_perhatian' => 'Pengalih perhatian atau bercanda saat bekerja',
            'tidak_menggunakan_apd' => 'Tidak menggunakan alat pelindung diri (APD)',
            'tidak_menggunakan_alat_tersedia' => 'Tidak menggunakan alat kerja yang tersedia',
            'lain_lain' => 'Lain-lain',
        ];
        $preventionOptions = [
            'hentikan_aktivitas' => 'Hentikan aktivitas',
            'pengamanan_sumber_bahaya' => 'Beri pengamanan pada sumber bahaya',
            'rancang_ulang_langkah_kerja' => 'Rancang ulang langkah kerja',
            'kebijakan_baru' => 'Buat kebijakan / peraturan baru',
            'inspeksi_rutin' => 'Inspeksi rutin pada sumber bahaya',
            'pelatihan_tenaga_kerja' => 'Beri pelatihan pada tenaga kerja',
            'pelatihan_pengawas' => 'Beri pelatihan pada pengawas kerja',
            'rancang_ulang_tempat_kerja' => 'Rancang ulang tempat kerja',
            'perkuat_kebijakan' => 'Perkuat penerapan kebijakan yang sudah ada',
            'penggunaan_apd' => 'Penggunaan alat pelindung diri (APD)',
            'lain_lain' => 'Lain-lain',
        ];
        $labels = fn (?array $values, array $options) => collect($values ?? [])->map(fn ($value) => $options[$value] ?? $value)->implode(', ') ?: '-';
    @endphp

    <main class="w-full bg-[#f6f8fc] px-4 pb-12 pt-34 lg:px-6 xl:px-8">
        <div class="mx-auto flex w-full max-w-[1600px] flex-col gap-6">
            <section id="header" class="relative overflow-hidden rounded-[1.4rem] px-6 py-8 lg:px-10 lg:py-10">
                <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl text-white">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/90">Detail Laporan</p>
                        <h1 class="mt-3 text-4xl font-bold leading-tight lg:text-5xl">{{ $incidentReport->title }}</h1>
                        <p class="mt-4 text-sm leading-7 text-white/90">
                            Nomor laporan: <span class="font-semibold text-white">{{ $incidentReport->report_number }}</span>
                        </p>
                    </div>

                    <span
                        class="inline-flex rounded-full bg-white px-5 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--primary-color)]">
                        {{ str_replace('_', ' ', $incidentReport->status) }}
                    </span>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <div class="space-y-6">
                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Ringkasan Status</p>
                                <h3 class="mt-2 text-2xl font-bold text-slate-900">Posisi laporan saat ini</h3>
                            </div>
                            <span class="inline-flex rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $statusBadge }}">
                                {{ str_replace('_', ' ', $incidentReport->status) }}
                            </span>
                        </div>
                        <p class="mt-5 text-sm leading-7 text-slate-600">{{ $statusMessage }}</p>
                    </div>

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Informasi Kejadian</h3>
                        <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->category?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->location?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ optional($incidentReport->incident_date)->format('d M Y') }} {{ $incidentReport->incident_time ? substr($incidentReport->incident_time, 0, 5) : '' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Keparahan</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ ucfirst($incidentReport->severity_level) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Pelapor</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->reporter?->name ?? $incidentReport->reporter_name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Korban</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->victim_name ?? $incidentReport->victim?->name ?? 'Diri sendiri / tidak dicatat' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Saksi</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->witness_name ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Data Korban dan Cedera</h3>
                        <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Posisi korban</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $victimPositions[$incidentReport->victim_position] ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Detail posisi</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->victim_position_description ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis kelamin / umur</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->victim_gender === 'male' ? 'Laki-laki' : ($incidentReport->victim_gender === 'female' ? 'Perempuan' : '-') }}{{ $incidentReport->victim_age ? ' / '.$incidentReport->victim_age.' tahun' : '' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis cedera</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->injuryCategory?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Bagian tubuh cedera</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->bodyPart?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">APD digunakan</dt>
                                <dd class="mt-2 text-sm text-slate-800">{{ $incidentReport->ppe_used ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Kronologi dan Dampak</h3>
                        <div class="mt-6 space-y-5 text-sm leading-7 text-slate-700">
                            <div>
                                <p class="font-semibold text-slate-900">Kronologi</p>
                                <p class="mt-2">{{ $incidentReport->chronology }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Penyebab Awal</p>
                                <p class="mt-2">{{ $incidentReport->cause ?: '-' }}</p>
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

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Analisa Awal dan Pencegahan</h3>
                        <div class="mt-6 space-y-5 text-sm leading-7 text-slate-700">
                            <div>
                                <p class="font-semibold text-slate-900">Kondisi tidak aman</p>
                                <p class="mt-2">{{ $labels($incidentReport->unsafe_conditions, $unsafeConditionOptions) }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Tindakan tidak aman</p>
                                <p class="mt-2">{{ $labels($incidentReport->unsafe_actions, $unsafeActionOptions) }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Penyebab kondisi/tindakan tidak aman</p>
                                <p class="mt-2">{{ $incidentReport->unsafe_condition_cause ?: '-' }}</p>
                                <p class="mt-2">{{ $incidentReport->unsafe_action_cause ?: '-' }}</p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <p><span class="font-semibold text-slate-900">Pernah diperingatkan:</span> {{ $yesNo($incidentReport->warning_given_before_incident) }}</p>
                                <p><span class="font-semibold text-slate-900">Pernah terjadi sebelumnya:</span> {{ $yesNo($incidentReport->incident_previously_occurred) }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Usulan pencegahan</p>
                                <p class="mt-2">{{ $labels($incidentReport->proposed_preventions, $preventionOptions) }}</p>
                                <p class="mt-2">{{ $incidentReport->prevention_action_plan ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[1.2rem] bg-[var(--primary-color)] p-6 text-white shadow-[0_16px_40px_rgba(0,72,167,0.22)] lg:p-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">Next Action</p>
                        <h3 class="mt-2 text-2xl font-bold">Yang bisa Anda lakukan sekarang</h3>
                        <div class="mt-5 space-y-3">
                            <div class="rounded-xl bg-white/10 px-4 py-4 text-sm leading-7 text-white/90">
                                Pantau riwayat status untuk melihat apakah Satgas menambahkan catatan verifikasi atau tindak lanjut.
                            </div>
                            <a href="{{ route('user.incidents.status', ['q' => $incidentReport->report_number]) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-[var(--primary-color)]">
                                Kembali ke status laporan
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                            </a>
                        </div>
                    </div>

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Riwayat Status</h3>
                        <div class="mt-6 space-y-4">
                            @forelse ($incidentReport->statusHistories->sortByDesc('created_at') as $history)
                                <div class="rounded-[0.95rem] bg-[#f8fbff] px-4 py-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ str_replace('_', ' ', $history->to_status) }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ optional($history->created_at)->format('d M Y H:i') }} oleh
                                        {{ $history->changer?->name ?? 'Sistem' }}
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

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Tindak Lanjut</h3>
                        <div class="mt-6 space-y-4">
                            @forelse ($incidentReport->followUps->sortByDesc('created_at') as $followUp)
                                <div class="rounded-[0.95rem] bg-[#f8fbff] px-4 py-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $followUp->actionOwner?->name ?? 'PIC belum ditentukan' }}
                                        </p>
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

                    <div class="rounded-[1.2rem] bg-white/95 p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                        <h3 class="text-lg font-semibold text-slate-900">Lampiran</h3>
                        <div class="mt-6 space-y-3">
                            @forelse ($incidentReport->attachments as $attachment)
                                <div class="flex items-center justify-between rounded-[0.95rem] bg-[#f8fbff] px-4 py-3 text-sm text-slate-700">
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
        </div>
    </main>
@endsection
