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

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                @include('partials.flash')
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Review Satgas</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $incidentReport->title }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    Laporan dari <span class="font-semibold text-slate-900">{{ $incidentReport->reporter?->name ?? $incidentReport->reporter_name ?? '-' }}</span>
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
                        <p class="mt-2 text-slate-800">{{ optional($incidentReport->incident_date)->format('d M Y') }} {{ $incidentReport->incident_time ? substr($incidentReport->incident_time, 0, 5) : '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge }}">
                                {{ str_replace('_', ' ', $incidentReport->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Korban</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->victim_name ?? $incidentReport->victim?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Saksi</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->witness_name ?: '-' }}</p>
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

            <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Data Investigasi dari Pelapor</h3>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Posisi korban</p>
                        <p class="mt-2 text-slate-800">{{ $victimPositions[$incidentReport->victim_position] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis kelamin / umur</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->victim_gender === 'male' ? 'Laki-laki' : ($incidentReport->victim_gender === 'female' ? 'Perempuan' : '-') }}{{ $incidentReport->victim_age ? ' / '.$incidentReport->victim_age.' tahun' : '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Jenis cedera</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->injuryCategory?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Bagian tubuh cedera</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->bodyPart?->name ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">APD digunakan</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->ppe_used ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kondisi tidak aman</p>
                        <p class="mt-2 text-slate-800">{{ $labels($incidentReport->unsafe_conditions, $unsafeConditionOptions) }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tindakan tidak aman</p>
                        <p class="mt-2 text-slate-800">{{ $labels($incidentReport->unsafe_actions, $unsafeActionOptions) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sudah diperingatkan</p>
                        <p class="mt-2 text-slate-800">{{ $yesNo($incidentReport->warning_given_before_incident) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Pernah terjadi sebelumnya</p>
                        <p class="mt-2 text-slate-800">{{ $yesNo($incidentReport->incident_previously_occurred) }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Usulan pencegahan</p>
                        <p class="mt-2 text-slate-800">{{ $labels($incidentReport->proposed_preventions, $preventionOptions) }}</p>
                        <p class="mt-2 text-slate-800">{{ $incidentReport->prevention_action_plan ?: '-' }}</p>
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

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="injury_category_id" class="mb-2 block text-sm font-semibold text-slate-800">Kategori cedera</label>
                                <select id="injury_category_id" name="injury_category_id"
                                    class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                    <option value="">Tidak ada / belum ditentukan</option>
                                    @foreach ($injuryCategories as $injuryCategory)
                                        <option value="{{ $injuryCategory->id }}" @selected(old('injury_category_id', $incidentReport->injury_category_id) == $injuryCategory->id)>
                                            {{ $injuryCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('injury_category_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="body_part_id" class="mb-2 block text-sm font-semibold text-slate-800">Bagian tubuh terdampak</label>
                                <select id="body_part_id" name="body_part_id"
                                    class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                                    <option value="">Tidak ada / belum ditentukan</option>
                                    @foreach ($bodyParts as $bodyPart)
                                        <option value="{{ $bodyPart->id }}" @selected(old('body_part_id', $incidentReport->body_part_id) == $bodyPart->id)>
                                            {{ $bodyPart->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('body_part_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="impact" class="mb-2 block text-sm font-semibold text-slate-800">Dampak terkonfirmasi</label>
                            <textarea id="impact" name="impact" rows="4"
                                class="w-full rounded-3xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40"
                                placeholder="Tuliskan dampak yang dikonfirmasi Satgas saat verifikasi.">{{ old('impact', $incidentReport->impact) }}</textarea>
                            @error('impact')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
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
