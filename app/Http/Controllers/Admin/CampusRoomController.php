<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusRoom;
use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CampusRoomController extends Controller
{
    public function index(): View
    {
        $rooms = CampusRoom::query()
            ->orderBy('building_name')
            ->orderBy('floor')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.campus-rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('admin.campus-rooms.create', [
            'room' => new CampusRoom(['is_active' => true]),
            'buildings' => $this->buildings(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        CampusRoom::query()->create($this->validated($request));

        return redirect()->route('admin.campus-rooms.index')->with('status', 'Ruangan gedung berhasil ditambahkan.');
    }

    public function edit(CampusRoom $campusRoom): View
    {
        return view('admin.campus-rooms.edit', [
            'room' => $campusRoom,
            'buildings' => $this->buildings(),
        ]);
    }

    public function update(Request $request, CampusRoom $campusRoom): RedirectResponse
    {
        $campusRoom->update($this->validated($request));

        return redirect()->route('admin.campus-rooms.index')->with('status', 'Ruangan gedung berhasil diperbarui.');
    }

    public function destroy(CampusRoom $campusRoom): RedirectResponse
    {
        if ($campusRoom->is_active) {
            $campusRoom->update(['is_active' => false]);

            return redirect()->route('admin.campus-rooms.index')->with('status', 'Ruangan dinonaktifkan.');
        }

        $campusRoom->delete();

        return redirect()->route('admin.campus-rooms.index')->with('status', 'Ruangan berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $buildingKeys = collect($this->buildings())->pluck('key')->all();
        $data = $request->validate([
            'building_key' => ['required', Rule::in($buildingKeys)],
            'floor' => ['required', 'integer', 'min:1', 'max:99'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $building = collect($this->buildings())->firstWhere('key', $data['building_key']);

        return [
            ...$data,
            'building_name' => $building['name'] ?? $data['building_key'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function buildings(): array
    {
        return app(PublicHazardMapData::class)->campusBuildingPolygons();
    }
}
