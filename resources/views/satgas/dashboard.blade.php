@extends('satgas.layouts.app')

@section('title', 'Dashboard Satgas')
@section('hero_eyebrow', 'Dashboard Satgas')
@section('hero_title', 'Pusat analisis temuan dan tindak lanjut lapangan')
@section('hero_description', 'Dashboard ini dirancang untuk membantu Satgas membaca pola temuan, memantau penyelesaian, dan menyusun aturan atau tindakan pencegahan baru berdasarkan data lapangan.')

@section('content')
    @php
        $periodOptions = [
            '30' => '30 Hari',
            '90' => '90 Hari',
            '180' => '6 Bulan',
            '365' => '1 Tahun',
            'all' => 'Semua',
        ];

        $summaryCards = [
            ['label' => 'Total Insiden', 'value' => $incidentStatusSummary['completed'] + $incidentStatusSummary['in_progress'] + $incidentStatusSummary['pending'], 'hint' => 'Semua laporan insiden pada periode aktif.', 'icon' => 'assessment', 'tone' => 'text-[var(--primary-color)]'],
            ['label' => 'Total Hazard', 'value' => $hazardStatusSummary['completed'] + $hazardStatusSummary['in_progress'] + $hazardStatusSummary['pending'], 'hint' => 'Seluruh hazard report yang masuk.', 'icon' => 'warning', 'tone' => 'text-rose-600'],
            ['label' => 'Sedang Diproses', 'value' => $incidentStatusSummary['in_progress'] + $hazardStatusSummary['in_progress'], 'hint' => 'Butuh pemantauan aktif dan tindak lanjut.', 'icon' => 'pending_actions', 'tone' => 'text-sky-600'],
            ['label' => 'Belum Ditangani', 'value' => $incidentStatusSummary['pending'] + $hazardStatusSummary['pending'], 'hint' => 'Perlu validasi awal atau respons pertama.', 'icon' => 'hourglass_top', 'tone' => 'text-amber-600'],
            ['label' => 'Insiden Kritis', 'value' => $stats['critical_incidents'], 'hint' => 'Kasus kritis yang perlu perhatian kebijakan.', 'icon' => 'crisis_alert', 'tone' => 'text-rose-700'],
        ];

        $severityTone = [
            'LOW' => 'bg-emerald-100 text-emerald-700',
            'MEDIUM' => 'bg-amber-100 text-amber-700',
            'HIGH' => 'bg-orange-100 text-orange-700',
            'CRITICAL' => 'bg-rose-100 text-rose-700',
        ];

        $buildPieStyle = function (array $segments): string {
            $total = collect($segments)->sum('value');

            if ($total <= 0) {
                return 'background:conic-gradient(#e2e8f0 0deg 360deg);';
            }

            $current = 0;
            $parts = collect($segments)->map(function ($segment) use (&$current, $total) {
                $start = round(($current / $total) * 360, 2);
                $current += $segment['value'];
                $end = round(($current / $total) * 360, 2);

                return "{$segment['color']} {$start}deg {$end}deg";
            })->implode(', ');

            return "background:conic-gradient({$parts});";
        };

        $incidentPieSegments = [
            ['label' => 'Selesai', 'value' => $incidentStatusSummary['completed'], 'color' => '#159947', 'tone' => 'bg-emerald-500'],
            ['label' => 'Diproses', 'value' => $incidentStatusSummary['in_progress'], 'color' => '#0ea5e9', 'tone' => 'bg-sky-500'],
            ['label' => 'Belum', 'value' => $incidentStatusSummary['pending'], 'color' => '#f59e0b', 'tone' => 'bg-amber-500'],
        ];

        $hazardPieSegments = [
            ['label' => 'Selesai', 'value' => $hazardStatusSummary['completed'], 'color' => '#159947', 'tone' => 'bg-emerald-500'],
            ['label' => 'Diproses', 'value' => $hazardStatusSummary['in_progress'], 'color' => '#0ea5e9', 'tone' => 'bg-sky-500'],
            ['label' => 'Belum', 'value' => $hazardStatusSummary['pending'], 'color' => '#f59e0b', 'tone' => 'bg-amber-500'],
        ];

        $maxSeverity = max(1, $severityBreakdown->max('count'));
        $maxHazardType = max(1, $hazardTypeBreakdown->max('count'));
        $maxMonthly = max(1, collect($monthlyTrend)->max(fn ($item) => max($item['incidents'], $item['hazards'])));
        $maxLocation = max(1, collect($locationInsights)->max('total_reports'));
        $maxIncidentSource = max(1, $sourceBreakdown['incidents']['user'], $sourceBreakdown['incidents']['internal']);
        $maxHazardSource = max(1, $sourceBreakdown['hazards']['user'], $sourceBreakdown['hazards']['internal']);

        $severityLabels = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis',
        ];
    @endphp

    <section class="space-y-8">
        <section class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Kontrol Analitik</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Baca pola laporan berdasarkan periode</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-500">
                        Semua chart di dashboard ini mengikuti periode aktif. Gunakan filter ini untuk membandingkan tren temuan,
                        memeriksa sumber laporan, dan menyusun prioritas kebijakan baru.
                    </p>
                </div>

                <form action="{{ route('satgas.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3" data-auto-submit-form>
                    <label for="dashboard-period" class="text-sm font-semibold text-slate-600">Periode</label>
                    <select id="dashboard-period" name="period"
                        class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition focus:border-[var(--primary-color)] focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10"
                        data-auto-submit>
                        @foreach ($periodOptions as $value => $label)
                            <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <article class="ambient-card rounded-[1.6rem] px-5 py-5 shadow-[0_14px_30px_rgba(15,23,42,0.07)] ring-1 ring-white/80">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $card['label'] }}</p>
                            <p class="mt-3 text-4xl font-bold text-slate-900">{{ $card['value'] }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $card['hint'] }}</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl {{ $card['tone'] }}">{{ $card['icon'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Insight Otomatis</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Rekomendasi tindak lanjut Satgas</h3>
                    </div>
                    <span class="inline-flex w-fit rounded-full border border-[var(--primary-color)]/15 bg-[var(--blue-low-opacity)] px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[var(--primary-color)]">
                        {{ $periodLabel }}
                    </span>
                </div>

                <div class="mt-8 grid gap-4">
                    @foreach ($recommendations as $recommendation)
                        <article class="rounded-[1.4rem] border border-slate-200 bg-[#f8fbff] p-5">
                            <div class="flex gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $recommendation['tone'] }}">
                                    <span class="material-symbols-outlined text-[1.45rem]">{{ $recommendation['icon'] }}</span>
                                </span>
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">{{ $recommendation['title'] }}</h4>
                                    <p class="mt-2 text-sm leading-7 text-slate-500">{{ $recommendation['description'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </article>

            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Sumber Temuan</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Asal laporan pada periode aktif</h3>
                    </div>
                    <p class="text-sm leading-7 text-slate-500">Pemisahan ini membantu Satgas membedakan temuan dari pengguna dan temuan internal lapangan.</p>
                </div>

                <div class="mt-8 space-y-6">
                    <div class="rounded-[1.4rem] bg-[#f8fbff] p-5 ring-1 ring-slate-200">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h4 class="text-lg font-bold text-slate-900">Insiden</h4>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $sourceBreakdown['incidents']['user'] + $sourceBreakdown['incidents']['internal'] }} laporan</span>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>Dari pengguna</span>
                                    <span>{{ $sourceBreakdown['incidents']['user'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-[var(--primary-color)]" style="width: {{ min(100, ($sourceBreakdown['incidents']['user'] / $maxIncidentSource) * 100) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>Temuan internal Satgas/Admin</span>
                                    <span>{{ $sourceBreakdown['incidents']['internal'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-sky-500" style="width: {{ min(100, ($sourceBreakdown['incidents']['internal'] / $maxIncidentSource) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[1.4rem] bg-[#f8fbff] p-5 ring-1 ring-slate-200">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h4 class="text-lg font-bold text-slate-900">Hazard</h4>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $sourceBreakdown['hazards']['user'] + $sourceBreakdown['hazards']['internal'] }} laporan</span>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>Dari pengguna</span>
                                    <span>{{ $sourceBreakdown['hazards']['user'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-[var(--orange)]" style="width: {{ min(100, ($sourceBreakdown['hazards']['user'] / $maxHazardSource) * 100) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-700">
                                    <span>Temuan internal Satgas/Admin</span>
                                    <span>{{ $sourceBreakdown['hazards']['internal'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, ($sourceBreakdown['hazards']['internal'] / $maxHazardSource) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Chart Insiden</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Penyelesaian laporan insiden</h3>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-slate-500">Membantu Satgas melihat berapa laporan yang sudah selesai, masih diproses, dan belum ditangani.</p>
                </div>

                <div class="mt-8 grid gap-8 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-center">
                    <div class="mx-auto flex h-56 w-56 items-center justify-center rounded-full bg-slate-100" style="{{ $buildPieStyle($incidentPieSegments) }}">
                        <div class="flex h-34 w-34 flex-col items-center justify-center rounded-full bg-white shadow-inner">
                            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total</span>
                            <span class="mt-2 text-4xl font-bold text-slate-900">{{ array_sum(array_column($incidentPieSegments, 'value')) }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($incidentPieSegments as $segment)
                            <div class="rounded-[1.2rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full {{ $segment['tone'] }}"></span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $segment['label'] }}</span>
                                    </div>
                                    <span class="text-xl font-bold text-slate-900">{{ $segment['value'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>

            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Chart Hazard</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Penyelesaian hazard report</h3>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-slate-500">Gambaran cepat untuk membedakan hazard yang sudah selesai, masih diproses, dan yang baru masuk.</p>
                </div>

                <div class="mt-8 grid gap-8 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-center">
                    <div class="mx-auto flex h-56 w-56 items-center justify-center rounded-full bg-slate-100" style="{{ $buildPieStyle($hazardPieSegments) }}">
                        <div class="flex h-34 w-34 flex-col items-center justify-center rounded-full bg-white shadow-inner">
                            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Total</span>
                            <span class="mt-2 text-4xl font-bold text-slate-900">{{ array_sum(array_column($hazardPieSegments, 'value')) }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($hazardPieSegments as $segment)
                            <div class="rounded-[1.2rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full {{ $segment['tone'] }}"></span>
                                        <span class="text-sm font-semibold text-slate-700">{{ $segment['label'] }}</span>
                                    </div>
                                    <span class="text-xl font-bold text-slate-900">{{ $segment['value'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Tren Bulanan</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Temuan insiden dan hazard per bulan</h3>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-slate-500">Membaca lonjakan tren bulanan membantu Satgas menyusun prioritas pembinaan dan aturan baru.</p>
                </div>

                <div class="mt-8 space-y-5">
                    @foreach ($monthlyTrend as $item)
                        <div class="rounded-[1.2rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200">
                            <div class="mb-3 flex items-center justify-between gap-4">
                                <span class="text-sm font-semibold text-slate-700">{{ $item['label'] }}</span>
                                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $item['incidents'] + $item['hazards'] }} total temuan</span>
                            </div>
                            <div class="grid gap-3">
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                        <span>Insiden</span>
                                        <span>{{ $item['incidents'] }}</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-[var(--primary-color)]" style="width: {{ min(100, ($item['incidents'] / $maxMonthly) * 100) }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-2 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                        <span>Hazard</span>
                                        <span>{{ $item['hazards'] }}</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-[var(--orange)]" style="width: {{ min(100, ($item['hazards'] / $maxMonthly) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="space-y-6">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-7">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Severity</p>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Sebaran tingkat keparahan</h3>

                    <div class="mt-6 space-y-4">
                        @foreach ($severityBreakdown as $severity)
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $severityTone[$severity['label']] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $severityLabels[$severity['key']] ?? $severity['label'] }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $severity['count'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $severity['key'] === 'critical' ? 'bg-rose-500' : ($severity['key'] === 'high' ? 'bg-orange-500' : ($severity['key'] === 'medium' ? 'bg-amber-500' : 'bg-emerald-500')) }}"
                                        style="width: {{ min(100, ($severity['count'] / $maxSeverity) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-7">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Jenis Hazard</p>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Sebaran sumber potensi bahaya</h3>

                    <div class="mt-6 space-y-4">
                        @foreach ($hazardTypeBreakdown as $hazardType)
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <span class="text-sm font-semibold text-slate-700">{{ $hazardType['label'] }}</span>
                                    <span class="text-sm font-bold text-slate-900">{{ $hazardType['count'] }}</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-[var(--primary-color)]" style="width: {{ min(100, ($hazardType['count'] / $maxHazardType) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Lokasi Dominan</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Area dengan temuan terbanyak</h3>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-slate-500">Area dominan bisa dipakai sebagai dasar inspeksi rutin, edukasi khusus, atau penyusunan aturan baru.</p>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($locationInsights as $location)
                        <div class="rounded-[1.2rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200">
                            <div class="mb-2 flex items-center justify-between gap-4">
                                <span class="text-sm font-semibold text-slate-700">{{ $location->location_name }}</span>
                                <span class="text-sm font-bold text-slate-900">{{ $location->total_reports }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-[var(--primary-color)]" style="width: {{ min(100, ($location->total_reports / $maxLocation) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.2rem] border border-dashed border-slate-300 px-5 py-8 text-center text-sm text-slate-500">
                            Belum ada data lokasi temuan yang bisa dianalisis.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-[var(--primary-color)]/8 lg:p-8">
                <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Laporan Prioritas</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-900">Penanganan yang perlu perhatian</h3>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('satgas.incidents.index') }}"
                            class="inline-flex items-center rounded-full bg-[var(--red)] px-4 py-2 text-sm font-semibold text-white transition hover:opacity-95">
                            Review Insiden
                        </a>
                        <a href="{{ route('satgas.knowledge-articles.index') }}"
                            class="inline-flex items-center rounded-full bg-[var(--primary-color)] px-4 py-2 text-sm font-semibold text-white transition hover:opacity-95">
                            Kelola Materi
                        </a>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($priorityReports as $report)
                        <article class="rounded-[1.4rem] border border-slate-200 bg-[#f8fbff] p-5 transition hover:bg-white hover:shadow-sm">
                            <div class="flex flex-col gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="text-lg font-bold text-slate-900">{{ $report->title }}</p>
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $report->severity_level === 'critical' ? 'bg-rose-600 text-white' : ($report->severity_level === 'high' ? 'bg-orange-500 text-white' : ($report->severity_level === 'medium' ? 'bg-amber-200 text-amber-900' : 'bg-emerald-100 text-emerald-800')) }}">
                                            {{ strtoupper($report->severity_level) }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            {{ $report->reporter?->role?->code === 'mahasiswa' ? 'Laporan Pengguna' : 'Temuan Internal' }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $report->report_number }} - {{ $report->reporter?->name ?? '-' }} - {{ $report->location?->name ?? '-' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">Status: {{ str_replace('_', ' ', $report->status) }}</p>
                                </div>
                                <a href="{{ route('satgas.incidents.show', $report) }}"
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                                    Tinjau laporan
                                    <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[1.4rem] border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada laporan prioritas untuk ditinjau pada periode ini.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    </section>
@endsection
