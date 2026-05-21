<section class="relative w-full overflow-hidden px-4 pb-20 pt-10 lg:px-8">
    <div class="mx-auto w-full max-w-[1600px]">
        @isset($showInlineFlash)
            @include('partials.flash')
        @endisset

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_380px]">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data"
                data-report-form="hazard"
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
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-outlined">contact_mail</span>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Identitas pelapor</h3>
                                <p class="text-sm text-slate-500">Pembaruan status hazard akan dikirim ke kontak aktif yang Anda isi.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-3">
                            <div>
                                <label for="hazard-reporter-name" class="mb-2 block text-sm font-bold text-slate-700">Nama lengkap</label>
                                <input id="hazard-reporter-name" name="reporter_name" type="text" value="{{ old('reporter_name', auth()->user()?->name) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_name')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hazard-reporter-email" class="mb-2 block text-sm font-bold text-slate-700">Email aktif</label>
                                <input id="hazard-reporter-email" name="reporter_email" type="email" value="{{ old('reporter_email', auth()->user()?->email) }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_email')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hazard-reporter-whatsapp" class="mb-2 block text-sm font-bold text-slate-700">No. WhatsApp aktif</label>
                                <input id="hazard-reporter-whatsapp" name="reporter_whatsapp" type="text" value="{{ old('reporter_whatsapp', auth()->user()?->phone) }}" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                @error('reporter_whatsapp')
                                    <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </section>

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
                                            <option value="{{ $location->id }}" data-location-name="{{ $location->name }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4" data-hazard-gps>
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">Koordinat GPS hazard</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500" data-hazard-gps-status>
                                                GPS wajib berada di area Polman.
                                            </p>
                                        </div>
                                        <button type="button" data-hazard-gps-button
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                            <span class="material-symbols-outlined text-base" data-hazard-gps-icon>my_location</span>
                                            <span data-hazard-gps-label>Ambil lokasi</span>
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                        <div>
                                            <label for="hazard-latitude" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Latitude</label>
                                            <input id="hazard-latitude" name="latitude" type="text" value="{{ old('latitude') }}" required readonly
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                            @error('latitude')
                                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="hazard-longitude" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Longitude</label>
                                            <input id="hazard-longitude" name="longitude" type="text" value="{{ old('longitude') }}" required readonly
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                            @error('longitude')
                                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="hazard-location-accuracy" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Akurasi meter</label>
                                            <input id="hazard-location-accuracy" name="location_accuracy" type="text" value="{{ old('location_accuracy') }}" readonly
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                            @error('location_accuracy')
                                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hidden" data-hazard-room-panel>
                                    <input type="hidden" name="building_key" id="hazard-building-key" value="{{ old('building_key') }}">
                                    <p class="text-sm font-bold text-slate-800">Gedung, lantai, dan ruangan</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Terisi saat GPS masuk polygon gedung. Pilih lantai lalu ruangan bila data master sudah tersedia.</p>
                                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="hazard-building-floor" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Lantai</label>
                                            <select id="hazard-building-floor" name="building_floor"
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                                <option value="">Pilih lantai</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="hazard-campus-room-id" class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Ruangan</label>
                                            <select id="hazard-campus-room-id" name="campus_room_id"
                                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none">
                                                <option value="">Pilih ruangan</option>
                                            </select>
                                        </div>
                                    </div>
                                    @error('campus_room_id')
                                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hazard-specific-location" class="mb-2 block text-sm font-bold text-slate-700">Detail lokasi hazard</label>
                                    <input id="hazard-specific-location" name="specific_location" type="text" value="{{ old('specific_location') }}" required
                                        placeholder="Contoh: lantai 2 dekat panel utama, sisi utara mesin CNC"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[var(--primary-color)] focus:bg-white focus:ring-4 focus:ring-[var(--primary-color)]/10">
                                    <p class="mt-2 text-xs leading-5 text-slate-500">Isi patokan spesifik agar Satgas mudah menemukan titik bahaya.</p>
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
                        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                    <span class="material-symbols-outlined">edit_note</span>
                                </span>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">Catatan tambahan</h3>
                                    <p class="text-sm text-slate-500">Sertakan kondisi aktual, risiko terdekat, atau saran pengamanan sementara.</p>
                                </div>
                            </div>
                            <div>
                                <button type="button" data-voice-target="hazard-notes"
                                    class="inline-flex items-center justify-center gap-2 rounded-full border border-[var(--primary-color)]/15 bg-white px-4 py-2 text-xs font-bold text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)]">
                                    <span class="material-symbols-outlined text-base" data-voice-icon>mic</span>
                                    <span data-voice-label>Voice to Text</span>
                                </button>
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

        (() => {
            const form = document.querySelector('[data-report-form="hazard"]');
            const gpsPanel = form?.querySelector('[data-hazard-gps]');
            const button = gpsPanel?.querySelector('[data-hazard-gps-button]');
            const icon = gpsPanel?.querySelector('[data-hazard-gps-icon]');
            const label = gpsPanel?.querySelector('[data-hazard-gps-label]');
            const status = gpsPanel?.querySelector('[data-hazard-gps-status]');
            const latitudeInput = gpsPanel?.querySelector('#hazard-latitude');
            const longitudeInput = gpsPanel?.querySelector('#hazard-longitude');
            const accuracyInput = gpsPanel?.querySelector('#hazard-location-accuracy');
            const locationSelect = form?.querySelector('#hazard-location');
            const roomPanel = form?.querySelector('[data-hazard-room-panel]');
            const buildingKeyInput = form?.querySelector('#hazard-building-key');
            const floorSelect = form?.querySelector('#hazard-building-floor');
            const roomSelect = form?.querySelector('#hazard-campus-room-id');
            const campusBuildings = @json($campusBuildingPolygons ?? []);
            const campusBoundary = @json($campusBoundaryPolygon ?? []);
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
                    const [latI, lngI] = polygon[i];
                    const [latJ, lngJ] = polygon[j];
                    const intersects = ((lngI > lng) !== (lngJ > lng))
                        && (lat < ((latJ - latI) * (lng - lngI)) / (lngJ - lngI) + latI);

                    if (intersects) {
                        inside = !inside;
                    }
                }

                return inside;
            };

            const buildingAt = (lat, lng) => campusBuildings.find((building) => (
                Array.isArray(building.coordinates) && isPointInsidePolygon([lat, lng], building.coordinates)
            ));

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

            const resetRoomSelection = () => {
                if (roomPanel) {
                    roomPanel.classList.add('hidden');
                }
                if (buildingKeyInput) {
                    buildingKeyInput.value = '';
                }
                if (floorSelect) {
                    floorSelect.innerHTML = '<option value="">Pilih lantai</option>';
                }
                if (roomSelect) {
                    roomSelect.innerHTML = '<option value="">Pilih ruangan</option>';
                }
            };

            const fillRooms = (buildingKey, selectedFloor = null, selectedRoomId = null) => {
                if (!roomPanel || !buildingKeyInput || !floorSelect || !roomSelect) {
                    return;
                }

                const floors = campusRooms[buildingKey] || {};
                const floorValues = Object.keys(floors).sort((a, b) => Number(a) - Number(b));
                roomPanel.classList.remove('hidden');
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

            const clearCoordinates = (message) => {
                latitudeInput.value = '';
                longitudeInput.value = '';
                accuracyInput.value = '';
                latitudeInput.setCustomValidity(message);
                longitudeInput.setCustomValidity(message);
                status.textContent = message;
            };

            const applyDetectedLocation = (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const isInsidePolman = isPointInsidePolygon([lat, lng], campusBoundary);

                if (!isInsidePolman) {
                    resetRoomSelection();
                    clearCoordinates('Koordinat hazard berada di luar area Polman. Ambil GPS dari area kampus.');
                    return;
                }

                latitudeInput.setCustomValidity('');
                longitudeInput.setCustomValidity('');
                latitudeInput.value = lat.toFixed(7);
                longitudeInput.value = lng.toFixed(7);
                accuracyInput.value = Number(position.coords.accuracy || 0).toFixed(2);

                const detected = buildingAt(lat, lng);
                if (detected && selectLocationByName(detected.name)) {
                    fillRooms(detected.key, oldFloor, oldRoomId);
                    status.textContent = `Koordinat masuk area Polman dan terdeteksi di ${detected.name}.`;
                    return;
                }

                resetRoomSelection();
                status.textContent = 'Koordinat masuk area Polman. Pilih lokasi utama yang paling dekat, lalu isi detail lokasi hazard.';
            };

            const captureLocation = () => {
                if (!navigator.geolocation) {
                    clearCoordinates('Browser ini belum mendukung GPS. Gunakan browser lain untuk mengirim hazard.');
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }

                setButtonState(true);
                status.textContent = 'Meminta izin lokasi dan membaca koordinat GPS...';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        applyDetectedLocation(position);
                        setButtonState(false);
                    },
                    () => {
                        clearCoordinates('Koordinat belum terisi. Izinkan akses lokasi lalu tekan Ambil lokasi.');
                        setButtonState(false);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 30000,
                    },
                );
            };

            button.addEventListener('click', captureLocation);
            form?.addEventListener('submit', (event) => {
                if (!latitudeInput.value || !longitudeInput.value) {
                    event.preventDefault();
                    clearCoordinates('Koordinat GPS hazard wajib diambil dan harus berada di area Polman.');
                    latitudeInput.reportValidity();
                }
            });

            if (latitudeInput.value.trim() === '' || longitudeInput.value.trim() === '') {
                captureLocation();
            }
        })();

        (() => {
            const form = document.querySelector('[data-report-form="hazard"]');
            const button = form?.querySelector('[data-voice-target="hazard-notes"]');
            const textarea = form?.querySelector('#hazard-notes');
            const icon = button?.querySelector('[data-voice-icon]');
            const label = button?.querySelector('[data-voice-label]');
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!button || !textarea || !SpeechRecognition) {
                if (button) {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.title = 'Voice to text belum didukung browser ini';
                }
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.interimResults = false;
            recognition.continuous = false;

            let isListening = false;

            recognition.addEventListener('start', () => {
                isListening = true;
                textarea.focus();
                button.classList.remove('border-[var(--primary-color)]/15', 'bg-white', 'text-[var(--primary-color)]', 'hover:bg-[var(--blue-low-opacity)]');
                button.classList.add('border-rose-600', 'bg-rose-600', 'text-white', 'hover:bg-rose-700');
                if (icon) {
                    icon.textContent = 'stop_circle';
                }
                if (label) {
                    label.textContent = 'Stop';
                }
            });

            recognition.addEventListener('end', () => {
                isListening = false;
                button.classList.remove('border-rose-600', 'bg-rose-600', 'text-white', 'hover:bg-rose-700');
                button.classList.add('border-[var(--primary-color)]/15', 'bg-white', 'text-[var(--primary-color)]', 'hover:bg-[var(--blue-low-opacity)]');
                if (icon) {
                    icon.textContent = 'mic';
                }
                if (label) {
                    label.textContent = 'Voice to Text';
                }
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

                textarea.focus();
                recognition.start();
            });
        })();
    </script>
@endpush
