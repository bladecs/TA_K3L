@extends('satgas.layouts.app')

@section('title', 'Knowledge Satgas')
@section('hero_eyebrow', 'Satgas Knowledge')
@section('hero_title', 'Kelola materi edukasi dan panduan lapangan')
@section('hero_description', 'Satgas dapat menambahkan, memperbarui, dan mengelola materi edukasi K3L yang akan ditampilkan kepada pengguna.')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col justify-between gap-4 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Satgas Knowledge</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Kelola Materi Knowledge</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Gunakan ruang ini untuk menyiapkan materi edukasi, SOP, dan panduan operasional yang bisa dibaca pengguna.</p>
            </div>

            <a href="{{ route('satgas.knowledge-articles.create') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Tambah Materi
            </a>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Judul</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Published</th>
                            <th class="px-6 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($articles as $article)
                            <tr>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $article->title }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $article->category?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ $article->status }}</td>
                                <td class="px-6 py-4 text-slate-700">{{ optional($article->published_at)->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('satgas.knowledge-articles.edit', $article) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Edit</a>
                                        <form action="{{ route('satgas.knowledge-articles.destroy', $article) }}" method="POST"
                                            data-confirm-action="materi knowledge"
                                            data-confirm-item="{{ $article->title }}"
                                            data-confirm-message="Materi yang dihapus akan hilang dari panel Satgas dan knowledge center. Pastikan tidak ada konten penting yang masih dibutuhkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada materi knowledge.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $articles->links() }}
            </div>
        </div>
    </section>
@endsection
