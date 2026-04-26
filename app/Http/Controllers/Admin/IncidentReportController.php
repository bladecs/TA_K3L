<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Incidents\CreateIncidentReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\StoreIncidentReportRequest;
use App\Models\IncidentReport;
use App\Support\Reports\ReportFormOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IncidentReportController extends Controller
{
    public function __construct(
        protected CreateIncidentReport $createIncidentReport,
        protected ReportFormOptions $reportFormOptions,
    ) {
    }

    public function create(): View
    {
        $this->authorize('create', IncidentReport::class);

        return view('admin.incidents.create', $this->reportFormOptions->incident());
    }

    public function store(StoreIncidentReportRequest $request): RedirectResponse
    {
        $this->authorize('create', IncidentReport::class);

        $report = $this->createIncidentReport->handle(
            $request->safe()->except('victim_type'),
            $request->user()->id,
        );

        return redirect()
            ->route('admin.incidents.create')
            ->with('status', "Laporan {$report->report_number} berhasil dibuat dan masuk ke antrean verifikasi.");
    }
}
