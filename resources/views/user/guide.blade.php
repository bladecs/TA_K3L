@extends('user.layouts.app')

@section('title', 'Panduan Pengguna')

@section('page')
    <header id="header" class="relative flex min-h-[560px] w-full flex-col items-center justify-center px-6 pb-20 pt-32">
        <div class="relative z-1 mx-auto flex w-full max-w-[1180px] flex-col items-center text-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">Panduan Pengguna</span>
            <h1 class="mt-6 max-w-4xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-7xl">
                Cara Menggunakan SIAGA POLMAN
            </h1>
            <p class="mt-5 max-w-3xl text-base leading-8 text-white/90 sm:text-lg lg:text-2xl">
                Ikuti panduan ini untuk membuat laporan, memantau status, melihat informasi K3, dan menghubungi bantuan darurat kampus.
            </p>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pb-16 pt-14">
        <div class="mx-auto grid w-full max-w-[1360px] gap-6 px-4 sm:px-6 lg:px-10">
            <section class="section-shell rounded-[2rem] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-white/80 lg:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Mulai Dari Sini</p>
                        <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Alur penggunaan utama</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            Gunakan fitur sesuai kebutuhan: laporkan insiden bila sudah terjadi, laporkan potensi bahaya bila menemukan kondisi berisiko, lalu pantau perkembangannya melalui halaman status.
                        </p>
                    </div>
                    <a href="{{ route('user.incidents.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white shadow-[0_15px_30px_rgba(10,77,179,0.18)] transition hover:-translate-y-1">
                        <span class="material-symbols-outlined text-[20px]">contract_edit</span>
                        Buat Laporan
                    </a>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ([
                        ['icon' => 'contract_edit', 'title' => 'Buat laporan', 'text' => 'Pilih menu Laporan, isi identitas, lokasi, kategori, kronologi, dan bukti pendukung bila tersedia.'],
                        ['icon' => 'warning', 'title' => 'Laporkan potensi bahaya', 'text' => 'Gunakan form potensi bahaya untuk kondisi yang belum menjadi insiden, seperti kabel terbuka atau area licin.'],
                        ['icon' => 'timeline', 'title' => 'Cek status', 'text' => 'Masukkan nomor laporan atau kata kunci lain untuk melihat progres verifikasi dan tindak lanjut.'],
                        ['icon' => 'emergency_home', 'title' => 'Hubungi darurat', 'text' => 'Buka Pusat Darurat untuk melihat kontak penting dan panduan respons awal saat keadaan mendesak.'],
                    ] as $item)
                        <article class="rounded-[1.4rem] bg-white p-5 shadow-[0_12px_30px_rgba(15,23,42,0.06)] ring-1 ring-slate-200">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                            </span>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">{{ $item['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Langkah Pelaporan</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Agar laporan mudah ditindaklanjuti</h2>

                    <div class="mt-7 space-y-4">
                        @foreach ([
                            ['title' => 'Isi data kontak aktif', 'text' => 'Pastikan nama, email, dan nomor WhatsApp benar agar petugas dapat menghubungi bila perlu klarifikasi.'],
                            ['title' => 'Jelaskan lokasi dengan spesifik', 'text' => 'Pilih lokasi yang tersedia dan tambahkan detail area, lantai, ruangan, atau titik terdekat.'],
                            ['title' => 'Tulis kronologi singkat', 'text' => 'Sampaikan apa yang terjadi, waktu kejadian, kondisi korban atau lingkungan, dan tindakan awal yang sudah dilakukan.'],
                            ['title' => 'Simpan nomor laporan', 'text' => 'Nomor laporan akan dipakai untuk mengecek status dan membuka detail laporan.'],
                        ] as $index => $step)
                            <div class="flex gap-4 rounded-[1.25rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--primary-color)] text-sm font-bold text-white">{{ $index + 1 }}</span>
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $step['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $step['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <div class="grid gap-6">
                    <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Fitur Pendukung</p>
                        <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Informasi yang bisa diakses pengguna</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <a href="{{ route('user.knowledge.index') }}" class="rounded-[1.2rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200 transition hover:bg-white">
                                <p class="font-bold text-slate-900">Materi K3</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Baca artikel keselamatan dan kesehatan lingkungan kampus.</p>
                            </a>
                            <a href="{{ route('user.hazards.map') }}" class="rounded-[1.2rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200 transition hover:bg-white">
                                <p class="font-bold text-slate-900">Peta Hazard</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Lihat persebaran potensi bahaya yang tercatat.</p>
                            </a>
                            <a href="{{ route('user.incidents.status') }}" class="rounded-[1.2rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200 transition hover:bg-white">
                                <p class="font-bold text-slate-900">Status Laporan</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Pantau progres laporan dari submitted hingga selesai.</p>
                            </a>
                            <a href="{{ route('user.emergency.index') }}" class="rounded-[1.2rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200 transition hover:bg-white">
                                <p class="font-bold text-slate-900">Pusat Darurat</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Akses kontak dan instruksi awal saat membutuhkan bantuan segera.</p>
                            </a>
                        </div>
                    </article>

                    <article class="rounded-[2rem] bg-[#7a2c00] p-6 text-white shadow-[0_18px_45px_rgba(122,44,0,0.2)] lg:p-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-white/75">Catatan Penting</p>
                        <h2 class="mt-3 text-2xl font-extrabold">Untuk kondisi gawat darurat</h2>
                        <p class="mt-3 text-sm leading-7 text-white/85">
                            Prioritaskan keselamatan diri dan orang sekitar. Hubungi kontak darurat kampus terlebih dahulu, lalu buat laporan setelah situasi cukup aman untuk didokumentasikan.
                        </p>
                    </article>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                        <span class="material-symbols-outlined">assignment_add</span>
                    </span>
                    <p class="mt-5 text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Laporan Insiden</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Kapan digunakan?</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Gunakan laporan insiden ketika kejadian sudah terjadi, misalnya kecelakaan kerja, cedera, kebakaran kecil, tumpahan bahan, kerusakan fasilitas yang berdampak langsung, atau kondisi lain yang memerlukan tindak lanjut petugas.
                    </p>
                    <a href="{{ route('user.incidents.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-[20px]">contract_edit</span>
                        Isi Laporan Insiden
                    </a>
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <span class="material-symbols-outlined">warning</span>
                    </span>
                    <p class="mt-5 text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Potensi Bahaya</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Kapan digunakan?</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Gunakan laporan potensi bahaya jika menemukan risiko yang belum menimbulkan insiden, seperti lantai licin, kabel terbuka, alat rusak, jalur evakuasi terhalang, atau area tanpa rambu keselamatan.
                    </p>
                    <a href="{{ route('user.hazards.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-amber-600 px-5 py-3 text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                        Laporkan Hazard
                    </a>
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <span class="material-symbols-outlined">fact_check</span>
                    </span>
                    <p class="mt-5 text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Data Pendukung</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Sebelum mengirim</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ([
                            'Pastikan lokasi dan waktu kejadian tidak kosong.',
                            'Gunakan nomor WhatsApp yang bisa dihubungi.',
                            'Tulis kronologi dengan urutan kejadian yang jelas.',
                            'Tambahkan foto atau bukti bila tersedia dan aman diambil.',
                        ] as $check)
                            <div class="flex gap-3 rounded-[1rem] bg-[#f8fbff] p-3 ring-1 ring-slate-200">
                                <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
                                <p class="text-sm leading-6 text-slate-600">{{ $check }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="section-shell rounded-[2rem] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-white/80 lg:p-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Cek Status</p>
                        <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Memahami perkembangan laporan</h2>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">
                            Setelah laporan dikirim, sistem akan menampilkan status sesuai proses penanganan. Gunakan nomor laporan untuk mencari data dengan cepat.
                        </p>
                    </div>
                    <a href="{{ route('user.incidents.status') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-[var(--primary-color)] ring-1 ring-[var(--primary-color)]/15 transition hover:-translate-y-1">
                        <span class="material-symbols-outlined text-[20px]">timeline</span>
                        Buka Status
                    </a>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ([
                        ['label' => 'Submitted', 'text' => 'Laporan berhasil masuk ke sistem dan menunggu pengecekan awal oleh petugas.', 'color' => 'bg-amber-100 text-amber-800'],
                        ['label' => 'Verified', 'text' => 'Data laporan sudah diverifikasi dan dinilai layak untuk ditindaklanjuti.', 'color' => 'bg-emerald-100 text-emerald-800'],
                        ['label' => 'Investigating', 'text' => 'Petugas sedang melakukan pemeriksaan, koordinasi, atau tindak lanjut di lapangan.', 'color' => 'bg-sky-100 text-sky-800'],
                        ['label' => 'Resolved', 'text' => 'Masalah sudah ditangani dan menunggu penyelesaian administrasi atau penutupan.', 'color' => 'bg-indigo-100 text-indigo-700'],
                        ['label' => 'Closed', 'text' => 'Laporan sudah selesai diproses dan ditutup oleh petugas.', 'color' => 'bg-slate-200 text-slate-700'],
                        ['label' => 'Rejected', 'text' => 'Laporan tidak dapat diproses, biasanya karena data tidak sesuai atau kurang relevan.', 'color' => 'bg-rose-100 text-rose-700'],
                    ] as $status)
                        <article class="rounded-[1.25rem] bg-white p-5 ring-1 ring-slate-200">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] {{ $status['color'] }}">{{ $status['label'] }}</span>
                            <p class="mt-4 text-sm leading-7 text-slate-600">{{ $status['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Panduan Pengisian</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Contoh informasi yang baik</h2>
                    <div class="mt-6 grid gap-4">
                        @foreach ([
                            ['title' => 'Judul laporan', 'bad' => 'Ada masalah', 'good' => 'Lantai licin di koridor Gedung A lantai 2'],
                            ['title' => 'Lokasi detail', 'bad' => 'Di kampus', 'good' => 'Gedung A, lantai 2, dekat tangga sisi utara'],
                            ['title' => 'Kronologi', 'bad' => 'Tadi hampir jatuh', 'good' => 'Sekitar pukul 09.30, lantai basah tanpa tanda peringatan dan satu mahasiswa hampir terpeleset.'],
                        ] as $example)
                            <div class="rounded-[1.25rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <h3 class="font-bold text-slate-900">{{ $example['title'] }}</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-rose-50 p-4 text-sm leading-6 text-rose-700 ring-1 ring-rose-100">
                                        <span class="font-bold">Kurang jelas:</span> {{ $example['bad'] }}
                                    </div>
                                    <div class="rounded-2xl bg-emerald-50 p-4 text-sm leading-6 text-emerald-700 ring-1 ring-emerald-100">
                                        <span class="font-bold">Lebih baik:</span> {{ $example['good'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Urutan Saat Darurat</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Keselamatan tetap nomor satu</h2>
                    <div class="mt-7 space-y-4">
                        @foreach ([
                            ['title' => 'Amankan diri', 'text' => 'Jauhkan diri dari sumber bahaya sebelum mengambil foto atau membuat laporan.'],
                            ['title' => 'Minta bantuan', 'text' => 'Hubungi satgas, keamanan, dosen, teknisi, atau kontak darurat yang tersedia.'],
                            ['title' => 'Beri tanda sementara', 'text' => 'Jika memungkinkan dan aman, beri peringatan agar orang lain tidak mendekat.'],
                            ['title' => 'Laporkan setelah aman', 'text' => 'Buat laporan di SIAGA POLMAN agar kejadian tercatat dan bisa ditindaklanjuti.'],
                        ] as $index => $step)
                            <div class="flex gap-4 rounded-[1.25rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#7a2c00] text-sm font-bold text-white">{{ $index + 1 }}</span>
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $step['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $step['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="section-shell rounded-[2rem] p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-white/80 lg:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Langkah Detail</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900">Panduan penggunaan per menu</h2>
                <p class="mt-3 max-w-4xl text-sm leading-7 text-slate-600">
                    Bagian ini menjelaskan alur klik dari halaman utama sampai laporan terkirim atau informasi ditemukan.
                </p>

                <div class="mt-8 grid gap-5 lg:grid-cols-2">
                    @foreach ([
                        [
                            'icon' => 'contract_edit',
                            'title' => 'Menu Laporan',
                            'description' => 'Dipakai untuk melaporkan insiden yang sudah terjadi.',
                            'steps' => [
                                'Klik tombol Laporan pada dashboard atau tombol Lapor Insiden di navbar.',
                                'Pilih kategori laporan yang paling sesuai dengan kejadian.',
                                'Isi identitas pelapor, nomor WhatsApp, lokasi, dan waktu kejadian.',
                                'Tuliskan judul dan kronologi kejadian secara ringkas namun jelas.',
                                'Tambahkan foto atau bukti pendukung bila tersedia.',
                                'Periksa kembali data, lalu kirim laporan.',
                                'Simpan nomor laporan yang muncul setelah laporan berhasil dibuat.',
                            ],
                        ],
                        [
                            'icon' => 'warning',
                            'title' => 'Menu Potensi Bahaya',
                            'description' => 'Dipakai untuk melaporkan kondisi berisiko sebelum terjadi insiden.',
                            'steps' => [
                                'Buka menu Potensi Bahaya dari navbar atau bagian fitur pendukung.',
                                'Pilih kategori hazard yang paling menggambarkan kondisi di lapangan.',
                                'Tentukan lokasi dan berikan detail titik bahaya.',
                                'Jelaskan risiko yang mungkin terjadi bila kondisi tersebut dibiarkan.',
                                'Tambahkan foto lokasi jika aman untuk dilakukan.',
                                'Kirim laporan agar petugas dapat melakukan peninjauan.',
                            ],
                        ],
                        [
                            'icon' => 'timeline',
                            'title' => 'Menu Cek Status',
                            'description' => 'Dipakai untuk memantau perkembangan laporan yang sudah masuk.',
                            'steps' => [
                                'Klik tombol Cek Status pada dashboard atau navbar.',
                                'Gunakan kolom pencarian untuk memasukkan nomor laporan.',
                                'Jika lupa nomor laporan, cari menggunakan nama, WhatsApp, kategori, atau lokasi.',
                                'Gunakan filter status bila ingin melihat laporan dengan status tertentu.',
                                'Klik salah satu laporan untuk membuka detail dan tindak lanjutnya.',
                            ],
                        ],
                        [
                            'icon' => 'book_5',
                            'title' => 'Menu Materi K3',
                            'description' => 'Dipakai untuk membaca informasi keselamatan dan pencegahan risiko.',
                            'steps' => [
                                'Buka menu Materi K3 dari navbar.',
                                'Pilih artikel atau modul yang ingin dibaca.',
                                'Gunakan materi sebagai referensi pencegahan sebelum bekerja atau beraktivitas.',
                                'Bila menemukan risiko nyata setelah membaca panduan, buat laporan hazard atau insiden sesuai kondisi.',
                            ],
                        ],
                    ] as $menu)
                        <article class="rounded-[1.5rem] bg-white p-5 ring-1 ring-slate-200">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--blue-low-opacity)] text-[var(--primary-color)]">
                                    <span class="material-symbols-outlined">{{ $menu['icon'] }}</span>
                                </span>
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-900">{{ $menu['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $menu['description'] }}</p>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3">
                                @foreach ($menu['steps'] as $index => $step)
                                    <div class="flex gap-3 rounded-[1rem] bg-[#f8fbff] p-3 ring-1 ring-slate-200">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[var(--primary-color)] text-xs font-bold text-white">{{ $index + 1 }}</span>
                                        <p class="text-sm leading-6 text-slate-600">{{ $step }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Setelah Submit</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Apa yang terjadi setelah laporan dikirim?</h2>
                    <div class="mt-7 space-y-4">
                        @foreach ([
                            ['title' => 'Laporan tersimpan', 'text' => 'Sistem membuat nomor laporan sebagai identitas unik untuk pengecekan status.'],
                            ['title' => 'Petugas meninjau data', 'text' => 'Satgas atau admin memeriksa kelengkapan informasi, kategori, lokasi, dan tingkat urgensi.'],
                            ['title' => 'Status diperbarui', 'text' => 'Perubahan status dapat dilihat di halaman Cek Status tanpa perlu membuat laporan ulang.'],
                            ['title' => 'Tindak lanjut dicatat', 'text' => 'Jika ada penanganan lapangan, catatan tindak lanjut akan muncul pada detail laporan.'],
                        ] as $index => $item)
                            <div class="flex gap-4 rounded-[1.25rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--primary-color)] text-sm font-bold text-white">{{ $index + 1 }}</span>
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $item['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $item['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Tips Pencarian</p>
                    <h2 class="mt-3 text-2xl font-extrabold text-slate-900">Jika ingin menemukan laporan lebih cepat</h2>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['title' => 'Gunakan nomor laporan', 'text' => 'Nomor laporan adalah cara paling akurat karena setiap laporan memiliki kode yang berbeda.'],
                            ['title' => 'Cari dengan WhatsApp', 'text' => 'Jika nomor laporan hilang, gunakan nomor WhatsApp yang diisi saat membuat laporan.'],
                            ['title' => 'Cari dengan lokasi', 'text' => 'Masukkan nama gedung, ruangan, atau area kejadian bila lupa data lainnya.'],
                            ['title' => 'Filter berdasarkan status', 'text' => 'Gunakan filter untuk fokus pada laporan yang masih diajukan, diproses, selesai, atau ditolak.'],
                        ] as $tip)
                            <div class="rounded-[1.2rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                                <h3 class="font-bold text-slate-900">{{ $tip['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $tip['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="rounded-[2rem] bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.08)] ring-1 ring-slate-200 lg:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[var(--primary-color)]">Pertanyaan Umum</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900">FAQ pengguna SIAGA POLMAN</h2>
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['question' => 'Apakah harus login untuk membuat laporan?', 'answer' => 'Beberapa fitur dapat diakses publik, tetapi pengguna yang login bisa melihat riwayat dan akses yang lebih lengkap sesuai peran akun.'],
                        ['question' => 'Apa bedanya insiden dan potensi bahaya?', 'answer' => 'Insiden adalah kejadian yang sudah terjadi. Potensi bahaya adalah kondisi berisiko yang dapat menyebabkan insiden bila tidak segera ditangani.'],
                        ['question' => 'Bagaimana jika salah mengisi data?', 'answer' => 'Gunakan nomor laporan untuk mengecek detail. Jika perlu koreksi penting, hubungi petugas melalui kontak darurat atau kanal resmi kampus.'],
                        ['question' => 'Apakah semua laporan langsung selesai?', 'answer' => 'Tidak selalu. Laporan perlu diverifikasi dan diprioritaskan berdasarkan urgensi, kelengkapan data, lokasi, dan risiko keselamatan.'],
                        ['question' => 'Bolehkah mengirim foto?', 'answer' => 'Boleh, selama pengambilan foto aman dan tidak mengganggu penanganan darurat atau privasi pihak lain.'],
                        ['question' => 'Apa yang harus dilakukan saat kondisi sangat darurat?', 'answer' => 'Hubungi kontak darurat terlebih dahulu. Setelah situasi aman, buat laporan agar kejadian terdokumentasi di sistem.'],
                    ] as $faq)
                        <div class="rounded-[1.25rem] bg-[#f8fbff] p-5 ring-1 ring-slate-200">
                            <h3 class="font-bold text-slate-900">{{ $faq['question'] }}</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">{{ $faq['answer'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
@endsection
