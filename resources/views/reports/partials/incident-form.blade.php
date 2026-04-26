@php
    $severityPalette = [
        'low' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'medium' => 'border-amber-200 bg-amber-50 text-amber-700',
        'high' => 'border-orange-200 bg-orange-50 text-orange-700',
        'critical' => 'border-rose-200 bg-rose-50 text-rose-700',
    ];
@endphp

<section class="relative w-full overflow-hidden px-4 pb-20 pt-10 lg:px-8">
    <div class="mx-auto w-full max-w-[1600px]">
        @isset($showInlineFlash)
            @include('partials.flash')
        @endisset

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_380px]">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data"
                class="section-shell rounded-[2rem] p-5 shadow-[0_22px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/80 lg:p-8">
                @csrf
                <input type="hidden" name="victim_type" value="{{ old('victim_type', 'self') }}">

                <div class="mb-8 flex flex-col gap-5 border-b border-slate-200 pb-7 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <span class="inline-flex rounded-full border border-[var(--primary-color)]/12 bg-[var(--blue-low-opacity)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">
                            {{ $panelEyebrow }}
                        </span>
                        <h2 class="mt-4 text-3xl font-extrabold text-slate-900 lg:text-4xl">{{ $panelTitle }}</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-500">{{ $panelDescription }}</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ($summaryTips as $tip)
                            <div class="rounded-[1.25rem] bg-white px-4 py-4 ring-1 ring-slate-200">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $tip['label'] }}</p>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-700">{{ $tip['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6">
                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                <span class="material-symbols-outlined">description</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Identitas kejadian</h3>
                                <p class="text-sm text-slate-500">Isi ringkasan dasar agar laporan cepat divalidasi.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="incident_date" class="mb-2 block text-sm font-bold text-slate-700">Tanggal kejadian</label>
                                <input id="incident_date" name="incident_date" type="date" value="{{ old('incident_date') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('incident_date')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="incident_time" class="mb-2 block text-sm font-bold text-slate-700">Jam kejadian</label>
                                <input id="incident_time" name="incident_time" type="time" value="{{ old('incident_time') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('incident_time')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location_id" class="mb-2 block text-sm font-bold text-slate-700">Lokasi kejadian</label>
                                <select id="location_id" name="location_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih lokasi</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="incident_category_id" class="mb-2 block text-sm font-bold text-slate-700">Kategori insiden</label>
                                <select id="incident_category_id" name="incident_category_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('incident_category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('incident_category_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="title" class="mb-2 block text-sm font-bold text-slate-700">Judul atau kondisi utama</label>
                                <input id="title" name="title" type="text" value="{{ old('title') }}"
                                    placeholder="Contoh: Operator mengalami luka ringan saat perawatan mesin"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('title')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                                <span class="material-symbols-outlined">priority_high</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Level dampak</h3>
                                <p class="text-sm text-slate-500">Pilih tingkat keparahan agar prioritas respons lebih tepat.</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-4">
                            @foreach ($severityOptions as $severityKey => $severityLabel)
                                <label class="block cursor-pointer">
                                    <input type="radio" name="severity_level" value="{{ $severityKey }}" class="peer sr-only"
                                        @checked(old('severity_level', 'medium') === $severityKey)>
                                    <div class="rounded-[1.35rem] border px-4 py-4 transition peer-checked:-translate-y-0.5 peer-checked:shadow-sm {{ $severityPalette[$severityKey] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em]">Severity</p>
                                        <p class="mt-2 text-lg font-bold">{{ $severityLabel }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('severity_level')
                            <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <span class="material-symbols-outlined">health_and_safety</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Detail korban dan dampak</h3>
                                <p class="text-sm text-slate-500">Bagian ini opsional, tapi sangat membantu analisis Satgas.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="injury_category_id" class="mb-2 block text-sm font-bold text-slate-700">Kategori cedera</label>
                                <select id="injury_category_id" name="injury_category_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih kategori cedera</option>
                                    @foreach ($injuryCategories as $injuryCategory)
                                        <option value="{{ $injuryCategory->id }}" @selected(old('injury_category_id') == $injuryCategory->id)>{{ $injuryCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('injury_category_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="body_part_id" class="mb-2 block text-sm font-bold text-slate-700">Bagian tubuh terdampak</label>
                                <select id="body_part_id" name="body_part_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih bagian tubuh</option>
                                    @foreach ($bodyParts as $bodyPart)
                                        <option value="{{ $bodyPart->id }}" @selected(old('body_part_id') == $bodyPart->id)>{{ $bodyPart->name }}</option>
                                    @endforeach
                                </select>
                                @error('body_part_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="impact" class="mb-2 block text-sm font-bold text-slate-700">Dampak kejadian</label>
                                <textarea id="impact" name="impact" rows="3" placeholder="Jelaskan dampak pada korban, alat, atau proses kerja."
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('impact') }}</textarea>
                                @error('impact')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                <span class="material-symbols-outlined">lab_profile</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Uraian kejadian</h3>
                                <p class="text-sm text-slate-500">Jelaskan apa yang terjadi, penyebab, dan respons awal yang sudah dilakukan.</p>
                            </div>
                        </div>

                        <div class="grid gap-5">
                            <div>
                                <label for="chronology" class="mb-2 block text-sm font-bold text-slate-700">Kronologi kejadian</label>
                                <textarea id="chronology" name="chronology" rows="5" placeholder="Ceritakan urutan kejadian dengan jelas dan runtut."
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('chronology') }}</textarea>
                                @error('chronology')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label for="cause" class="mb-2 block text-sm font-bold text-slate-700">Penyebab yang diketahui</label>
                                    <textarea id="cause" name="cause" rows="4" placeholder="Faktor alat, perilaku, lingkungan, atau prosedur."
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('cause') }}</textarea>
                                    @error('cause')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="initial_action" class="mb-2 block text-sm font-bold text-slate-700">Tindakan awal / P3K</label>
                                    <textarea id="initial_action" name="initial_action" rows="4" placeholder="Tuliskan bantuan pertama atau tindakan pengamanan yang sudah dilakukan."
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('initial_action') }}</textarea>
                                    @error('initial_action')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                <span class="material-symbols-outlined">photo_camera</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Lampiran pendukung</h3>
                                <p class="text-sm text-slate-500">Tambahkan maksimal 3 file untuk memudahkan verifikasi lapangan.</p>
                            </div>
                        </div>

                        <label for="incident-attachments"
                            class="flex min-h-[220px] cursor-pointer flex-col items-center justify-center gap-4 rounded-[1.6rem] border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center transition hover:border-[var(--primary-color)] hover:bg-white">
                            <span class="flex h-18 w-18 items-center justify-center rounded-full bg-[var(--primary-color)] text-white shadow-[0_14px_28px_rgba(10,77,179,0.2)]">
                                <span class="material-symbols-outlined text-4xl">upload</span>
                            </span>
                            <div>
                                <p class="text-xl font-bold text-slate-900">Klik atau seret file pendukung ke area ini</p>
                                <p class="mt-2 text-sm font-medium text-slate-500">JPG, PNG, PDF, DOC, DOCX. Maksimal 5 MB per file.</p>
                            </div>
                        </label>
                        <input id="incident-attachments" name="attachments[]" type="file" multiple class="hidden"
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" data-file-preview-input="incident">
                        @error('attachments')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror

                        <ul data-file-preview-list="incident" class="mt-4 grid gap-3 sm:grid-cols-2"></ul>
                    </section>
                </div>

                <div class="mt-8 flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-3 rounded-full bg-[var(--primary-color)] px-7 py-4 text-sm font-bold text-white shadow-[0_18px_35px_rgba(10,77,179,0.2)] transition hover:bg-[var(--primary-deep)]">
                        {{ $submitLabel }}
                        <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
                    </button>
                    <a href="{{ $cancelUrl }}"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-7 py-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </form>

            <aside class="space-y-6">
                <article class="section-shell rounded-[2rem] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-white/80">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">{{ $sidebarEyebrow }}</p>
                    <h3 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $sidebarTitle }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-500">{{ $sidebarDescription }}</p>

                    <div class="mt-6 space-y-4">
                        @foreach ($sidebarSteps as $index => $step)
                            <div class="rounded-[1.35rem] bg-white px-4 py-4 ring-1 ring-slate-200">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--blue-low-opacity)] text-sm font-bold text-[var(--primary-color)]">{{ $index + 1 }}</span>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $step['title'] }}</p>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $step['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[2rem] bg-[#7a2c00] p-6 text-white shadow-[0_18px_45px_rgba(122,44,0,0.2)]">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#d95b00]">
                            <span class="material-symbols-outlined">notification_important</span>
                        </span>
                        <div>
                            <h3 class="text-xl font-bold">{{ $emergencyTitle }}</h3>
                            <p class="mt-2 text-sm leading-7 text-white/85">{{ $emergencyDescription }}</p>
                        </div>
                    </div>
                </article>
            </aside>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        (() => {
            const input = document.querySelector('[data-file-preview-input="incident"]');
            const list = document.querySelector('[data-file-preview-list="incident"]');

            if (!input || !list) {
                return;
            }

            const renderFiles = () => {
                list.innerHTML = '';

                Array.from(input.files || []).forEach((file) => {
                    const item = document.createElement('li');
                    item.className = 'rounded-[1.2rem] bg-white px-4 py-3 text-sm font-medium text-slate-600 ring-1 ring-slate-200';
                    item.textContent = `${file.name} (${Math.ceil(file.size / 1024)} KB)`;
                    list.appendChild(item);
                });
            };

            input.addEventListener('change', renderFiles);
        })();
    </script>
@endpush
