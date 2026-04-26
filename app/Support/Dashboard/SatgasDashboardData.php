<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;
use App\Models\PotentialHazardReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SatgasDashboardData
{
    public function build(string $period = '180'): array
    {
        $days = $this->resolvePeriodDays($period);
        $startDate = $days ? Carbon::now()->subDays($days)->startOfDay() : null;

        $priorityReports = $this->incidentBaseQuery($startDate)
            ->with(['reporter.role', 'location', 'category'])
            ->whereIn('status', ['submitted', 'verified', 'investigating', 'resolved'])
            ->orderByRaw("
                CASE severity_level
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->take(6)
            ->get();

        $incidentStatusSummary = [
            'completed' => $this->incidentBaseQuery($startDate)->where('status', 'closed')->count(),
            'in_progress' => $this->incidentBaseQuery($startDate)->whereIn('status', ['verified', 'investigating', 'resolved'])->count(),
            'pending' => $this->incidentBaseQuery($startDate)->where('status', 'submitted')->count(),
        ];

        $hazardStatusSummary = [
            'completed' => $this->hazardBaseQuery($startDate)->where('status', 'resolved')->count(),
            'in_progress' => $this->hazardBaseQuery($startDate)->where('status', 'reviewed')->count(),
            'pending' => $this->hazardBaseQuery($startDate)->where('status', 'submitted')->count(),
        ];

        $severityBreakdown = collect(['low', 'medium', 'high', 'critical'])
            ->map(fn (string $severity) => [
                'key' => $severity,
                'label' => strtoupper($severity),
                'count' => $this->incidentBaseQuery($startDate)->where('severity_level', $severity)->count(),
            ]);

        $hazardTypeBreakdown = collect([
            'lingkungan' => 'Lingkungan',
            'peralatan' => 'Peralatan',
            'listrik' => 'Listrik',
            'zat-kimia' => 'Zat Kimia',
        ])->map(fn (string $label, string $key) => [
            'key' => $key,
            'label' => $label,
            'count' => $this->hazardBaseQuery($startDate)->where('hazard_type', $key)->count(),
        ]);

        $locationInsights = $this->incidentBaseQuery($startDate)
            ->selectRaw('locations.name as location_name, count(*) as total_reports')
            ->join('locations', 'locations.id', '=', 'incident_reports.location_id')
            ->groupBy('locations.name')
            ->orderByDesc('total_reports')
            ->take(5)
            ->get();

        $monthlyTrend = $this->buildMonthlyTrend($startDate);

        $sourceBreakdown = [
            'incidents' => [
                'user' => $this->incidentBaseQuery($startDate)
                    ->whereHas('reporter.role', fn (Builder $query) => $query->where('code', 'mahasiswa'))
                    ->count(),
                'internal' => $this->incidentBaseQuery($startDate)
                    ->whereHas('reporter.role', fn (Builder $query) => $query->whereIn('code', ['satgas', 'admin']))
                    ->count(),
            ],
            'hazards' => [
                'user' => $this->hazardBaseQuery($startDate)
                    ->whereHas('reporter.role', fn (Builder $query) => $query->where('code', 'mahasiswa'))
                    ->count(),
                'internal' => $this->hazardBaseQuery($startDate)
                    ->whereHas('reporter.role', fn (Builder $query) => $query->whereIn('code', ['satgas', 'admin']))
                    ->count(),
            ],
        ];

        $stats = [
            'submitted_incidents' => $this->incidentBaseQuery($startDate)->where('status', 'submitted')->count(),
            'verified_incidents' => $this->incidentBaseQuery($startDate)->where('status', 'verified')->count(),
            'investigating_incidents' => $this->incidentBaseQuery($startDate)->where('status', 'investigating')->count(),
            'resolved_incidents' => $this->incidentBaseQuery($startDate)->where('status', 'resolved')->count(),
            'closed_incidents' => $this->incidentBaseQuery($startDate)->where('status', 'closed')->count(),
            'critical_incidents' => $this->incidentBaseQuery($startDate)->where('severity_level', 'critical')->count(),
            'total_hazards' => $this->hazardBaseQuery($startDate)->count(),
        ];

        $recommendations = $this->buildRecommendations(
            $incidentStatusSummary,
            $hazardStatusSummary,
            $severityBreakdown,
            $hazardTypeBreakdown,
            $locationInsights,
            $sourceBreakdown
        );

        return [
            'period' => $period,
            'periodLabel' => $this->periodLabel($period),
            'stats' => $stats,
            'priorityReports' => $priorityReports,
            'incidentStatusSummary' => $incidentStatusSummary,
            'hazardStatusSummary' => $hazardStatusSummary,
            'severityBreakdown' => $severityBreakdown,
            'hazardTypeBreakdown' => $hazardTypeBreakdown,
            'monthlyTrend' => $monthlyTrend,
            'locationInsights' => $locationInsights,
            'sourceBreakdown' => $sourceBreakdown,
            'recommendations' => $recommendations,
            'workloadSummary' => [
                'needs_review' => $priorityReports->where('status', 'submitted')->count(),
                'needs_field_follow_up' => $priorityReports->whereIn('status', ['investigating', 'resolved'])->count(),
                'ready_to_close' => $this->incidentBaseQuery($startDate)->where('status', 'resolved')->count(),
            ],
        ];
    }

    protected function incidentBaseQuery(?Carbon $startDate): Builder
    {
        return IncidentReport::query()
            ->when($startDate, fn (Builder $query) => $query->where('submitted_at', '>=', $startDate));
    }

    protected function hazardBaseQuery(?Carbon $startDate): Builder
    {
        return PotentialHazardReport::query()
            ->when($startDate, fn (Builder $query) => $query->where('submitted_at', '>=', $startDate));
    }

    protected function resolvePeriodDays(string $period): ?int
    {
        return match ($period) {
            '30' => 30,
            '90' => 90,
            '180' => 180,
            '365' => 365,
            'all' => null,
            default => 180,
        };
    }

    protected function periodLabel(string $period): string
    {
        return match ($period) {
            '30' => '30 hari terakhir',
            '90' => '90 hari terakhir',
            '180' => '6 bulan terakhir',
            '365' => '1 tahun terakhir',
            'all' => 'Semua periode',
            default => '6 bulan terakhir',
        };
    }

    protected function buildMonthlyTrend(?Carbon $startDate): Collection
    {
        $rangeStart = $startDate
            ? (clone $startDate)->startOfMonth()
            : Carbon::now()->startOfMonth()->subMonths(5);
        $rangeEnd = Carbon::now()->startOfMonth();

        $months = collect();
        $cursor = (clone $rangeStart);

        while ($cursor <= $rangeEnd) {
            $start = (clone $cursor)->startOfMonth();
            $end = (clone $cursor)->endOfMonth();

            $months->push([
                'label' => $start->translatedFormat('M Y'),
                'incidents' => $this->incidentBaseQuery($startDate)
                    ->whereBetween('submitted_at', [$start, $end])
                    ->count(),
                'hazards' => $this->hazardBaseQuery($startDate)
                    ->whereBetween('submitted_at', [$start, $end])
                    ->count(),
            ]);

            $cursor->addMonth();
        }

        return $months->take(-6)->values();
    }

    protected function buildRecommendations(
        array $incidentStatusSummary,
        array $hazardStatusSummary,
        Collection $severityBreakdown,
        Collection $hazardTypeBreakdown,
        Collection $locationInsights,
        array $sourceBreakdown
    ): Collection {
        $recommendations = collect();

        $topLocation = $locationInsights->sortByDesc('total_reports')->first();
        if ($topLocation && $topLocation->total_reports > 0) {
            $recommendations->push([
                'title' => 'Prioritaskan inspeksi di lokasi dominan',
                'description' => "Area {$topLocation->location_name} mencatat {$topLocation->total_reports} temuan. Jadwalkan inspeksi rutin dan evaluasi SOP lokal di area ini.",
                'icon' => 'place',
                'tone' => 'text-[var(--primary-color)] bg-[var(--blue-low-opacity)]',
            ]);
        }

        $criticalSeverity = $severityBreakdown->firstWhere('key', 'critical');
        if (($criticalSeverity['count'] ?? 0) > 0) {
            $recommendations->push([
                'title' => 'Perkuat mitigasi insiden kritis',
                'description' => "Ada {$criticalSeverity['count']} insiden kritis pada periode ini. Tinjau kembali prosedur eskalasi, APD, dan jalur pelaporan darurat.",
                'icon' => 'crisis_alert',
                'tone' => 'text-rose-700 bg-rose-100',
            ]);
        }

        $topHazard = $hazardTypeBreakdown->sortByDesc('count')->first();
        if (($topHazard['count'] ?? 0) > 0) {
            $recommendations->push([
                'title' => 'Fokuskan edukasi pada hazard terbanyak',
                'description' => "Potensi bahaya {$topHazard['label']} paling sering muncul. Materi sosialisasi dan checklist inspeksi sebaiknya diprioritaskan di area ini.",
                'icon' => 'menu_book',
                'tone' => 'text-amber-700 bg-amber-100',
            ]);
        }

        $pendingTotal = $incidentStatusSummary['pending'] + $hazardStatusSummary['pending'];
        if ($pendingTotal > 0) {
            $recommendations->push([
                'title' => 'Kurangi antrean laporan baru',
                'description' => "Masih ada {$pendingTotal} laporan yang belum ditangani. Pembagian reviewer awal atau jadwal validasi harian bisa mempercepat respons.",
                'icon' => 'pending_actions',
                'tone' => 'text-sky-700 bg-sky-100',
            ]);
        }

        $userReports = $sourceBreakdown['incidents']['user'] + $sourceBreakdown['hazards']['user'];
        $internalReports = $sourceBreakdown['incidents']['internal'] + $sourceBreakdown['hazards']['internal'];

        if ($userReports > $internalReports) {
            $recommendations->push([
                'title' => 'Perkuat tindak lanjut dari laporan pengguna',
                'description' => 'Mayoritas temuan berasal dari pengguna. Satgas bisa menyiapkan aturan turunan atau edukasi berdasarkan pola pelaporan lapangan dari mahasiswa.',
                'icon' => 'groups',
                'tone' => 'text-emerald-700 bg-emerald-100',
            ]);
        }

        if ($recommendations->isEmpty()) {
            $recommendations->push([
                'title' => 'Belum ada pola dominan',
                'description' => 'Data pada periode ini masih minim. Dashboard akan mulai memberi rekomendasi otomatis saat laporan temuan bertambah.',
                'icon' => 'insights',
                'tone' => 'text-slate-700 bg-slate-100',
            ]);
        }

        return $recommendations->take(4)->values();
    }
}
