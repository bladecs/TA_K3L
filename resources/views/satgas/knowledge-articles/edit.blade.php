@extends('satgas.layouts.app')

@section('title', 'Edit Materi Knowledge Satgas')
@section('hero_eyebrow', 'Satgas Knowledge')
@section('hero_title', 'Perbarui materi edukasi yang sudah ada')
@section('hero_description', 'Rapikan isi section, ubah media, dan sesuaikan status publikasi materi sesuai kebutuhan operasional.')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Satgas Knowledge</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Materi Knowledge</h2>
        <form action="{{ route('satgas.knowledge-articles.update', $knowledgeArticle) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @include('admin.knowledge-articles._form', ['submitLabel' => 'Perbarui Materi'])
        </form>
    </section>
@endsection
