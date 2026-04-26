@extends('satgas.layouts.app')

@section('title', 'Tambah Laporan Insiden')
@section('hero_eyebrow', 'Operasional Satgas')
@section('hero_title', 'Buat laporan insiden dan tindak lanjuti lebih cepat')
@section('hero_description', 'Satgas dapat mencatat insiden langsung dari lapangan, mendokumentasikan temuan operasional, lalu segera membuka detail laporan untuk proses verifikasi dan tindak lanjut.')

@section('content')
    @include('reports.partials.incident-form', [
        'formAction' => route('satgas.incidents.store'),
        'submitLabel' => 'Buat Laporan Insiden',
        'cancelUrl' => route('satgas.dashboard'),
        'panelEyebrow' => 'Form Satgas',
        'panelTitle' => 'Catat insiden langsung dari area kerja lapangan',
        'panelDescription' => 'Form ini memudahkan Satgas membuat laporan resmi berdasarkan temuan saat inspeksi, kejadian mendadak di lapangan, atau informasi yang diterima langsung di lokasi.',
        'summaryTips' => [
            ['label' => 'Akses', 'value' => 'Setelah dibuat, laporan bisa langsung Anda buka untuk tindak lanjut.'],
            ['label' => 'Dokumen', 'value' => 'Lampiran lapangan membantu validasi status awal.'],
            ['label' => 'Prioritas', 'value' => 'Tingkat keparahan akan membantu analisis dan respons.'],
        ],
        'sidebarEyebrow' => 'Panduan Lapangan',
        'sidebarTitle' => 'Siapkan laporan yang siap diverifikasi',
        'sidebarDescription' => 'Karena Satgas juga menjadi pihak yang memproses, form ini dirancang agar pencatatan awal dan tindak lanjut operasional bisa mengalir lebih cepat.',
        'sidebarSteps' => [
            ['title' => 'Tulis kronologi berbasis fakta lapangan', 'description' => 'Hindari uraian umum. Fokus pada urutan peristiwa yang bisa diverifikasi.'],
            ['title' => 'Tentukan severity setepat mungkin', 'description' => 'Ini akan memengaruhi prioritas penanganan dan insight dashboard satgas.'],
            ['title' => 'Lampirkan kondisi aktual', 'description' => 'Foto area, alat, atau dampak langsung akan memperkuat catatan tindak lanjut.'],
        ],
        'emergencyTitle' => 'Masih ada risiko aktif?',
        'emergencyDescription' => 'Jika bahaya atau insiden masih berlangsung, dahulukan pengamanan dan tindakan awal lapangan sebelum proses administrasi lanjutan.',
    ])
@endsection
