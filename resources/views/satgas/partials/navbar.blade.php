<div id="satgas-floating-navbar" class="fixed inset-x-0 top-0 z-[99] flex w-full justify-center px-4 pt-4 transition-all duration-300 ease-out translate-y-0 opacity-100">
    <nav
        class="frosted-panel pointer-events-auto flex w-full max-w-[1360px] flex-wrap items-center justify-between gap-4 rounded-[2rem] px-4 py-3 sm:px-5">
        <div class="flex items-center gap-4">
            <div class="logo flex h-15 w-15 items-center justify-center rounded-full bg-white p-3">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-9">
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[var(--primary-color)]/70">Portal K3L</p>
                <h2 class="text-lg font-bold text-[var(--primary-color)]">SIAGA POLMAN</h2>
            </div>
        </div>

        <div class="flex h-fit flex-wrap items-center gap-2 rounded-2xl bg-[var(--blue-low-opacity)] px-2 py-2">
            <a href="{{ route('satgas.dashboard') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('satgas.dashboard') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">home_app_logo</span>
                <h6 class="font-bold text-[var(--primary-color)]">Dashboard</h6>
            </a>

            <a href="{{ route('satgas.incidents.index') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('satgas.incidents.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">fact_check</span>
                <h6 class="font-bold text-[var(--primary-color)]">Review Insiden</h6>
            </a>

            <a href="{{ route('satgas.hazards.index') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('satgas.hazards.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">warning</span>
                <h6 class="font-bold text-[var(--primary-color)]">Review Hazard</h6>
            </a>

            <a href="{{ route('satgas.knowledge-articles.index') }}"
                class="flex flex-row items-center gap-2 rounded-xl px-4 py-3 text-sm transition {{ request()->routeIs('satgas.knowledge-articles.*') ? 'bg-white shadow-sm' : 'hover:bg-white/80' }}">
                <span class="material-symbols-outlined text-[var(--primary-color)]">menu_book</span>
                <h6 class="font-bold text-[var(--primary-color)]">Knowledge</h6>
            </a>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <details class="group relative">
                <summary
                    class="list-none [&::-webkit-details-marker]:hidden flex h-12 w-12 items-center justify-center rounded-xl cursor-pointer transition hover:bg-[var(--blue-low-opacity)]">
                    <span class="material-symbols-outlined text-[var(--primary-color)]">more_vert</span>
                </summary>

                <div class="ambient-card absolute right-0 top-16 w-72 rounded-3xl p-3 shadow-all ring-1 ring-slate-100">
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Menu Satgas</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <a href="{{ route('satgas.incidents.create') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('satgas.incidents.create') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">note_add</span>
                            Buat Laporan Insiden
                        </a>

                        <a href="{{ route('satgas.hazards.create') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('satgas.hazards.create') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">add_alert</span>
                            Buat Hazard Report
                        </a>

                        <a href="{{ route('satgas.knowledge-articles.create') }}"
                            class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[var(--blue-low-opacity)] {{ request()->routeIs('satgas.knowledge-articles.create') ? 'bg-[var(--blue-low-opacity)] text-[var(--primary-color)]' : '' }}">
                            <span class="material-symbols-outlined text-[var(--primary-color)]">edit_square</span>
                            Tambah Materi
                        </a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                <span class="material-symbols-outlined">logout</span>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </details>

            <span class="hidden h-13 w-px rounded-full bg-slate-300 sm:block"></span>
            <div
                class="flex h-15 w-auto items-center gap-3 rounded-full bg-[var(--primary-color)] px-3 py-3 shadow-[0_15px_30px_rgba(10,77,179,0.24)]">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 font-bold text-white">{{ $initials !== '' ? $initials : 'S' }}</span>
                <div class="text-left">
                    <h6 class="font-bold text-white">{{ $user->name }}</h6>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ $user->role?->name ?? 'Satgas' }}</p>
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

            const showNavbar = () => {
                if (!isHidden) return;
                navbar.classList.remove('-translate-y-[140%]', 'opacity-0');
                navbar.classList.add('translate-y-0', 'opacity-100');
                isHidden = false;
            };

            const hideNavbar = () => {
                if (isHidden) return;
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

                if (currentY <= 32) {
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
            }, {
                passive: true
            });

            ['mouseenter', 'focusin', 'touchstart'].forEach((eventName) => {
                navbar.addEventListener(eventName, () => {
                    showNavbar();
                    armIdleReveal();
                }, {
                    passive: true
                });
            });
        })();
    </script>
@endpush
