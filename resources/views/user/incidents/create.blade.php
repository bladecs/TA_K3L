@extends('user.layouts.app')

@section('title', 'Form Pelaporan Insiden')

@section('page')
    <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                Portal Operasional K3L
            </span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pelaporan Insiden</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Laporkan kejadian kecelakaan kerja, cedera, atau insiden operasional agar tim terkait dapat segera
                melakukan penanganan dan tindak lanjut.
            </p>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pb-14">
        @include('reports.partials.incident-form', [
            'showInlineFlash' => true,
            'formAction' => route('user.incidents.store'),
            'submitLabel' => 'Kirim Laporan Insiden',
            'cancelUrl' => route('user.dashboard'),
            'panelEyebrow' => 'Form Pelaporan',
            'panelTitle' => 'Susun laporan insiden dengan struktur yang lebih jelas',
            'panelDescription' => 'Lengkapi identitas kejadian, uraian kronologi, dan bukti pendukung agar Satgas dapat melakukan verifikasi serta tindak lanjut dengan lebih cepat.',
            'summaryTips' => [
                ['label' => 'Pelapor', 'value' => 'Akun Anda akan tercatat otomatis sebagai pengirim laporan.'],
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
    </main>
@endsection
