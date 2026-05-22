@php
    $selectedUnsafeConditions = old('unsafe_conditions', []);
    $selectedUnsafeActions = old('unsafe_actions', []);
    $selectedPreventions = old('proposed_preventions', []);
    $isPublicIncidentForm = $isPublicIncidentForm ?? false;
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

                    @unless ($isPublicIncidentForm)
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
                    @endunless

                    @unless ($isPublicIncidentForm)
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
                    @endunless

                    @unless ($isPublicIncidentForm)
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
                    @endunless

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
                                        <option value="{{ $location->id }}" data-location-name="{{ $location->name }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4" data-incident-gps>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Koordinat GPS lokasi kejadian</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500" data-incident-gps-status>
                                            Sistem akan mengisi koordinat otomatis ketika izin lokasi diberikan.
                                        </p>
                                    </div>
                                    <button type="button" data-incident-gps-button
                                        class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                        <span class="material-symbols-outlined text-base" data-incident-gps-icon>my_location</span>
                                        <span data-incident-gps-label>Ambil lokasi</span>
                                    </button>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div>
                                        <label for="incident-latitude" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Latitude</label>
                                        <input id="incident-latitude" name="latitude" type="text" value="{{ old('latitude') }}" readonly
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                        @error('latitude')
                                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="incident-longitude" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Longitude</label>
                                        <input id="incident-longitude" name="longitude" type="text" value="{{ old('longitude') }}" readonly
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                        @error('longitude')
                                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="incident-location-accuracy" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Akurasi meter</label>
                                        <input id="incident-location-accuracy" name="location_accuracy" type="text" value="{{ old('location_accuracy') }}" readonly
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                        @error('location_accuracy')
                                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 {{ old('specific_location') ? '' : 'hidden' }}" data-incident-specific-location>
                                <label for="incident-specific-location" class="mb-2 block text-sm font-bold text-slate-700">Patokan lokasi</label>
                                <input id="incident-specific-location" name="specific_location" type="text" value="{{ old('specific_location') }}"
                                    placeholder="Contoh: lantai 2 dekat tangga, depan lab CNC, samping panel listrik"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                <p class="mt-2 text-xs leading-5 text-slate-500" data-incident-specific-location-help>
                                    Opsional bila titik GPS sudah masuk area gedung. Satgas akan memverifikasi detail lokasi final saat review.
                                </p>
                                @error('specific_location')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4" data-incident-room-panel>
                                <input type="hidden" name="building_key" id="incident-building-key" value="{{ old('building_key') }}">
                                <p class="text-sm font-bold text-slate-800">Gedung, lantai, dan ruangan</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500" data-incident-room-help>Pilih lantai lalu ruangan bila lokasi berada di dalam area Polman dan data ruangan tersedia.</p>
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="incident-building-floor" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Lantai</label>
                                        <select id="incident-building-floor" name="building_floor"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                            <option value="">Pilih lantai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="incident-campus-room-id" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Ruangan</label>
                                        <select id="incident-campus-room-id" name="campus_room_id"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                            <option value="">Pilih ruangan</option>
                                        </select>
                                    </div>
                                </div>
                                @error('campus_room_id')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($isPublicIncidentForm)
                                <div class="md:col-span-2 rounded-2xl border border-rose-100 bg-rose-50/60 p-4" data-incident-injuries>
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">Catatan luka bila ada</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">Tambahkan setiap titik luka secara terpisah, misalnya tangan kanan memar dan lutut kiri lecet.</p>
                                        </div>
                                        <button type="button" data-add-injury
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                            <span class="material-symbols-outlined text-base">add</span>
                                            Tambah luka
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3" data-injury-list>
                                        @foreach (old('injuries', [['injury_category_id' => null, 'body_part_id' => null, 'description' => null]]) as $index => $injury)
                                            <div class="grid gap-3 rounded-2xl bg-white p-3 ring-1 ring-rose-100 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto]" data-injury-row>
                                                <div>
                                                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Jenis luka</label>
                                                    <select name="injuries[{{ $index }}][injury_category_id]"
                                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                                        <option value="">Pilih jenis luka</option>
                                                        @foreach ($injuryCategories as $injuryCategory)
                                                            <option value="{{ $injuryCategory->id }}" @selected(($injury['injury_category_id'] ?? null) == $injuryCategory->id)>{{ $injuryCategory->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Bagian terdampak</label>
                                                    <select name="injuries[{{ $index }}][body_part_id]"
                                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                                        <option value="">Pilih bagian tubuh</option>
                                                        @foreach ($bodyParts as $bodyPart)
                                                            <option value="{{ $bodyPart->id }}" @selected(($injury['body_part_id'] ?? null) == $bodyPart->id)>{{ $bodyPart->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Keterangan singkat</label>
                                                    <input name="injuries[{{ $index }}][description]" type="text" value="{{ $injury['description'] ?? '' }}"
                                                        placeholder="Contoh: bengkak, berdarah, perih"
                                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                                </div>

                                                <button type="button" data-remove-injury
                                                    class="inline-flex h-11 w-11 items-center justify-center self-end rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('injuries')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                    @error('injuries.*.injury_category_id')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                    @error('injuries.*.body_part_id')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @unless ($isPublicIncidentForm)
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
                            @endunless

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
            const gpsPanel = form?.querySelector('[data-incident-gps]');
            const button = gpsPanel?.querySelector('[data-incident-gps-button]');
            const icon = gpsPanel?.querySelector('[data-incident-gps-icon]');
            const label = gpsPanel?.querySelector('[data-incident-gps-label]');
            const status = gpsPanel?.querySelector('[data-incident-gps-status]');
            const latitudeInput = gpsPanel?.querySelector('#incident-latitude');
            const longitudeInput = gpsPanel?.querySelector('#incident-longitude');
            const accuracyInput = gpsPanel?.querySelector('#incident-location-accuracy');
            const locationSelect = form?.querySelector('#location_id');
            const specificLocationPanel = form?.querySelector('[data-incident-specific-location]');
            const specificLocationInput = form?.querySelector('#incident-specific-location');
            const specificLocationHelp = form?.querySelector('[data-incident-specific-location-help]');
            const roomPanel = form?.querySelector('[data-incident-room-panel]');
            const roomHelp = roomPanel?.querySelector('[data-incident-room-help]');
            const buildingKeyInput = form?.querySelector('#incident-building-key');
            const floorSelect = form?.querySelector('#incident-building-floor');
            const roomSelect = form?.querySelector('#incident-campus-room-id');
            const campusBuildings = @json($campusBuildingPolygons ?? []);
            const campusRooms = @json($campusRoomsByBuilding ?? []);
            const oldFloor = @json(old('building_floor'));
            const oldRoomId = @json(old('campus_room_id'));

            if (!gpsPanel || !button || !latitudeInput || !longitudeInput || !accuracyInput || !status) {
                return;
            }

            const setButtonState = (isLoading) => {
                button.disabled = isLoading;
                button.classList.toggle('opacity-60', isLoading);
                button.classList.toggle('cursor-wait', isLoading);

                if (icon) {
                    icon.textContent = isLoading ? 'progress_activity' : 'my_location';
                }
                if (label) {
                    label.textContent = isLoading ? 'Mengambil...' : 'Ambil lokasi';
                }
            };

            const normalize = (value) => String(value || '')
                .toLowerCase()
                .replace(/&/g, 'dan')
                .replace(/[^a-z0-9]+/g, ' ')
                .trim();

            const isPointInsidePolygon = ([lat, lng], polygon) => {
                let inside = false;

                for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                    const yi = polygon[i][0];
                    const xi = polygon[i][1];
                    const yj = polygon[j][0];
                    const xj = polygon[j][1];
                    const intersects = ((yi > lat) !== (yj > lat))
                        && (lng < ((xj - xi) * (lat - yi)) / (yj - yi) + xi);

                    if (intersects) {
                        inside = !inside;
                    }
                }

                return inside;
            };

            const buildingAt = (lat, lng) => campusBuildings.find((building) => (
                Array.isArray(building.coordinates) && isPointInsidePolygon([lat, lng], building.coordinates)
            ));

            const buildingByLocationName = () => {
                const entries = new Map();

                campusBuildings.forEach((building) => {
                    entries.set(normalize(building.name), building);
                });

                return entries;
            };

            const locationBuildingMap = buildingByLocationName();

            const selectedLocationName = () => {
                if (!locationSelect) {
                    return '';
                }

                const option = locationSelect.options[locationSelect.selectedIndex];
                return option?.dataset.locationName || option?.textContent || '';
            };

            const setRoomControlsState = (disabled, message = null) => {
                [floorSelect, roomSelect].forEach((select) => {
                    if (!select) {
                        return;
                    }

                    select.disabled = disabled;
                    select.classList.toggle('opacity-60', disabled);
                    select.classList.toggle('cursor-not-allowed', disabled);
                    select.classList.toggle('bg-slate-100', disabled);
                    select.classList.toggle('bg-white', !disabled);
                });

                if (roomHelp && message) {
                    roomHelp.textContent = message;
                }
            };

            const selectLocationByName = (name) => {
                if (!locationSelect) {
                    return false;
                }

                const targetName = normalize(name);
                const option = Array.from(locationSelect.options).find((item) => (
                    normalize(item.dataset.locationName || item.textContent) === targetName
                ));

                if (!option) {
                    return false;
                }

                locationSelect.value = option.value;
                locationSelect.dispatchEvent(new Event('change', { bubbles: true }));
                return true;
            };

            const resetRoomSelection = (disabled = false, message = 'Pilih lantai lalu ruangan bila lokasi berada di dalam area Polman dan data ruangan tersedia.') => {
                if (buildingKeyInput) {
                    buildingKeyInput.value = '';
                }
                if (floorSelect) {
                    floorSelect.innerHTML = '<option value="">Pilih lantai</option>';
                }
                if (roomSelect) {
                    roomSelect.innerHTML = '<option value="">Pilih ruangan</option>';
                }
                setRoomControlsState(disabled, message);
            };

            const fillRooms = (buildingKey, selectedFloor = null, selectedRoomId = null) => {
                if (!roomPanel || !buildingKeyInput || !floorSelect || !roomSelect) {
                    return;
                }

                const floors = campusRooms[buildingKey] || {};
                const floorValues = Object.keys(floors).sort((a, b) => Number(a) - Number(b));

                if (floorValues.length === 0) {
                    resetRoomSelection(true, 'Data lantai dan ruangan belum tersedia untuk lokasi ini.');
                    return;
                }

                setRoomControlsState(false, 'Pilih lantai lalu ruangan yang paling sesuai dengan titik kejadian.');
                buildingKeyInput.value = buildingKey;
                floorSelect.innerHTML = '<option value="">Pilih lantai</option>';
                floorValues.forEach((floor) => {
                    const option = document.createElement('option');
                    option.value = floor;
                    option.textContent = `Lantai ${floor}`;
                    option.selected = String(selectedFloor || '') === String(floor);
                    floorSelect.appendChild(option);
                });

                const renderRoomOptions = () => {
                    const rooms = floors[floorSelect.value] || [];
                    roomSelect.innerHTML = '<option value="">Pilih ruangan</option>';
                    rooms.forEach((room) => {
                        const option = document.createElement('option');
                        option.value = room.id;
                        option.textContent = room.code ? `${room.name} / ${room.code}` : room.name;
                        option.selected = String(selectedRoomId || '') === String(room.id);
                        roomSelect.appendChild(option);
                    });
                };

                floorSelect.onchange = renderRoomOptions;
                if (!floorSelect.value && floorValues.length === 1) {
                    floorSelect.value = floorValues[0];
                }
                renderRoomOptions();
            };

            const updateRoomSelectionFromLocation = (useOldValues = false) => {
                const locationName = selectedLocationName();
                const normalizedLocation = normalize(locationName);

                if (!normalizedLocation) {
                    resetRoomSelection(true, 'Pilih lokasi kejadian terlebih dahulu untuk mengaktifkan pilihan lantai dan ruangan.');
                    return;
                }

                if (normalizedLocation === normalize('Diluar Polman')) {
                    resetRoomSelection(true, 'Lokasi berada di luar Polman, sehingga lantai dan ruangan tidak dapat dipilih.');
                    showSpecificLocation('Diluar Polman', true);
                    return;
                }

                const building = locationBuildingMap.get(normalizedLocation);

                if (!building) {
                    resetRoomSelection(true, 'Data lantai dan ruangan belum tersedia untuk lokasi ini.');
                    showSpecificLocation(locationName, false);
                    return;
                }

                showSpecificLocation(building.name, false);
                fillRooms(building.key, useOldValues ? oldFloor : null, useOldValues ? oldRoomId : null);
            };

            const showSpecificLocation = (detectedName, isOutsidePolman = false) => {
                if (!specificLocationPanel || !specificLocationInput) {
                    return;
                }

                specificLocationPanel.classList.remove('hidden');
                if (isOutsidePolman) {
                    specificLocationInput.required = true;
                    specificLocationInput.placeholder = 'Contoh: depan gerbang, jalan sekitar kampus, atau alamat titik kejadian';
                    if (specificLocationHelp) {
                        specificLocationHelp.textContent = 'Koordinat berada di luar polygon Polman. Tambahkan alamat atau patokan terdekat.';
                    }
                    return;
                }

                specificLocationInput.required = false;
                specificLocationInput.placeholder = `Contoh: lantai/ruang/titik spesifik di ${detectedName}`;
                if (specificLocationHelp) {
                    specificLocationHelp.textContent = `Lokasi utama otomatis terisi ${detectedName}. Patokan boleh diisi singkat; detail final akan diverifikasi Satgas.`;
                }
            };

            const applyDetectedLocation = (lat, lng) => {
                const detected = buildingAt(lat, lng);

                if (detected) {
                    const isSelected = selectLocationByName(detected.name);
                    showSpecificLocation(detected.name, false);
                    fillRooms(detected.key, oldFloor, oldRoomId);

                    status.textContent = isSelected
                        ? `Koordinat masuk area ${detected.name}. Lokasi kejadian terisi otomatis.`
                        : `Koordinat masuk area ${detected.name}, tetapi nama lokasi belum ada di master lokasi.`;
                    return;
                }

                const isOutsideSelected = selectLocationByName('Diluar Polman');
                resetRoomSelection(true, 'Lokasi berada di luar Polman, sehingga lantai dan ruangan tidak dapat dipilih.');
                showSpecificLocation('Diluar Polman', true);
                status.textContent = isOutsideSelected
                    ? 'Koordinat berada di luar polygon Polman. Lokasi kejadian terisi Diluar Polman.'
                    : 'Koordinat berada di luar polygon Polman, tetapi lokasi Diluar Polman belum ada di master lokasi.';
            };

            const captureLocation = () => {
                if (!navigator.geolocation) {
                    status.textContent = 'Browser ini belum mendukung GPS. Koordinat bisa dikosongkan.';
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }

                setButtonState(true);
                status.textContent = 'Meminta izin lokasi dan membaca koordinat GPS...';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        latitudeInput.value = position.coords.latitude.toFixed(7);
                        longitudeInput.value = position.coords.longitude.toFixed(7);
                        accuracyInput.value = Number(position.coords.accuracy || 0).toFixed(2);
                        applyDetectedLocation(position.coords.latitude, position.coords.longitude);
                        status.textContent = `${status.textContent} Akurasi sekitar ${accuracyInput.value} meter.`;
                        setButtonState(false);
                    },
                    () => {
                        status.textContent = 'Koordinat belum terisi. Izinkan akses lokasi lalu tekan Ambil lokasi.';
                        setButtonState(false);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 60000,
                    },
                );
            };

            button.addEventListener('click', captureLocation);
            locationSelect?.addEventListener('change', () => updateRoomSelectionFromLocation(false));

            if (latitudeInput.value.trim() === '' || longitudeInput.value.trim() === '') {
                updateRoomSelectionFromLocation(true);
                captureLocation();
            } else {
                applyDetectedLocation(Number(latitudeInput.value), Number(longitudeInput.value));
            }
        })();

        (() => {
            const form = document.querySelector('[data-report-form="incident"]');
            const injuryPanel = form?.querySelector('[data-incident-injuries]');
            const list = injuryPanel?.querySelector('[data-injury-list]');
            const addButton = injuryPanel?.querySelector('[data-add-injury]');

            if (!injuryPanel || !list || !addButton) {
                return;
            }

            const injuryOptions = @json($injuryCategories->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values());
            const bodyPartOptions = @json($bodyParts->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values());

            const optionMarkup = (options, placeholder) => [
                `<option value="">${placeholder}</option>`,
                ...options.map((option) => `<option value="${option.id}">${option.name}</option>`),
            ].join('');

            const createRow = (index) => {
                const row = document.createElement('div');
                row.className = 'grid gap-3 rounded-2xl bg-white p-3 ring-1 ring-rose-100 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto]';
                row.dataset.injuryRow = '';
                row.innerHTML = `
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Jenis luka</label>
                        <select name="injuries[${index}][injury_category_id]" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                            ${optionMarkup(injuryOptions, 'Pilih jenis luka')}
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Bagian terdampak</label>
                        <select name="injuries[${index}][body_part_id]" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                            ${optionMarkup(bodyPartOptions, 'Pilih bagian tubuh')}
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Keterangan singkat</label>
                        <input name="injuries[${index}][description]" type="text" placeholder="Contoh: bengkak, berdarah, perih" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                    </div>
                    <button type="button" data-remove-injury class="inline-flex h-11 w-11 items-center justify-center self-end rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                `;
                return row;
            };

            const reindexRows = () => {
                Array.from(list.querySelectorAll('[data-injury-row]')).forEach((row, index) => {
                    row.querySelectorAll('[name]').forEach((field) => {
                        field.name = field.name.replace(/injuries\[\d+\]/, `injuries[${index}]`);
                    });
                });
            };

            const updateRemoveButtons = () => {
                const rows = Array.from(list.querySelectorAll('[data-injury-row]'));
                rows.forEach((row) => {
                    const removeButton = row.querySelector('[data-remove-injury]');
                    if (removeButton) {
                        removeButton.disabled = rows.length === 1;
                        removeButton.classList.toggle('opacity-40', rows.length === 1);
                        removeButton.classList.toggle('cursor-not-allowed', rows.length === 1);
                    }
                });
            };

            addButton.addEventListener('click', () => {
                const rows = list.querySelectorAll('[data-injury-row]');

                if (rows.length >= 10) {
                    return;
                }

                list.appendChild(createRow(rows.length));
                updateRemoveButtons();
            });

            list.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-remove-injury]');

                if (!removeButton || list.querySelectorAll('[data-injury-row]').length === 1) {
                    return;
                }

                removeButton.closest('[data-injury-row]')?.remove();
                reindexRows();
                updateRemoveButtons();
            });

            updateRemoveButtons();
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
