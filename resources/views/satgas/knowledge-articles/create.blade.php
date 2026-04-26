@extends('satgas.layouts.app')

@section('title', 'Tambah Materi Knowledge Satgas')
@section('hero_eyebrow', 'Satgas Knowledge')
@section('hero_title', 'Susun materi baru untuk edukasi K3L')
@section('hero_description', 'Buat materi terstruktur per section, tambahkan media pendukung, dan siapkan konten yang siap dipublikasikan.')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Satgas Knowledge</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Materi Knowledge</h2>
        <form action="{{ route('satgas.knowledge-articles.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @include('admin.knowledge-articles._form', ['submitLabel' => 'Simpan Materi'])
        </form>
    </section>
@endsection
