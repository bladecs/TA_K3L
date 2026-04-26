<section class="relative w-full overflow-hidden px-4 pb-20 pt-10 lg:px-8">
    <div class="mx-auto w-full max-w-[1600px]">
        @isset($showInlineFlash)
            @include('partials.flash')
        @endisset

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_380px]">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data"
                class="section-shell rounded-[2rem] p-5 shadow-[0_22px_55px_rgba(15,23,42,0.12)] ring-1 ring-white/80 lg:p-8">
                @csrf

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
                                <span class="material-symbols-outlined">warning</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Informasi temuan</h3>
                                <p class="text-sm text-slate-500">Tuliskan inti bahaya dengan jelas agar area dapat segera diamankan.</p>
                            </div>
                        </div>

                        <div class="grid gap-5">
                            <div>
                                <label for="hazard-title" class="mb-2 block text-sm font-bold text-slate-700">Judul temuan</label>
                                <input id="hazard-title" name="title" type="text" value="{{ old('title') }}"
                                    placeholder="Contoh: Kabel panel terkelupas di area praktikum"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('title')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label for="hazard-location" class="mb-2 block text-sm font-bold text-slate-700">Lokasi utama</label>
                                    <select id="hazard-location" name="location_id"
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
                                    <label for="specific_location" class="mb-2 block text-sm font-bold text-slate-700">Titik spesifik</label>
                                    <input id="specific_location" name="specific_location" type="text" value="{{ old('specific_location') }}"
                                        placeholder="Contoh: Lantai 2, dekat panel utama"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    @error('specific_location')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <span class="material-symbols-outlined">category</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Jenis potensi bahaya</h3>
                                <p class="text-sm text-slate-500">Pilih kategori yang paling mendekati kondisi lapangan.</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            @foreach ($hazardTypes as $type)
                                <label class="block cursor-pointer">
                                    <input type="radio" name="hazard_type" value="{{ $type['key'] }}" class="peer sr-only"
                                        {{ $selectedHazardType === $type['key'] ? 'checked' : '' }}>
                                    <div class="rounded-[1.35rem] border border-slate-200 bg-slate-50 px-4 py-5 text-center text-slate-700 transition peer-checked:-translate-y-0.5 peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--primary-color)] peer-checked:text-white peer-checked:shadow-sm">
                                        <span class="material-symbols-outlined text-4xl">{{ $type['icon'] }}</span>
                                        <p class="mt-3 text-sm font-bold">{{ $type['label'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('hazard_type')
                            <p class="mt-3 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-outlined">edit_note</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Catatan tambahan</h3>
                                <p class="text-sm text-slate-500">Sertakan kondisi aktual, risiko terdekat, atau saran pengamanan sementara.</p>
                            </div>
                        </div>

                        <textarea id="hazard-notes" name="notes" rows="6" placeholder="Jelaskan kondisi bahaya, siapa yang berisiko, dan tindakan awal bila sudah ada."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                <span class="material-symbols-outlined">photo_camera</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Bukti visual</h3>
                                <p class="text-sm text-slate-500">Lampiran akan membantu tim memverifikasi jenis dan tingkat risiko dengan lebih cepat.</p>
                            </div>
                        </div>

                        <label for="hazard-attachments"
                            class="flex min-h-[220px] cursor-pointer flex-col items-center justify-center gap-4 rounded-[1.6rem] border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center transition hover:border-[var(--primary-color)] hover:bg-white">
                            <span class="flex h-18 w-18 items-center justify-center rounded-full bg-[var(--primary-color)] text-white shadow-[0_14px_28px_rgba(10,77,179,0.2)]">
                                <span class="material-symbols-outlined text-4xl">upload</span>
                            </span>
                            <div>
                                <p class="text-xl font-bold text-slate-900">Klik atau seret foto bukti ke sini</p>
                                <p class="mt-2 text-sm font-medium text-slate-500">JPG atau PNG. Maksimal 5 MB per file.</p>
                            </div>
                        </label>
                        <input id="hazard-attachments" name="attachments[]" type="file" multiple class="hidden"
                            accept=".jpg,.jpeg,.png" data-file-preview-input="hazard">
                        @error('attachments')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror

                        <ul data-file-preview-list="hazard" class="mt-4 grid gap-3 sm:grid-cols-2"></ul>
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
            const input = document.querySelector('[data-file-preview-input="hazard"]');
            const list = document.querySelector('[data-file-preview-list="hazard"]');

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
