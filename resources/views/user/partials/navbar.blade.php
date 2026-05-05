<div id="user-floating-navbar" class="fixed inset-x-0 top-0 z-[99] flex w-full justify-center px-3 pt-3 transition-all duration-300 ease-out translate-y-0 opacity-100 sm:px-4 sm:pt-4">
    <nav
        class="frosted-panel pointer-events-auto flex w-full max-w-[1360px] items-center justify-between gap-3 rounded-[1.5rem] px-3 py-3 sm:px-5 lg:rounded-[2rem]">
        <a href="{{ route('user.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <div class="logo flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white p-2 sm:h-15 sm:w-15 sm:p-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-8 w-7 sm:h-10 sm:w-9">
            </div>
            <div class="min-w-0">
                <p class="hidden text-[10px] font-semibold uppercase tracking-[0.24em] text-[var(--primary-color)]/70 sm:block lg:text-[11px] lg:tracking-[0.32em]">Portal K3L</p>
                <h2 class="truncate text-base font-bold text-[var(--primary-color)] sm:text-lg">SIAGA POLMAN</h2>
            </div>
        </a>

        <div class="hidden h-fit items-center gap-2 rounded-2xl bg-[var(--blue-low-opacity)] px-2 py-2 lg:flex">
            <a href="{{ route('user.dashboard') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.dashboard') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">home_app_logo</span>
                <span class="font-bold text-[var(--primary-color)]">Beranda</span>
            </a>
            <a href="{{ route('user.incidents.status') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.incidents.status') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">timeline</span>
                <span class="font-bold text-[var(--primary-color)]">Cek Status</span>
            </a>
            <a href="{{ route('user.knowledge.index') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.knowledge.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">book_5</span>
                <span class="font-bold text-[var(--primary-color)]">Materi K3</span>
            </a>
            <a href="{{ route('user.hazards.map') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.hazards.map') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">map</span>
                <span class="font-bold text-[var(--primary-color)]">Peta</span>
            </a>
            <a href="{{ route('user.emergency.index') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('user.emergency.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">emergency_home</span>
                <span class="font-bold text-[var(--primary-color)]">Darurat</span>
            </a>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('user.incidents.create') }}"
                class="hidden items-center gap-2 rounded-full bg-[var(--primary-color)] px-4 py-3 text-sm font-bold text-white shadow-[0_15px_30px_rgba(10,77,179,0.22)] transition hover:bg-[var(--primary-deep)] sm:inline-flex">
                <span class="material-symbols-outlined text-[20px]">contract_edit</span>
                Lapor Insiden
            </a>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                    @csrf
                    <button type="submit"
                        class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-rose-600 transition hover:bg-rose-50"
                        title="Keluar">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            @endauth

            <details class="group relative lg:hidden">
                <summary
                    class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl transition hover:bg-[var(--blue-low-opacity)] [&::-webkit-details-marker]:hidden">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">menu</span>
                </summary>

                <div class="ambient-card absolute right-0 top-14 w-[min(18rem,calc(100vw-2rem))] rounded-3xl p-3 shadow-all ring-1 ring-slate-100">
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Menu</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">home_app_logo</span>
                            Beranda
                        </a>
                        <a href="{{ route('user.incidents.status') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">timeline</span>
                            Cek Status
                        </a>
                        <a href="{{ route('user.incidents.create') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">contract_edit</span>
                            Lapor Insiden
                        </a>
                        <a href="{{ route('user.hazards.create') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">warning</span>
                            Potensi Bahaya
                        </a>
                        <a href="{{ route('user.hazards.map') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">map</span>
                            Peta Hazard
                        </a>
                        <a href="{{ route('user.knowledge.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">book_5</span>
                            Materi K3
                        </a>
                        <a href="{{ route('user.emergency.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)]">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">emergency_home</span>
                            Pusat Darurat
                        </a>
                        @auth
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                    <span class="material-symbols-outlined">logout</span>
                                    Keluar
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </details>
        </div>
    </nav>
</div>

@push('scripts')
    <script>
        (() => {
            const navbar = document.getElementById('user-floating-navbar');

            if (!navbar) {
                return;
            }

            let lastY = window.scrollY;
            let idleTimer = null;
            let isHidden = false;
            const scrollThreshold = 12;
            const topRevealOffset = 32;
            const idleDelay = 320;

            const showNavbar = () => {
                if (!isHidden) {
                    return;
                }

                navbar.classList.remove('-translate-y-[140%]', 'opacity-0');
                navbar.classList.add('translate-y-0', 'opacity-100');
                isHidden = false;
            };

            const hideNavbar = () => {
                if (isHidden) {
                    return;
                }

                navbar.classList.remove('translate-y-0', 'opacity-100');
                navbar.classList.add('-translate-y-[140%]', 'opacity-0');
                isHidden = true;
            };

            const armIdleReveal = () => {
                window.clearTimeout(idleTimer);
                idleTimer = window.setTimeout(() => {
                    showNavbar();
                }, idleDelay);
            };

            navbar.classList.add('translate-y-0', 'opacity-100');
            armIdleReveal();

            window.addEventListener('scroll', () => {
                const currentY = window.scrollY;
                const delta = currentY - lastY;

                if (currentY <= topRevealOffset) {
                    showNavbar();
                    lastY = currentY;
                    armIdleReveal();
                    return;
                }

                if (delta > scrollThreshold) {
                    hideNavbar();
                } else if (delta < -scrollThreshold) {
                    showNavbar();
                }

                lastY = currentY;
                armIdleReveal();
            }, { passive: true });

            ['mouseenter', 'focusin', 'touchstart'].forEach((eventName) => {
                navbar.addEventListener(eventName, () => {
                    showNavbar();
                    armIdleReveal();
                }, { passive: true });
            });
        })();
    </script>
@endpush
