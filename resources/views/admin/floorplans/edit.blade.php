@extends('admin.layouts.app')

@section('title', 'Edit Denah Gedung')
@section('hero_title', 'Edit Denah Gedung')

@section('content')
    <section class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <form action="{{ route('admin.floorplans.update', $floorplan) }}" method="POST">
            @method('PUT')
            @include('admin.floorplans._form', ['submitLabel' => 'Update dan Generate Ulang'])
        </form>
    </section>
@endsection
