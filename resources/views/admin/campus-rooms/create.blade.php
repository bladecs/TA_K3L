@extends('admin.layouts.app')

@section('title', 'Tambah Ruangan Gedung')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Master Ruangan</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Ruangan Gedung</h2>
        <form action="{{ route('admin.campus-rooms.store') }}" method="POST" class="mt-8">
            @include('admin.campus-rooms._form', ['submitLabel' => 'Simpan Ruangan'])
        </form>
    </section>
@endsection
