<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard Pengguna')</title>
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

        .title-pelaporan {
            background: linear-gradient(135deg, var(--green), var(--dark-green));
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-deep), var(--green), #0fa3a7);
        }

        .shadow-all {
            box-shadow: 0 0 45px rgba(15, 23, 42, 0.08);
        }

        .icon-medium {
            font-size: 45px;
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
    <div class="relative flex min-h-screen w-full flex-col items-center overflow-x-hidden">
        @include('user.partials.navbar')

        @yield('page')
    </div>
    @include('partials.delete-confirm-modal')
    @stack('scripts')
</body>

</html>
