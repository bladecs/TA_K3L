<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Hazards\CreatePotentialHazardReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StorePotentialHazardReportRequest;
use App\Models\PotentialHazardReport;
use App\Support\Reports\ReportFormOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PotentialHazardReportController extends Controller
{
    public function __construct(
        protected CreatePotentialHazardReport $createPotentialHazardReport,
        protected ReportFormOptions $reportFormOptions,
    ) {
    }

    public function create(): View
    {
        return view('admin.hazards.create', $this->reportFormOptions->hazard());
    }

    public function store(StorePotentialHazardReportRequest $request): RedirectResponse
    {
        $report = $this->createPotentialHazardReport->handle(
            $request->validated(),
            $request->user()->id,
        );

        return redirect()
            ->route('admin.hazards.create')
            ->with('status', "Hazard {$report->report_number} berhasil dibuat dan masuk ke antrean penanganan.");
    }

    public function index(): View
    {
        $reports = PotentialHazardReport::query()
            ->with(['reporter', 'location', 'reviewer', 'resolver'])
            ->latest('submitted_at')
            ->paginate(10);

        $summaryCounts = collect(['submitted', 'reviewed', 'resolved'])
            ->mapWithKeys(fn (string $status) => [
                $status => PotentialHazardReport::query()->where('status', $status)->count(),
            ])
            ->all();

        return view('admin.hazards.index', compact('reports', 'summaryCounts'));
    }

    public function show(PotentialHazardReport $potentialHazardReport): View
    {
        $potentialHazardReport->load([
            'reporter.role',
            'location',
            'attachments',
            'reviewer',
            'resolver',
        ]);

        return view('admin.hazards.show', [
            'hazardReport' => $potentialHazardReport,
        ]);
    }
}
