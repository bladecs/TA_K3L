@extends('admin.layouts.app')

@section('title', 'Tambah Hazard Report')
@section('hero_eyebrow', 'Admin Reporting')
@section('hero_title', 'Catat potensi bahaya dari panel admin')
@section('hero_description', 'Admin dapat menambahkan hazard report untuk mendokumentasikan temuan operasional, aduan offline, atau hasil inspeksi awal yang perlu masuk ke sistem.')

@section('content')
    @include('reports.partials.hazard-form', [
        'formAction' => route('admin.hazards.store'),
        'submitLabel' => 'Simpan Hazard Report',
        'cancelUrl' => route('admin.dashboard'),
        'panelEyebrow' => 'Form Admin',
        'panelTitle' => 'Masukkan temuan bahaya ke sistem dengan format yang lebih rapi',
        'panelDescription' => 'Form ini cocok untuk mencatat aduan pengguna yang masuk secara luring, hasil inspeksi cepat, atau hazard yang ditemukan oleh admin saat memantau area kampus.',
        'summaryTips' => [
            ['label' => 'Peran', 'value' => 'Laporan akan tercatat atas akun admin yang sedang aktif.'],
            ['label' => 'Status', 'value' => 'Hazard baru akan masuk ke antrean penanganan.'],
            ['label' => 'Media', 'value' => 'Lampiran foto akan memudahkan klasifikasi awal.'],
        ],
        'sidebarEyebrow' => 'Panduan Admin',
        'sidebarTitle' => 'Catat hazard dengan sudut pandang operasional',
        'sidebarDescription' => 'Semakin detail lokasi, jenis bahaya, dan bukti visual yang dicantumkan, semakin mudah bagi tim untuk menindaklanjuti dan mengarsipkan temuan secara akurat.',
        'sidebarSteps' => [
            ['title' => 'Gunakan lokasi yang konsisten', 'description' => 'Pilih nama lokasi resmi agar dashboard analitik tetap rapi.'],
            ['title' => 'Klasifikasikan sesuai jenis risiko', 'description' => 'Pemilihan kategori yang tepat membantu tren data dan prioritas inspeksi.'],
            ['title' => 'Sertakan konteks lapangan', 'description' => 'Tuliskan apakah area masih aktif digunakan dan siapa yang paling berisiko.'],
        ],
        'emergencyTitle' => 'Perlu pengamanan segera?',
        'emergencyDescription' => 'Jika bahaya ini aktif dan berpotensi langsung mencederai orang, prioritaskan pengamanan area sambil tetap mendokumentasikan temuan di sistem.',
    ])
@endsection
