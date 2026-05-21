@extends('admin.layouts.app')

@section('title', 'Edit Ruangan Gedung')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Master Ruangan</p>
        <h2 class="mt-2 text-3xl font-semibold text-slate-900">Edit Ruangan Gedung</h2>
        <form action="{{ route('admin.campus-rooms.update', $room) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.campus-rooms._form', ['submitLabel' => 'Perbarui Ruangan'])
        </form>
    </section>
@endsection
