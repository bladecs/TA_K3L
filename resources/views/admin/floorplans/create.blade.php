@extends('admin.layouts.app')

@section('title', 'Buat Denah Gedung')
@section('hero_title', 'Buat Denah Gedung')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <form action="{{ route('admin.floorplans.store') }}" method="POST">
            @include('admin.floorplans._form', ['submitLabel' => 'Simpan dan Generate Denah'])
        </form>
    </section>
@endsection
