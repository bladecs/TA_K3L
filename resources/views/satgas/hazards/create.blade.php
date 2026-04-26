@extends('satgas.layouts.app')

@section('title', 'Tambah Hazard Report')
@section('hero_eyebrow', 'Operasional Satgas')
@section('hero_title', 'Masukkan hazard report langsung dari lapangan')
@section('hero_description', 'Satgas dapat mencatat potensi bahaya saat inspeksi, patroli, atau tindak lanjut lapangan agar temuan langsung masuk ke sistem analitik dan penanganan.')

@section('content')
    @include('reports.partials.hazard-form', [
        'formAction' => route('satgas.hazards.store'),
        'submitLabel' => 'Buat Hazard Report',
        'cancelUrl' => route('satgas.dashboard'),
        'panelEyebrow' => 'Form Satgas',
        'panelTitle' => 'Dokumentasikan temuan bahaya dengan format siap tindak lanjut',
        'panelDescription' => 'Gunakan formulir ini untuk mengubah temuan lapangan menjadi hazard report resmi yang bisa dianalisis, dipantau, dan diselesaikan melalui panel satgas.',
        'summaryTips' => [
            ['label' => 'Aksi Cepat', 'value' => 'Laporan bisa langsung dibuka kembali untuk update status.'],
            ['label' => 'Analitik', 'value' => 'Jenis hazard dan lokasi akan masuk ke dashboard satgas.'],
            ['label' => 'Bukti', 'value' => 'Foto visual membantu klasifikasi dan evaluasi tren.'],
        ],
        'sidebarEyebrow' => 'Catatan Satgas',
        'sidebarTitle' => 'Buat data temuan yang berguna untuk analisis',
        'sidebarDescription' => 'Selain untuk respons cepat, hazard report dari Satgas juga akan membantu membaca pola lokasi rawan dan jenis potensi bahaya dominan.',
        'sidebarSteps' => [
            ['title' => 'Pilih kategori yang paling relevan', 'description' => 'Konsistensi kategori akan membuat dashboard analitik lebih bermakna.'],
            ['title' => 'Isi catatan dengan konteks lapangan', 'description' => 'Jelaskan apakah area sedang aktif dipakai, siapa yang berisiko, dan seberapa mendesak penanganannya.'],
            ['title' => 'Lampirkan bukti aktual', 'description' => 'Foto saat temuan pertama akan mempermudah evaluasi sebelum dan sesudah perbaikan.'],
        ],
        'emergencyTitle' => 'Bahaya butuh pengamanan langsung?',
        'emergencyDescription' => 'Jika kondisi hazard berpotensi langsung menimbulkan cedera atau kerusakan besar, amankan area terlebih dahulu lalu lanjutkan dokumentasi di sistem.',
    ])
@endsection
