@extends('user.layouts.app')

@section('title', 'Form Potensi Bahaya')

@section('page')
    <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                Portal Operasional K3L
            </span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pelaporan Potensi Bahaya</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Laporkan kondisi tidak aman, near-miss, atau potensi risiko di area kampus agar dapat segera ditindaklanjuti
                sebelum berkembang menjadi insiden.
            </p>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pb-14">
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
    </main>
@endsection
