@extends('user.layouts.app')

@php
    $activeReportType = $activeReportType ?? 'incident';
@endphp

@section('title', 'Form Pelaporan K3L')

@section('page')
    <header id="header" class="relative flex min-h-[34rem] w-full flex-col items-center justify-center gap-4 px-6 pb-12 pt-24">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                Portal Operasional K3L
            </span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Form Pelaporan K3L</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Pilih jenis laporan yang sesuai, lalu lengkapi detailnya agar tim dapat melakukan verifikasi dan tindak lanjut dengan cepat.
            </p>

            <div class="mt-8 grid w-full max-w-2xl gap-3 rounded-[1.7rem] border border-white/20 bg-white/12 p-2 shadow-[0_18px_38px_rgba(15,23,42,0.16)] backdrop-blur sm:grid-cols-2">
                <a href="{{ route('user.incidents.create') }}" data-report-switch="incident"
                    class="inline-flex items-center justify-center gap-2 rounded-[1.35rem] px-5 py-4 text-sm font-bold transition {{ $activeReportType === 'incident' ? 'bg-white text-[var(--primary-color)] shadow-sm' : 'text-white hover:bg-white/15' }}">
                    <span class="material-symbols-outlined text-[20px]">contract_edit</span>
                    Form Insiden
                </a>
                <a href="{{ route('user.hazards.create') }}" data-report-switch="hazard"
                    class="inline-flex items-center justify-center gap-2 rounded-[1.35rem] px-5 py-4 text-sm font-bold transition {{ $activeReportType === 'hazard' ? 'bg-white text-[var(--primary-color)] shadow-sm' : 'text-white hover:bg-white/15' }}">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                    Form Hazard
                </a>
            </div>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pb-14" data-report-page data-active-report="{{ $activeReportType }}">
        <div data-report-panel="incident" class="{{ $activeReportType === 'incident' ? '' : 'hidden' }}">
            @include('reports.partials.incident-form', [
                'showInlineFlash' => true,
                'formAction' => route('user.incidents.store'),
                'submitLabel' => 'Kirim Laporan Insiden',
                'cancelUrl' => route('user.dashboard'),
                'panelEyebrow' => 'Form Pelaporan',
                'panelTitle' => 'Susun laporan insiden dengan struktur yang lebih jelas',
                'panelDescription' => 'Lengkapi identitas kejadian, uraian kronologi, dan bukti pendukung agar Satgas dapat melakukan verifikasi serta tindak lanjut dengan lebih cepat.',
                'summaryTips' => [
                    ['label' => 'Pelapor', 'value' => 'Isi kontak aktif agar pembaruan status dapat dikirim.'],
                    ['label' => 'Lampiran', 'value' => 'Maksimal 3 file untuk memudahkan verifikasi awal.'],
                    ['label' => 'Status Awal', 'value' => 'Laporan akan masuk ke antrean review Satgas.'],
                ],
                'sidebarEyebrow' => 'Panduan Singkat',
                'sidebarTitle' => 'Agar laporan lebih cepat diproses',
                'sidebarDescription' => 'Semakin jelas detail yang Anda isi, semakin cepat tim dapat memahami tingkat risiko dan menentukan tindak lanjut.',
                'sidebarSteps' => [
                    ['title' => 'Tulis lokasi yang spesifik', 'description' => 'Sebutkan gedung, ruang, atau titik kejadian sedetail mungkin.'],
                    ['title' => 'Jelaskan urutan kejadian', 'description' => 'Tuliskan kronologi singkat tapi runtut, mulai dari awal sampai kondisi terakhir.'],
                    ['title' => 'Lampirkan bukti yang relevan', 'description' => 'Foto alat, area, atau kondisi terkini akan sangat membantu proses review.'],
                ],
                'emergencyTitle' => 'Butuh respons darurat?',
                'emergencyDescription' => 'Jika kejadian ini melibatkan cedera langsung, kebakaran, atau risiko lanjutan yang aktif, segera gunakan pusat darurat agar bantuan awal tidak tertunda.',
            ])
        </div>

        <div data-report-panel="hazard" class="{{ $activeReportType === 'hazard' ? '' : 'hidden' }}">
            @include('reports.partials.hazard-form', [
                'showInlineFlash' => true,
                'formAction' => route('user.hazards.store'),
                'submitLabel' => 'Kirim Hazard Report',
                'cancelUrl' => route('user.dashboard'),
                'panelEyebrow' => 'Form Hazard',
                'panelTitle' => 'Laporkan potensi bahaya sebelum menjadi insiden',
                'panelDescription' => 'Gunakan formulir ini untuk mengirim temuan lapangan yang berpotensi menimbulkan kecelakaan, gangguan kerja, atau risiko keselamatan lainnya.',
                'summaryTips' => [
                    ['label' => 'Prioritas', 'value' => 'Temuan baru akan masuk ke antrean review Satgas.'],
                    ['label' => 'Bukti', 'value' => 'Tambahkan foto aktual agar verifikasi lebih cepat.'],
                    ['label' => 'Lokasi', 'value' => 'Titik spesifik membantu tindakan pengamanan awal.'],
                ],
                'sidebarEyebrow' => 'Panduan Lapangan',
                'sidebarTitle' => 'Agar hazard report lebih akurat',
                'sidebarDescription' => 'Laporan yang spesifik memudahkan Satgas menentukan apakah risiko perlu tindakan segera, inspeksi ulang, atau edukasi tambahan.',
                'sidebarSteps' => [
                    ['title' => 'Pilih jenis bahaya yang paling dekat', 'description' => 'Ini membantu klasifikasi awal dan pembagian tindak lanjut yang tepat.'],
                    ['title' => 'Sebutkan titik bahaya secara rinci', 'description' => 'Contohnya panel, lorong, mesin, atau meja kerja tertentu.'],
                    ['title' => 'Tulis risiko yang mungkin terjadi', 'description' => 'Misalnya tersengat listrik, terpeleset, atau terpapar bahan kimia.'],
                ],
                'emergencyTitle' => 'Ada risiko langsung?',
                'emergencyDescription' => 'Jika potensi bahaya ini dapat segera menyebabkan cedera, kebakaran, atau kerusakan besar, prioritaskan pengamanan area dan gunakan pusat darurat.',
            ])
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        (() => {
            const page = document.querySelector('[data-report-page]');
            const switches = Array.from(document.querySelectorAll('[data-report-switch]'));
            const panels = Array.from(document.querySelectorAll('[data-report-panel]'));

            if (!page || switches.length === 0 || panels.length === 0) {
                return;
            }

            const setActiveReport = (type) => {
                page.dataset.activeReport = type;

                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.reportPanel !== type);
                });

                switches.forEach((switcher) => {
                    const isActive = switcher.dataset.reportSwitch === type;
                    switcher.classList.toggle('bg-white', isActive);
                    switcher.classList.toggle('text-[var(--primary-color)]', isActive);
                    switcher.classList.toggle('shadow-sm', isActive);
                    switcher.classList.toggle('text-white', !isActive);
                    switcher.classList.toggle('hover:bg-white/15', !isActive);
                    switcher.setAttribute('aria-current', isActive ? 'page' : 'false');
                });
            };

            switches.forEach((switcher) => {
                switcher.addEventListener('click', (event) => {
                    const type = switcher.dataset.reportSwitch;

                    if (!type || !panels.some((panel) => panel.dataset.reportPanel === type)) {
                        return;
                    }

                    event.preventDefault();
                    setActiveReport(type);
                });
            });

            setActiveReport(page.dataset.activeReport || 'incident');
        })();
    </script>
@endpush
