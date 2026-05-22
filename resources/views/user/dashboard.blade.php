@extends('user.layouts.app')

@section('title', 'SIAGA POLMAN')

@section('page')
    <header id="header" class="relative flex min-h-[720px] w-full flex-col justify-center px-4 pb-24 pt-32 sm:px-6 lg:px-10">
        <div class="relative z-1 mx-auto grid w-full max-w-[1360px] gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(360px,0.72fr)] lg:items-center">
            <div>
                <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/90">Portal K3L Publik</span>
                <h1 class="mt-6 max-w-5xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-7xl">
                    SIAGA POLMAN
                </h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-white/90 sm:text-lg lg:text-2xl">
                    Pusat informasi keselamatan, pelaporan insiden, dan pelaporan potensi bahaya untuk seluruh warga kampus dan pengunjung.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('user.incidents.create') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-4 text-sm font-bold text-[var(--primary-color)] shadow-[0_18px_35px_rgba(15,23,42,0.2)] transition hover:-translate-y-1">
                        <span class="material-symbols-outlined">contract_edit</span>
                        Laporan
                    </a>
                    <a href="{{ route('user.incidents.status') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-6 py-4 text-sm font-bold text-white transition hover:bg-white/20">
                        <span class="material-symbols-outlined">timeline</span>
                        Cek Status
                    </a>
                    <a href="{{ route('user.guide') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-6 py-4 text-sm font-bold text-white transition hover:bg-white/20">
                        <span class="material-symbols-outlined">help</span>
                        Panduan
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc]">
        @isset($userDashboard)
            <section class="mx-auto grid w-full max-w-[1360px] gap-6 px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-10">
                <article class="section-shell rounded-[2rem] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-white/80 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Progress Laporan Insiden</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-slate-900">{{ $userDashboard['latestReportSummary']['status_label'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $userDashboard['latestReportSummary']['status_note'] }}</p>

                    @if ($userDashboard['latestReportSummary']['report'])
                        <div class="mt-6 rounded-[1.25rem] bg-white p-5 ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $userDashboard['latestReportSummary']['report']->report_number }}</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">{{ $userDashboard['latestReportSummary']['report']->title }}</h3>
                            <p class="mt-2 text-sm font-semibold text-[var(--primary-color)]">{{ $userDashboard['latestReportSummary']['report']->category?->name ?? '-' }}</p>
                        </div>
                    @endif
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Status Hazard Terbaru</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-slate-900">{{ $userDashboard['latestHazardSummary']['status_label'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $userDashboard['latestHazardSummary']['status_note'] }}</p>

                    @if ($userDashboard['latestHazardSummary']['report'])
                        <div class="mt-6 rounded-[1.25rem] bg-[#f8fbff] p-5 ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $userDashboard['latestHazardSummary']['report']->report_number }}</p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">{{ $userDashboard['latestHazardSummary']['report']->title }}</h3>
                            <p class="mt-2 text-sm text-slate-500">Ditangani oleh {{ $userDashboard['latestHazardSummary']['handled_by'] }}</p>
                        </div>
                    @endif
                </article>
            </section>
        @endisset

        @include('user.hazards.partials.public-gis-section')

        <section class="mx-auto grid w-full max-w-[1360px] gap-6 px-4 pb-14 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-10">
            <div class="section-shell rounded-[2rem] p-6 ring-1 ring-white/80 lg:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Alur Pelaporan</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Laporkan cepat, pantau dengan nomor laporan</h2>
                <div class="mt-7 space-y-4">
                    @foreach ([
                        ['title' => 'Isi identitas dan kontak aktif', 'text' => 'Nama, email, dan WhatsApp diperlukan untuk update status.'],
                        ['title' => 'Lengkapi detail kejadian', 'text' => 'Kronologi insiden mendukung voice to text di browser yang kompatibel.'],
                        ['title' => 'Simpan nomor laporan', 'text' => 'Nomor ini dipakai untuk mengecek progres pada halaman status.'],
                    ] as $index => $step)
                        <div class="flex gap-4 rounded-[1.3rem] bg-white p-4 ring-1 ring-slate-200">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--primary-color)] text-sm font-bold text-white">{{ $index + 1 }}</span>
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $step['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-6">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Materi K3</p>
                            <h2 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $publishedKnowledgeCount }} materi tersedia</h2>
                        </div>
                        <a href="{{ route('user.knowledge.index') }}" class="inline-flex items-center justify-center rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white">Buka Materi</a>
                    </div>

                    <div class="mt-6 grid gap-3">
                        @forelse ($knowledgeRecommendations as $article)
                            <a href="{{ route('user.knowledge.show', $article->slug) }}" class="rounded-[1.2rem] bg-[#f8fbff] px-4 py-4 ring-1 ring-slate-200 transition hover:bg-white">
                                <p class="text-sm font-semibold text-[var(--primary-color)]">{{ $article->category?->name ?? 'Materi K3' }}</p>
                                <h3 class="mt-1 font-bold text-slate-900">{{ $article->title }}</h3>
                            </a>
                        @empty
                            <p class="rounded-[1.2rem] bg-[#f8fbff] px-4 py-5 text-sm text-slate-500 ring-1 ring-slate-200">Materi K3 akan tampil setelah dipublikasikan admin.</p>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-[2rem] bg-[#7a2c00] p-6 text-white shadow-[0_18px_45px_rgba(122,44,0,0.2)] lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/75">Kontak Darurat</p>
                    <h2 class="mt-3 text-2xl font-extrabold">Butuh bantuan segera?</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        @forelse ($emergencyContacts as $contact)
                            <div class="rounded-[1.2rem] bg-white/10 px-4 py-4 ring-1 ring-white/15">
                                <p class="text-sm font-bold">{{ $contact->name }}</p>
                                <p class="mt-2 text-lg font-extrabold">{{ $contact->phone_number }}</p>
                            </div>
                        @empty
                            <p class="rounded-[1.2rem] bg-white/10 px-4 py-4 text-sm text-white/80 ring-1 ring-white/15">Kontak darurat akan tersedia setelah admin mengisi data.</p>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>
    </main>
@endsection
