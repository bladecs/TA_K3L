@php
    $selectedUnsafeConditions = old('unsafe_conditions', []);
    $selectedUnsafeActions = old('unsafe_actions', []);
    $selectedPreventions = old('proposed_preventions', []);
@endphp

<section class="relative w-full overflow-hidden px-4 pb-20 pt-10 lg:px-8">
    <div class="mx-auto w-full max-w-[1600px]">
        @isset($showInlineFlash)
            @include('partials.flash')
        @endisset

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_380px]">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data"
                data-report-form="incident"
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
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-outlined">contact_mail</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Identitas pelapor</h3>
                                <p class="text-sm text-slate-500">Data ini dipakai untuk mengirim pembaruan status melalui email dan WhatsApp.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-3">
                            <div>
                                <label for="reporter_name" class="mb-2 block text-sm font-bold text-slate-700">Nama lengkap</label>
                                <input id="reporter_name" name="reporter_name" type="text" value="{{ old('reporter_name', auth()->user()?->name) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_name')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reporter_email" class="mb-2 block text-sm font-bold text-slate-700">Email aktif</label>
                                <input id="reporter_email" name="reporter_email" type="email" value="{{ old('reporter_email', auth()->user()?->email) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_email')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reporter_whatsapp" class="mb-2 block text-sm font-bold text-slate-700">No. WhatsApp aktif</label>
                                <input id="reporter_whatsapp" name="reporter_whatsapp" type="text" value="{{ old('reporter_whatsapp', auth()->user()?->phone) }}" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_whatsapp')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                                <span class="material-symbols-outlined">healing</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Cedera dan dampak</h3>
                                <p class="text-sm text-slate-500">Bagian ini mengikuti kebutuhan data korban pada formulir investigasi kecelakaan.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="injury_category_id" class="mb-2 block text-sm font-bold text-slate-700">Jenis cedera</label>
                                <select id="injury_category_id" name="injury_category_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Tidak ada / belum diketahui</option>
                                    @foreach ($injuryCategories as $injuryCategory)
                                        <option value="{{ $injuryCategory->id }}" @selected(old('injury_category_id') == $injuryCategory->id)>{{ $injuryCategory->name }}</option>
                                    @endforeach
                                </select>
                                @error('injury_category_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="body_part_id" class="mb-2 block text-sm font-bold text-slate-700">Bagian tubuh yang cedera</label>
                                <select id="body_part_id" name="body_part_id"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Tidak ada / belum diketahui</option>
                                    @foreach ($bodyParts as $bodyPart)
                                        <option value="{{ $bodyPart->id }}" @selected(old('body_part_id') == $bodyPart->id)>{{ $bodyPart->name }}</option>
                                    @endforeach
                                </select>
                                @error('body_part_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ppe_used" class="mb-2 block text-sm font-bold text-slate-700">APD yang digunakan saat kejadian</label>
                                <textarea id="ppe_used" name="ppe_used" rows="4" placeholder="Contoh: helm, sarung tangan, safety shoes, atau tulis tidak ada"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('ppe_used') }}</textarea>
                                @error('ppe_used')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="impact" class="mb-2 block text-sm font-bold text-slate-700">Dampak langsung</label>
                                <textarea id="impact" name="impact" rows="4" placeholder="Tuliskan cedera, kerusakan, gangguan aktivitas, atau dampak lain yang terjadi."
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('impact') }}</textarea>
                                @error('impact')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <span class="material-symbols-outlined">rule</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Analisa awal kejadian</h3>
                                <p class="text-sm text-slate-500">Pilih kondisi atau tindakan tidak aman yang terlihat saat kejadian.</p>
                            </div>
                        </div>

                        <div class="grid gap-6 xl:grid-cols-2">
                            <div>
                                <p class="mb-3 text-sm font-bold text-slate-700">Kondisi lingkungan kerja tidak aman</p>
                                <div class="grid gap-3">
                                    @foreach ($unsafeConditionOptions as $value => $label)
                                        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                                            <input type="checkbox" name="unsafe_conditions[]" value="{{ $value }}" class="mt-1 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]" @checked(in_array($value, $selectedUnsafeConditions, true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('unsafe_conditions')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <p class="mb-3 text-sm font-bold text-slate-700">Tindakan tidak aman saat kejadian</p>
                                <div class="grid gap-3">
                                    @foreach ($unsafeActionOptions as $value => $label)
                                        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                                            <input type="checkbox" name="unsafe_actions[]" value="{{ $value }}" class="mt-1 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]" @checked(in_array($value, $selectedUnsafeActions, true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('unsafe_actions')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="unsafe_condition_cause" class="mb-2 block text-sm font-bold text-slate-700">Penyebab kondisi tidak aman</label>
                                <textarea id="unsafe_condition_cause" name="unsafe_condition_cause" rows="4"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('unsafe_condition_cause') }}</textarea>
                                @error('unsafe_condition_cause')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unsafe_action_cause" class="mb-2 block text-sm font-bold text-slate-700">Penyebab tindakan tidak aman</label>
                                <textarea id="unsafe_action_cause" name="unsafe_action_cause" rows="4"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('unsafe_action_cause') }}</textarea>
                                @error('unsafe_action_cause')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <p class="mb-2 text-sm font-bold text-slate-700">Apakah sudah diperingatkan sebelum kejadian?</p>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warning_given_before_incident" value="1" class="peer sr-only" @checked(old('warning_given_before_incident') === '1')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Ya</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warning_given_before_incident" value="0" class="peer sr-only" @checked(old('warning_given_before_incident') === '0')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Tidak</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="mb-2 text-sm font-bold text-slate-700">Apakah kejadian pernah terjadi sebelumnya?</p>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="incident_previously_occurred" value="1" class="peer sr-only" @checked(old('incident_previously_occurred') === '1')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Ya</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="incident_previously_occurred" value="0" class="peer sr-only" @checked(old('incident_previously_occurred') === '0')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Tidak</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-outlined">task_alt</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Usulan pencegahan</h3>
                                <p class="text-sm text-slate-500">Tambahkan usulan agar kejadian serupa tidak terulang.</p>
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            @foreach ($preventionOptions as $value => $label)
                                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                                    <input type="checkbox" name="proposed_preventions[]" value="{{ $value }}" class="mt-1 rounded border-slate-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]" @checked(in_array($value, $selectedPreventions, true))>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('proposed_preventions')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-5">
                            <label for="prevention_action_plan" class="mb-2 block text-sm font-bold text-slate-700">Hal yang perlu dilakukan untuk menerapkan usulan</label>
                            <textarea id="prevention_action_plan" name="prevention_action_plan" rows="5" placeholder="Tuliskan langkah penerapan, kebutuhan pengamanan, pelatihan, inspeksi, atau perubahan prosedur."
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('prevention_action_plan') }}</textarea>
                            @error('prevention_action_plan')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    <section class="rounded-[1.6rem] bg-white p-5 ring-1 ring-slate-200 lg:p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                                <span class="material-symbols-outlined">personal_injury</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Data korban</h3>
                                <p class="text-sm text-slate-500">Isi bila insiden berdampak langsung pada seseorang.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="victim_name" class="mb-2 block text-sm font-bold text-slate-700">Nama korban</label>
                                <input id="victim_name" name="victim_name" type="text" value="{{ old('victim_name', auth()->user()?->name) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('victim_name')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="victim_position" class="mb-2 block text-sm font-bold text-slate-700">Posisi korban dalam institusi</label>
                                <select id="victim_position" name="victim_position"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih posisi</option>
                                    @foreach ($victimPositionOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('victim_position') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('victim_position')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="victim_position_description" class="mb-2 block text-sm font-bold text-slate-700">Detail posisi</label>
                                <input id="victim_position_description" name="victim_position_description" type="text" value="{{ old('victim_position_description') }}"
                                    placeholder="Contoh: Mahasiswa Teknik Mesin / teknisi laboratorium"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('victim_position_description')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="victim_age" class="mb-2 block text-sm font-bold text-slate-700">Umur korban</label>
                                <input id="victim_age" name="victim_age" type="number" min="0" max="120" value="{{ old('victim_age') }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('victim_age')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <p class="mb-2 block text-sm font-bold text-slate-700">Jenis kelamin</p>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="victim_gender" value="male" class="peer sr-only" @checked(old('victim_gender') === 'male')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Laki-laki</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="victim_gender" value="female" class="peer sr-only" @checked(old('victim_gender') === 'female')>
                                        <span class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition peer-checked:border-[var(--primary-color)] peer-checked:bg-[var(--blue-low-opacity)] peer-checked:text-[var(--primary-color)]">Perempuan</span>
                                    </label>
                                </div>
                                @error('victim_gender')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="victim_address" class="mb-2 block text-sm font-bold text-slate-700">Alamat korban</label>
                                <textarea id="victim_address" name="victim_address" rows="3"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('victim_address') }}</textarea>
                                @error('victim_address')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

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
                                <label for="incident_time" class="mb-2 block text-sm font-bold text-slate-700">Waktu kejadian</label>
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

                            <div>
                                <label for="severity_level" class="mb-2 block text-sm font-bold text-slate-700">Perkiraan tingkat keparahan</label>
                                <select id="severity_level" name="severity_level"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <option value="">Pilih tingkat</option>
                                    @foreach ($severityOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('severity_level') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('severity_level')
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

                            <div class="md:col-span-2">
                                <label for="witness_name" class="mb-2 block text-sm font-bold text-slate-700">Nama saksi bila ada</label>
                                <input id="witness_name" name="witness_name" type="text" value="{{ old('witness_name') }}"
                                    placeholder="Nama saksi utama atau pihak yang melihat kejadian"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('witness_name')
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
                                <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <label for="chronology" class="block text-sm font-bold text-slate-700">Kronologi kejadian</label>
                                    <button type="button" data-voice-target="chronology"
                                        class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                        <span class="material-symbols-outlined text-base" data-voice-icon>mic</span>
                                        <span data-voice-label>Voice to Text</span>
                                    </button>
                                </div>
                                <textarea id="chronology" name="chronology" rows="5" placeholder="Ceritakan urutan kejadian dengan jelas dan runtut."
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('chronology') }}</textarea>
                                @error('chronology')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <label for="cause" class="block text-sm font-bold text-slate-700">Penyebab yang diketahui</label>
                                        <button type="button" data-voice-target="cause"
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                            <span class="material-symbols-outlined text-base" data-voice-icon>mic</span>
                                            <span data-voice-label>Voice to Text</span>
                                        </button>
                                    </div>
                                    <textarea id="cause" name="cause" rows="4" placeholder="Faktor alat, perilaku, lingkungan, atau prosedur."
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">{{ old('cause') }}</textarea>
                                    @error('cause')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <label for="initial_action" class="block text-sm font-bold text-slate-700">Tindakan awal / P3K</label>
                                        <button type="button" data-voice-target="initial_action"
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                            <span class="material-symbols-outlined text-base" data-voice-icon>mic</span>
                                            <span data-voice-label>Voice to Text</span>
                                        </button>
                                    </div>
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

        (() => {
            const form = document.querySelector('[data-report-form="incident"]');
            const buttons = form ? Array.from(form.querySelectorAll('[data-voice-target]')) : [];
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (buttons.length === 0) {
                return;
            }

            if (!SpeechRecognition) {
                buttons.forEach((button) => {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.title = 'Voice to text belum didukung browser ini';
                });
                return;
            }

            let activeRecognition = null;

            const setListeningState = (button, isListening) => {
                const icon = button.querySelector('[data-voice-icon]');
                const label = button.querySelector('[data-voice-label]');

                button.classList.toggle('border-[var(--primary-color)]/15', !isListening);
                button.classList.toggle('bg-white', !isListening);
                button.classList.toggle('text-[var(--primary-color)]', !isListening);
                button.classList.toggle('hover:bg-[var(--blue-low-opacity)]', !isListening);
                button.classList.toggle('border-rose-600', isListening);
                button.classList.toggle('bg-rose-600', isListening);
                button.classList.toggle('text-white', isListening);
                button.classList.toggle('hover:bg-rose-700', isListening);

                if (icon) {
                    icon.textContent = isListening ? 'stop_circle' : 'mic';
                }
                if (label) {
                    label.textContent = isListening ? 'Stop' : 'Voice to Text';
                }
            };

            buttons.forEach((button) => {
                const targetId = button.dataset.voiceTarget;
                const textarea = targetId ? document.getElementById(targetId) : null;

                if (!textarea) {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.title = 'Target voice to text tidak ditemukan';
                    return;
                }

                const recognition = new SpeechRecognition();
                recognition.lang = 'id-ID';
                recognition.interimResults = false;
                recognition.continuous = false;

                let isListening = false;

                recognition.addEventListener('start', () => {
                    isListening = true;
                    activeRecognition = recognition;
                    textarea.focus();
                    setListeningState(button, true);
                });

                recognition.addEventListener('end', () => {
                    isListening = false;
                    if (activeRecognition === recognition) {
                        activeRecognition = null;
                    }
                    setListeningState(button, false);
                });

                recognition.addEventListener('result', (event) => {
                    const transcript = Array.from(event.results)
                        .map((result) => result[0]?.transcript || '')
                        .join(' ')
                        .trim();

                    if (transcript !== '') {
                        textarea.value = textarea.value.trim() === ''
                            ? transcript
                            : `${textarea.value.trim()} ${transcript}`;
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });

                button.addEventListener('click', () => {
                    if (isListening) {
                        recognition.stop();
                        return;
                    }

                    if (activeRecognition) {
                        activeRecognition.stop();
                    }

                    textarea.focus();
                    recognition.start();
                });
            });
        })();
    </script>
@endpush
