@php
    $mainLinks = [
        ['route' => 'satgas.dashboard', 'active' => ['satgas.dashboard'], 'icon' => 'home_app_logo', 'label' => 'Dashboard'],
        ['route' => 'satgas.incidents.index', 'active' => ['satgas.incidents.index', 'satgas.incidents.show', 'satgas.incidents.create'], 'icon' => 'fact_check', 'label' => 'Review Insiden'],
        ['route' => 'satgas.hazards.index', 'active' => ['satgas.hazards.index', 'satgas.hazards.show', 'satgas.hazards.create'], 'icon' => 'warning', 'label' => 'Review Hazard'],
    ];

    $dropdownGroups = [
        [
            'title' => 'Pemetaan & Materi',
            'links' => [
                ['route' => 'satgas.hazards.map', 'active' => ['satgas.hazards.map'], 'icon' => 'map', 'label' => 'GIS Hazard'],
                ['route' => 'satgas.incidents.gis', 'active' => ['satgas.incidents.gis', 'satgas.incidents.gis.export'], 'icon' => 'satellite_alt', 'label' => 'GIS Insiden'],
                ['route' => 'satgas.knowledge-articles.index', 'active' => ['satgas.knowledge-articles.*'], 'icon' => 'menu_book', 'label' => 'Knowledge'],
            ],
        ],
        [
            'title' => 'Aksi Satgas',
            'links' => [
                ['route' => 'satgas.profile.show', 'active' => ['satgas.profile.*'], 'icon' => 'account_circle', 'label' => 'Profil Satgas'],
                ['route' => 'satgas.incidents.create', 'active' => ['satgas.incidents.create'], 'icon' => 'note_add', 'label' => 'Buat Laporan Insiden'],
                ['route' => 'satgas.hazards.create', 'active' => ['satgas.hazards.create'], 'icon' => 'add_alert', 'label' => 'Buat Hazard Report'],
                ['route' => 'satgas.knowledge-articles.create', 'active' => ['satgas.knowledge-articles.create'], 'icon' => 'edit_square', 'label' => 'Tambah Materi'],
            ],
        ],
    ];

    $isActive = fn (array $patterns): bool => collect($patterns)->contains(fn ($pattern) => request()->routeIs($pattern));
    $dropdownIsActive = collect($dropdownGroups)
        ->flatMap(fn ($group) => $group['links'])
        ->contains(fn ($link) => $isActive($link['active']));
@endphp

