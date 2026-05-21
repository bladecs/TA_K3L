@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="building_key" class="mb-2 block text-sm font-semibold text-slate-700">Gedung polygon</label>
        <select id="building_key" name="building_key" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
            @foreach ($buildings as $building)
                <option value="{{ $building['key'] }}" @selected(old('building_key', $room->building_key) === $building['key'])>{{ $building['name'] }}</option>
            @endforeach
        </select>
        @error('building_key') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="floor" class="mb-2 block text-sm font-semibold text-slate-700">Lantai</label>
        <input id="floor" name="floor" type="number" min="1" max="99" value="{{ old('floor', $room->floor) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        @error('floor') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama ruangan</label>
        <input id="name" name="name" type="text" value="{{ old('name', $room->name) }}" placeholder="Contoh: B201 / Lab CNC / Ruang Dosen" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="mb-2 block text-sm font-semibold text-slate-700">Kode opsional</label>
        <input id="code" name="code" type="text" value="{{ old('code', $room->code) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        @error('code') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-700">Urutan</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $room->sort_order ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-[var(--primary-color)]">
        @error('sort_order') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3">
        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-[var(--primary-color)]" @checked(old('is_active', $room->is_active ?? true))>
        <span class="text-sm font-semibold text-slate-700">Aktif</span>
    </label>
</div>

<div class="mt-7 flex gap-3">
    <button type="submit" class="rounded-full bg-[var(--primary-color)] px-6 py-3 text-sm font-semibold text-white">{{ $submitLabel }}</button>
    <a href="{{ route('admin.campus-rooms.index') }}" class="rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
</div>
