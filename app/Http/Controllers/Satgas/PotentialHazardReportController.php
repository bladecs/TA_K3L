<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Hazards\CreatePotentialHazardReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StorePotentialHazardReportRequest;
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
        return view('satgas.hazards.create', $this->reportFormOptions->hazard());
    }

    public function store(StorePotentialHazardReportRequest $request): RedirectResponse
    {
        $report = $this->createPotentialHazardReport->handle(
            $request->validated(),
            $request->user()->id,
        );

        return redirect()
            ->route('satgas.hazards.show', $report)
            ->with('status', "Hazard {$report->report_number} berhasil dibuat dan siap Anda tindak lanjuti.");
    }
}