<div id="satgas-floating-navbar" class="fixed inset-x-0 top-0 z-[99] flex w-full justify-center px-3 pt-3 transition-all duration-300 ease-out translate-y-0 opacity-100 sm:px-4 sm:pt-4">
    <nav class="frosted-panel pointer-events-auto relative flex w-full max-w-[1360px] items-center justify-between gap-3 rounded-[1.5rem] px-3 py-3 sm:px-5 lg:rounded-[2rem]">
        <a href="{{ route('satgas.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <div class="logo flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white p-2 sm:h-15 sm:w-15 sm:p-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-8 w-7 sm:h-10 sm:w-9">
            </div>
            <div class="min-w-0">
                <p class="hidden text-[10px] font-semibold uppercase tracking-[0.24em] text-[var(--primary-color)]/70 sm:block lg:text-[11px] lg:tracking-[0.32em]">Portal K3L</p>
                <h2 class="truncate text-base font-bold text-[var(--primary-color)] sm:text-lg">SIAGA POLMAN</h2>
            </div>
        </a>

        <div class="hidden h-fit items-center gap-2 rounded-2xl bg-[var(--blue-low-opacity)] px-2 py-2 lg:flex">
            @foreach ($mainLinks as $link)
                <a href="{{ route($link['route']) }}"
                    class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ $isActive($link['active']) ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">{{ $link['icon'] }}</span>
                    <h6 class="font-bold text-[var(--primary-color)]">{{ $link['label'] }}</h6>
                </a>
            @endforeach
        </div>

        <div class="hidden items-center justify-center gap-3 lg:flex">
            <details class="group relative">
                <summary class="{{ $dropdownIsActive ? 'bg-white shadow-sm' : 'hover:bg-[var(--blue-low-opacity)]' }} flex h-12 w-12 cursor-pointer list-none items-center justify-center rounded-xl transition [&::-webkit-details-marker]:hidden">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">more_vert</span>
                </summary>

                <div class="ambient-card absolute right-0 top-16 w-[42rem] max-w-[calc(100vw-2rem)] rounded-3xl p-4 shadow-all ring-1 ring-slate-100">
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Menu Satgas</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($dropdownGroups as $group)
                            <div class="min-w-0 rounded-2xl bg-white/60 p-2 ring-1 ring-slate-100">
                                <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $group['title'] }}</p>
                                <div class="grid gap-1.5">
                                    @foreach ($group['links'] as $link)
                                        <a href="{{ route($link['route']) }}"
                                            class="flex min-h-12 items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ $isActive($link['active']) ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                                            <span class="material-symbols-outlined shrink-0 text-[22px] text-[var(--primary-color)]">{{ $link['icon'] }}</span>
                                            <span class="min-w-0 leading-5">{{ $link['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="col-span-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex min-h-12 w-full items-center justify-center gap-3 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-100">
                                    <span class="material-symbols-outlined text-[21px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </details>

            <span class="h-13 w-px rounded-full bg-slate-300"></span>
            <a href="{{ route('satgas.profile.show') }}" class="flex h-15 w-auto items-center gap-3 rounded-full bg-[var(--primary-color)] px-3 py-3 shadow-[0_15px_30px_rgba(10,77,179,0.24)]">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 font-bold text-white">{{ $initials !== '' ? $initials : 'S' }}</span>
                <div class="text-left">
                    <h6 class="font-bold text-white">{{ $user->name }}</h6>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ $user->role?->name ?? 'Satgas' }}</p>
                </div>
            </a>
        </div>

        <button type="button"
            id="satgas-mobile-menu-toggle"
            class="flex h-11 w-11 items-center justify-center rounded-xl text-[var(--primary-color)] transition hover:bg-[var(--blue-low-opacity)] lg:hidden"
            aria-controls="satgas-mobile-menu-panel"
            aria-expanded="false"
            aria-label="Buka menu satgas">
            <span id="satgas-mobile-menu-icon" class="material-symbols-outlined">menu</span>
        </button>

        <div id="satgas-mobile-menu-panel"
            class="ambient-card hidden absolute overflow-y-auto rounded-3xl p-4 shadow-all ring-1 ring-slate-100 lg:hidden"
            style="left: 0; right: 0; top: calc(100% + 0.75rem); max-height: calc(100vh - 5.5rem);">
            <div class="mb-3 flex items-center justify-between gap-3 rounded-2xl bg-[var(--primary-color)] px-4 py-3 text-white">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/70">Satgas</p>
                    <p class="mt-1 truncate text-sm font-bold">{{ $user->name }}</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold">{{ $initials !== '' ? $initials : 'S' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="min-w-0">
                    <p class="mb-2 px-4 text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-400">Navigasi</p>
                    <div class="flex flex-col gap-1.5">
                        @foreach ($mainLinks as $link)
                            <a href="{{ route($link['route']) }}"
                                class="flex min-h-12 items-center gap-3 rounded-2xl px-4 py-2 text-xs font-bold leading-4 text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ $isActive($link['active']) ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : 'bg-white/70' }}">
                                <span class="material-symbols-outlined shrink-0 text-[21px] text-[var(--primary-color)]">{{ $link['icon'] }}</span>
                                <span class="min-w-0 break-words">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="min-w-0">
                    <p class="mb-2 px-4 text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-400">Aksi</p>
                    <div class="flex flex-col gap-1.5">
                        @foreach ($dropdownGroups as $group)
                            @foreach ($group['links'] as $link)
                                <a href="{{ route($link['route']) }}"
                                    class="flex min-h-12 items-center gap-3 rounded-2xl bg-white/70 px-4 py-2 text-xs font-bold leading-4 text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ $isActive($link['active']) ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                                    <span class="material-symbols-outlined shrink-0 text-[21px] text-[var(--primary-color)]">{{ $link['icon'] }}</span>
                                    <span class="min-w-0 break-words">{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                        @endforeach

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex min-h-12 w-full items-center gap-3 rounded-2xl bg-rose-50 px-4 py-2 text-left text-xs font-bold leading-4 text-rose-600 transition hover:bg-rose-100">
                                <span class="material-symbols-outlined shrink-0 text-[21px]">logout</span>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>

@push('scripts')
    <script>
        (() => {
            const navbar = document.getElementById('satgas-floating-navbar');

            if (!navbar) {
                return;
            }

            let lastY = window.scrollY;
            let idleTimer = null;
            let isHidden = false;

            const mobileToggle = document.getElementById('satgas-mobile-menu-toggle');
            const mobilePanel = document.getElementById('satgas-mobile-menu-panel');
            const mobileIcon = document.getElementById('satgas-mobile-menu-icon');

            const mobileMenuIsOpen = () => mobilePanel ? !mobilePanel.classList.contains('hidden') : false;
            const menuIsOpen = () => mobileMenuIsOpen() || Array.from(navbar.querySelectorAll('details')).some((detail) => detail.open);

            const setMobileMenuOpen = (isOpen) => {
                if (!mobileToggle || !mobilePanel || !mobileIcon) {
                    return;
                }

                mobilePanel.classList.toggle('hidden', !isOpen);
                mobileIcon.textContent = isOpen ? 'close' : 'menu';
                mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (isOpen) {
                    navbar.querySelectorAll('details[open]').forEach((detail) => {
                        detail.open = false;
                    });
                    showNavbar();
                }
            };

            const showNavbar = () => {
                if (!isHidden) return;
                navbar.classList.remove('-translate-y-[140%]', 'opacity-0');
                navbar.classList.add('translate-y-0', 'opacity-100');
                isHidden = false;
            };

            const hideNavbar = () => {
                if (isHidden || menuIsOpen()) return;
                navbar.classList.remove('translate-y-0', 'opacity-100');
                navbar.classList.add('-translate-y-[140%]', 'opacity-0');
                isHidden = true;
            };

            const armIdleReveal = () => {
                window.clearTimeout(idleTimer);
                idleTimer = window.setTimeout(showNavbar, 320);
            };

            navbar.classList.add('translate-y-0', 'opacity-100');
            armIdleReveal();

            window.addEventListener('scroll', () => {
                const currentY = window.scrollY;
                const delta = currentY - lastY;

                if (currentY <= 32 || menuIsOpen()) {
                    showNavbar();
                    lastY = currentY;
                    armIdleReveal();
                    return;
                }

                if (delta > 12) {
                    hideNavbar();
                } else if (delta < -12) {
                    showNavbar();
                }

                lastY = currentY;
                armIdleReveal();
            }, { passive: true });

            navbar.addEventListener('toggle', (event) => {
                if (!(event.target instanceof HTMLDetailsElement) || !event.target.open) {
                    return;
                }

                setMobileMenuOpen(false);
                navbar.querySelectorAll('details').forEach((detail) => {
                    if (detail !== event.target) {
                        detail.open = false;
                    }
                });
                showNavbar();
            }, true);

            if (mobileToggle) {
                mobileToggle.addEventListener('click', () => {
                    setMobileMenuOpen(!mobileMenuIsOpen());
                    armIdleReveal();
                });
            }

            document.addEventListener('click', (event) => {
                if (navbar.contains(event.target)) {
                    return;
                }

                setMobileMenuOpen(false);
                navbar.querySelectorAll('details[open]').forEach((detail) => {
                    detail.open = false;
                });
            });

            navbar.querySelectorAll('#satgas-mobile-menu-panel a').forEach((link) => {
                link.addEventListener('click', () => {
                    setMobileMenuOpen(false);
                });
            });

            ['mouseenter', 'focusin', 'touchstart'].forEach((eventName) => {
                navbar.addEventListener(eventName, () => {
                    showNavbar();
                    armIdleReveal();
                }, { passive: true });
            });
        })();
    </script>
@endpush
