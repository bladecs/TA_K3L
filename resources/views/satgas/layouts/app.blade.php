<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard Satgas')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
    <style>
        :root {
            --primary-color: #0a4db3;
            --primary-deep: #072d70;
            --blue-low-opacity: rgba(196, 215, 245, 0.58);
            --orange: #ef6a22;
            --green: #159947;
            --dark-green: #0d5b2c;
            --red: #d93f33;
            --yellow: #e7aa14;
            --surface: #f4f7fb;
            --surface-strong: #e7eef8;
            --ink-soft: #5f6b7d;
            --header-overlay: linear-gradient(135deg, rgba(7, 45, 112, 0.92), rgba(10, 77, 179, 0.74), rgba(239, 106, 34, 0.46));
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(231, 170, 20, 0.15), transparent 26%),
                radial-gradient(circle at top right, rgba(10, 77, 179, 0.16), transparent 30%),
                linear-gradient(180deg, #eef4fb 0%, #f8fafd 22%, #f3f6fb 100%);
        }

        #header {
            background: url('/img/background.jpeg') no-repeat center 150% fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        #header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--header-overlay);
            z-index: 0;
        }

        #header::after {
            content: "";
            position: absolute;
            inset: auto -10% -18rem auto;
            width: 28rem;
            height: 28rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.12);
            filter: blur(10px);
            z-index: 0;
        }

        .logo {
            box-shadow: 0 10px 30px rgba(10, 77, 179, 0.18);
        }

        .shadow-all {
            box-shadow: 0 0 45px rgba(15, 23, 42, 0.08);
        }

        .frosted-panel {
            backdrop-filter: blur(18px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        .ambient-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(245, 248, 252, 0.98)),
                radial-gradient(circle at top right, rgba(10, 77, 179, 0.08), transparent 35%);
        }

        .section-shell {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 249, 252, 0.98)),
                radial-gradient(circle at top left, rgba(231, 170, 20, 0.08), transparent 28%);
        }
    </style>
</head>

<body class="bg-gray-50 text-slate-900">
    @php
        $user = auth()->user();
        $initials = collect(preg_split('/\s+/', trim($user->name)) ?: [])
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div class="relative flex min-h-screen w-full flex-col items-center overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[34rem] bg-gradient-to-b from-[#dce9fb] via-[#eef4fb] to-transparent"></div>
        @include('satgas.partials.navbar', ['user' => $user, 'initials' => $initials])

        <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6 pt-30">
            <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
            <div class="relative z-1 flex max-w-6xl flex-col items-center">
                <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                    @yield('hero_eyebrow', 'Panel Satgas K3L')
                </span>
                <h2 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">
                    @yield('hero_title', 'Pusat Operasional Satgas K3L')
                </h2>
                <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                    @yield('hero_description', 'Kelola verifikasi, tindak lanjut, hazard, dan materi edukasi dari satu ruang kerja yang konsisten.')
                </p>
            </div>
        </header>

        <main class="w-full bg-[#f6f8fc] pb-16">
            <section class="w-full px-4 lg:px-8">
                <div class="mx-auto -mt-10 flex w-full max-w-[1600px] flex-col gap-6 pt-20 lg:-mt-14 lg:pt-25">
                    @include('partials.flash')
                    @yield('content')
                </div>
            </section>
        </main>
    </div>
    @include('partials.delete-confirm-modal')
    @stack('scripts')
</body>

</html>
