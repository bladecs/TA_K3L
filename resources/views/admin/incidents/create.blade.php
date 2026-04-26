@extends('admin.layouts.app')

@section('title', 'Tambah Laporan Insiden')
@section('hero_eyebrow', 'Admin Reporting')
@section('hero_title', 'Buat laporan insiden dari panel admin')
@section('hero_description', 'Admin dapat mencatat insiden langsung dari panel kerja untuk mendokumentasikan temuan operasional, kejadian internal, atau laporan yang diterima secara offline.')

@section('content')
    @include('reports.partials.incident-form', [
        'formAction' => route('admin.incidents.store'),
        'submitLabel' => 'Simpan Laporan Insiden',
        'cancelUrl' => route('admin.dashboard'),
        'panelEyebrow' => 'Form Admin',
        'panelTitle' => 'Dokumentasikan insiden langsung dari pusat kendali',
        'panelDescription' => 'Gunakan formulir ini ketika admin menerima laporan manual, menemukan insiden secara langsung, atau perlu mencatat kejadian untuk kebutuhan dokumentasi sistem.',
        'summaryTips' => [
            ['label' => 'Aktor', 'value' => 'Akun admin akan tercatat sebagai pelapor laporan ini.'],
            ['label' => 'Output', 'value' => 'Laporan masuk ke antrean verifikasi dan analisis lanjutan.'],
            ['label' => 'Dokumen', 'value' => 'Lampiran akan tersimpan sebagai bukti pendukung resmi.'],
        ],
        'sidebarEyebrow' => 'Catatan Admin',
        'sidebarTitle' => 'Pastikan data siap dipakai Satgas',
        'sidebarDescription' => 'Karena laporan dari admin sering menjadi dokumen rujukan, pastikan isinya cukup kuat untuk audit, tindak lanjut, dan evaluasi kebijakan.',
        'sidebarSteps' => [
            ['title' => 'Pastikan sumber informasi valid', 'description' => 'Jika laporan berasal dari pihak lain, tulis informasi lapangan seakurat mungkin.'],
            ['title' => 'Isi kronologi dengan bahasa formal', 'description' => 'Gunakan uraian yang rapi agar mudah dipakai dalam dokumentasi internal.'],
            ['title' => 'Tambahkan bukti yang bisa diverifikasi', 'description' => 'Foto kondisi, dokumen, atau file pendukung sangat membantu proses tindak lanjut.'],
        ],
        'emergencyTitle' => 'Kejadian berisiko tinggi?',
        'emergencyDescription' => 'Jika insiden masih aktif atau memerlukan koordinasi cepat, teruskan juga melalui jalur operasional darurat agar tindakannya tidak menunggu proses administrasi.',
    ])
@endsection
