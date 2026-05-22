<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusRoom;
use App\Models\Floorplan;
use App\Models\Location;
use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FloorplanController extends Controller
{
    public function index(): View
    {
        $floorplans = Floorplan::query()
            ->withCount('rooms')
            ->orderBy('building_name')
            ->orderBy('floor')
            ->orderByDesc('version')
            ->paginate(12);

        return view('admin.floorplans.index', compact('floorplans'));
    }

    public function create(): View
    {
        return view('admin.floorplans.create', [
            'floorplan' => new Floorplan([
                'floor' => 1,
                'version' => 1,
                'canvas_width' => 900,
                'canvas_height' => 520,
                'is_active' => true,
            ]),
            'roomMappings' => collect(),
            'locations' => $this->locations(),
            'buildings' => $this->buildings(),
            'campusRooms' => $this->campusRooms(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validated($request);

        DB::transaction(function () use ($payload) {
            $floorplan = Floorplan::query()->create([
                ...Arr::except($payload['floorplan'], ['rooms']),
                'source_type' => 'generated-html',
                'created_by' => auth()->id(),
            ]);

            $this->syncRooms($floorplan, $payload['rooms']);
            $floorplan->update(['svg_markup' => $this->generateSvg($floorplan->fresh('rooms.campusRoom'))]);
        });

        return redirect()->route('admin.floorplans.index')->with('status', 'Denah berhasil dibuat dan disimpan ke database.');
    }

    public function show(Floorplan $floorplan): View
    {
        $floorplan->load(['rooms.campusRoom', 'location']);

        return view('admin.floorplans.show', compact('floorplan'));
    }

    public function edit(Floorplan $floorplan): View
    {
        $floorplan->load('rooms.campusRoom');

        return view('admin.floorplans.edit', [
            'floorplan' => $floorplan,
            'roomMappings' => $floorplan->rooms,
            'locations' => $this->locations(),
            'buildings' => $this->buildings(),
            'campusRooms' => $this->campusRooms(),
        ]);
    }

    public function update(Request $request, Floorplan $floorplan): RedirectResponse
    {
        $payload = $this->validated($request, $floorplan);

        DB::transaction(function () use ($floorplan, $payload) {
            $floorplan->update([
                ...Arr::except($payload['floorplan'], ['rooms']),
                'source_type' => 'generated-html',
            ]);

            $this->syncRooms($floorplan, $payload['rooms']);
            $floorplan->update(['svg_markup' => $this->generateSvg($floorplan->fresh('rooms.campusRoom'))]);
        });

        return redirect()->route('admin.floorplans.show', $floorplan)->with('status', 'Denah berhasil diperbarui.');
    }

    public function destroy(Floorplan $floorplan): RedirectResponse
    {
        $floorplan->delete();

        return redirect()->route('admin.floorplans.index')->with('status', 'Denah berhasil dihapus.');
    }

    protected function validated(Request $request, ?Floorplan $floorplan = null): array
    {
        $buildingKeys = collect($this->buildings())->pluck('key')->all();

        $data = $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
            'building_key' => ['required', Rule::in($buildingKeys)],
            'floor' => ['required', 'integer', 'min:1', 'max:99'],
            'name' => ['required', 'string', 'max:150'],
            'version' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                Rule::unique('floorplans')
                    ->where('building_key', $request->input('building_key'))
                    ->where('floor', $request->integer('floor'))
                    ->ignore($floorplan?->id),
            ],
            'canvas_width' => ['required', 'integer', 'min:100', 'max:5000'],
            'canvas_height' => ['required', 'integer', 'min:100', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.campus_room_id' => ['required', 'exists:campus_rooms,id'],
            'rooms.*.shape_type' => ['required', Rule::in(['polygon', 'rect'])],
            'rooms.*.coordinates' => ['required', 'string'],
            'rooms.*.label' => ['nullable', 'string', 'max:150'],
            'rooms.*.default_fill_color' => ['nullable', 'string', 'max:20'],
            'rooms.*.incident_fill_color' => ['nullable', 'string', 'max:20'],
            'rooms.*.hazard_fill_color' => ['nullable', 'string', 'max:20'],
            'rooms.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $building = collect($this->buildings())->firstWhere('key', $data['building_key']);
        $rooms = collect($data['rooms'])
            ->map(function (array $room, int $index) {
                $geometry = $this->parseGeometry($room['coordinates'], $room['shape_type']);
                $campusRoom = CampusRoom::query()->findOrFail($room['campus_room_id']);

                return [
                    'campus_room_id' => $campusRoom->id,
                    'shape_key' => Str::slug($campusRoom->code ?: $campusRoom->name) . '-' . ($index + 1),
                    'shape_type' => $room['shape_type'],
                    'geometry' => $geometry,
                    'label' => $room['label'] ?? $campusRoom->name,
                    'default_fill_color' => $room['default_fill_color'] ?? '#e5e7eb',
                    'incident_fill_color' => $room['incident_fill_color'] ?? '#ef4444',
                    'hazard_fill_color' => $room['hazard_fill_color'] ?? '#f59e0b',
                    'is_active' => true,
                    'sort_order' => $room['sort_order'] ?? $index,
                ];
            })
            ->values()
            ->all();

        return [
            'floorplan' => [
                'location_id' => $data['location_id'] ?? null,
                'building_key' => $data['building_key'],
                'building_name' => $building['name'] ?? $data['building_key'],
                'floor' => $data['floor'],
                'name' => $data['name'],
                'version' => $data['version'],
                'canvas_width' => $data['canvas_width'],
                'canvas_height' => $data['canvas_height'],
                'metadata' => [
                    'coordinate_unit' => 'pixel',
                    'generated_by' => 'admin-floorplan-editor',
                ],
                'is_active' => $request->boolean('is_active'),
            ],
            'rooms' => $rooms,
        ];
    }

    protected function parseGeometry(string $raw, string $shapeType): array
    {
        $geometry = json_decode($raw, true);

        if (! is_array($geometry)) {
            throw ValidationException::withMessages(['rooms' => 'Koordinat harus berupa JSON valid.']);
        }

        if ($shapeType === 'rect') {
            foreach (['x', 'y', 'width', 'height'] as $key) {
                if (! is_numeric($geometry[$key] ?? null)) {
                    throw ValidationException::withMessages(['rooms' => 'Koordinat rect harus berisi x, y, width, dan height.']);
                }
            }

            return Arr::only($geometry, ['x', 'y', 'width', 'height']);
        }

        $points = collect($geometry)
            ->map(function ($point) {
                if (is_array($point) && is_numeric($point[0] ?? null) && is_numeric($point[1] ?? null)) {
                    return [(float) $point[0], (float) $point[1]];
                }

                if (is_array($point) && is_numeric($point['x'] ?? null) && is_numeric($point['y'] ?? null)) {
                    return [(float) $point['x'], (float) $point['y']];
                }

                throw ValidationException::withMessages(['rooms' => 'Koordinat polygon harus berupa pasangan x/y.']);
            })
            ->values()
            ->all();

        if (count($points) < 3) {
            throw ValidationException::withMessages(['rooms' => 'Polygon minimal membutuhkan 3 titik koordinat.']);
        }

        return $points;
    }

    protected function syncRooms(Floorplan $floorplan, array $rooms): void
    {
        $floorplan->rooms()->delete();

        foreach ($rooms as $room) {
            $floorplan->rooms()->create($room);
        }
    }

    protected function generateSvg(Floorplan $floorplan): string
    {
        $width = $floorplan->canvas_width ?: 900;
        $height = $floorplan->canvas_height ?: 520;
        $elements = $floorplan->rooms
            ->sortBy('sort_order')
            ->map(function ($room) {
                $labelText = $this->compactLabel($room->label ?: $room->campusRoom?->name ?: 'Ruangan');
                $label = e($labelText);
                $fill = e($room->default_fill_color);
                $stroke = '#334155';
                $shapeKey = e($room->shape_key);
                $labelColor = '#1e293b';

                if ($room->shape_type === 'rect') {
                    $g = $room->geometry;
                    $x = (float) $g['x'];
                    $y = (float) $g['y'];
                    $w = (float) $g['width'];
                    $h = (float) $g['height'];
                    $textX = $x + ($w / 2);
                    $textY = $y + ($h / 2);
                    $fontSize = $this->labelFontSize($w, $h, $labelText);
                    $text = $this->svgLabel($labelText, $textX, $textY, $fontSize, $w, $h, $labelColor);

                    return "<g data-room-id=\"{$room->campus_room_id}\" data-shape-key=\"{$shapeKey}\"><rect x=\"{$x}\" y=\"{$y}\" width=\"{$w}\" height=\"{$h}\" rx=\"3\" fill=\"{$fill}\" stroke=\"{$stroke}\" stroke-width=\"1\"/>{$text}</g>";
                }

                $points = collect($room->geometry)
                    ->map(fn ($point) => ((float) $point[0]) . ',' . ((float) $point[1]))
                    ->implode(' ');
                $center = $this->polygonCenter($room->geometry);
                $fontSize = 9;

                return "<g data-room-id=\"{$room->campus_room_id}\" data-shape-key=\"{$shapeKey}\"><polygon points=\"{$points}\" fill=\"{$fill}\" stroke=\"{$stroke}\" stroke-width=\"1\"/><text x=\"{$center[0]}\" y=\"{$center[1]}\" text-anchor=\"middle\" dominant-baseline=\"middle\" font-size=\"{$fontSize}\" font-weight=\"500\" fill=\"{$labelColor}\">{$label}</text></g>";
            })
            ->implode('');

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$width} {$height}\" role=\"img\" aria-label=\"Denah {$floorplan->name}\" class=\"h-auto w-full\"><rect width=\"{$width}\" height=\"{$height}\" fill=\"#f8fafc\"/>{$elements}</svg>";
    }

    protected function compactLabel(string $label): string
    {
        $label = trim($label);

        return match ($label) {
            'Tangga Turun' => 'Tangga',
            'Tangga Darurat' => 'T. Darurat',
            'Tg. Ke LT4' => 'Ke LT4',
            'Kantin Teori' => 'Kantin',
            'Ruang Dosen' => 'Dosen',
            default => Str::limit($label, 14, ''),
        };
    }

    protected function labelFontSize(float $width, float $height, string $label): int
    {
        if ($width < 45 || $height < 38) {
            return 7;
        }

        if ($width < 70 || $height < 55 || mb_strlen($label) > 8) {
            return 8;
        }

        return 9;
    }

    protected function svgLabel(string $label, float $x, float $y, int $fontSize, float $width, float $height, string $color): string
    {
        $lines = $this->wrapLabel($label, $fontSize, $width, $height);

        if ($lines === []) {
            return '';
        }

        $lineHeight = $fontSize + 2;
        $startY = $y - (($lineHeight * (count($lines) - 1)) / 2);
        $tspans = collect($lines)
            ->map(function (string $line, int $index) use ($x, $startY, $lineHeight) {
                $dy = $index === 0 ? 0 : $lineHeight;

                return '<tspan x="' . $x . '" y="' . ($startY + $dy) . '">' . e($line) . '</tspan>';
            })
            ->implode('');

        return "<text text-anchor=\"middle\" dominant-baseline=\"middle\" font-size=\"{$fontSize}\" font-weight=\"500\" fill=\"{$color}\">{$tspans}</text>";
    }

    protected function wrapLabel(string $label, int $fontSize, float $width, float $height): array
    {
        $maxLines = $height >= 32 ? 2 : 1;
        $maxChars = max(3, (int) floor(($width - 8) / ($fontSize * 0.62)));

        if ($height < ($fontSize + 6) || $width < 24) {
            return [];
        }

        if (mb_strlen($label) <= $maxChars) {
            return [$label];
        }

        if ($maxLines === 1) {
            return [Str::limit($label, $maxChars, '')];
        }

        $words = preg_split('/\s+/', $label) ?: [];
        $lines = [''];

        foreach ($words as $word) {
            $current = $lines[count($lines) - 1];
            $candidate = trim($current . ' ' . $word);

            if (mb_strlen($candidate) <= $maxChars) {
                $lines[count($lines) - 1] = $candidate;
                continue;
            }

            if (count($lines) < $maxLines) {
                $lines[] = Str::limit($word, $maxChars, '');
                continue;
            }

            $lines[count($lines) - 1] = Str::limit($lines[count($lines) - 1], $maxChars, '');
            break;
        }

        return array_values(array_filter($lines, fn (string $line) => $line !== ''));
    }

    protected function polygonCenter(array $points): array
    {
        $count = max(count($points), 1);
        $x = collect($points)->sum(fn ($point) => (float) $point[0]) / $count;
        $y = collect($points)->sum(fn ($point) => (float) $point[1]) / $count;

        return [round($x, 2), round($y, 2)];
    }

    protected function locations()
    {
        return Location::query()->where('is_active', true)->orderBy('name')->get();
    }

    protected function campusRooms()
    {
        return CampusRoom::query()
            ->where('is_active', true)
            ->orderBy('building_name')
            ->orderBy('floor')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    protected function buildings(): array
    {
        return app(PublicHazardMapData::class)->campusBuildingPolygons();
    }
}
