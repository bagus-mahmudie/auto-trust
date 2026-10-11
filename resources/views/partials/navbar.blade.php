<nav class="bg-white" x-data="mobileDrawerNav()">
    {{-- TOP BAR --}}
    <div
        id="topbar"
        class="fixed inset-x-0 top-0 z-50 h-16 bg-white transform transition-transform duration-300 will-change-transform"
    >
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 h-full">
            <div class="flex h-full items-center justify-between">

                {{-- LEFT: hamburger (mobile) --}}
                <button type="button"
                        class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-slate-100 transition"
                        aria-label="Open menu"
                        @click="openDrawer()">
                    <i class="bi bi-list text-2xl text-slate-700"></i>
                </button>

                {{-- LOGO --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/icons/logo-navbar.png') }}" class="h-8 w-8" alt="Logo">
                    <span class="text-2xl sm:text-3xl font-semibold text-slate-900">AutoTrust</span>
                </a>

                {{-- RIGHT: auth + jual --}}
                <div class="flex items-center gap-4 sm:gap-6">

                    {{-- DESKTOP ONLY: auth button/dropdown --}}
                    <div class="hidden lg:flex items-center gap-3">
                        @guest
                            <button type="button"
                                    class="text-base text-slate-700 hover:text-slate-900"
                                    @click="
                                        fetch('{{ route('auth.intended') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ url: window.location.href })
                                        }).finally(() => $store.authModal.open('login'))
                                    ">
                                Masuk / Daftar
                            </button>
                        @endguest

                        @auth

                        {{-- ICON BUTTONS: Chat / Like / Notif --}}
                            <div class="flex items-center gap-3">
                                {{-- Chat --}}
                                <a href="{{ route('chat.index') }}"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-50 transition"
                                title="Chat">
                                    <i class="bi bi-chat text-slate-600 text-lg"></i>
                                </a>

                                {{-- Like (placeholder, ganti route kalau sudah ada fitur) --}}
                                <a href="#"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-50 transition"
                                title="Like">
                                    <i class="bi bi-heart text-slate-600 text-lg"></i>
                                </a>

                                {{-- Notif (placeholder) --}}
                                <a href="#"
                                class="relative inline-flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-50 transition"
                                title="Notifikasi">
                                    <i class="bi bi-bell text-slate-600 text-lg"></i>

                                    {{-- Badge contoh (kalau nanti ada count) --}}
                                    {{-- <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-semibold text-white">3</span> --}}
                                </a>

                                {{-- Divider --}}
                                <span class="mx-1 h-8 w-px bg-slate-200"></span>
                            </div>


                            <div class="relative" x-data="{ open:false }" @click.outside="open=false" @keydown.escape.window="open=false">
                                {{-- trigger --}}
                                <button type="button"
                                    class="flex items-center gap-4 rounded-2xl px-3 py-2 hover:bg-slate-50 transition"
                                    @click="open=!open"
                                >
                                    @php
                                        $u = auth()->user();
                                        $avatar = $u->foto_profile
                                            ? asset('storage/'.$u->foto_profile)
                                            : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=E2E8F0&color=0F172A';
                                    @endphp

                                    <img src="{{ $avatar }}" class="h-11 w-11 rounded-full object-cover" alt="avatar" />

                                    <div class="text-left leading-tight hidden sm:block">
                                        <div class="text-base font-semibold text-slate-900">{{ $u->name }}</div>
                                        <div class="text-sm text-slate-500">{{ $u->email }}</div>
                                    </div>

                                    <i class="bi bi-chevron-down text-slate-500"></i>
                                </button>

                                {{-- dropdown --}}
                                <div
                                    x-show="open"
                                    x-transition.opacity.scale.origin.top.right
                                    class="absolute right-0 mt-3 w-[320px] rounded-3xl bg-white shadow-[0_20px_60px_rgba(2,6,23,0.18)] ring-1 ring-slate-200 overflow-hidden z-[99999]"
                                    x-cloak
                                >
                                    <div class="px-5 pt-4 pb-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="avatar" />
                                            <div class="min-w-0">
                                                <div class="text-base font-semibold text-slate-900 truncate">{{ $u->name }}</div>
                                                <div class="text-sm text-slate-500 truncate">{{ $u->email }}</div>
                                            </div>
                                        </div>

                                        <a href="{{ route('settings.edit') }}"
                                           class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#51A3EE] px-4 py-2.5 text-sm font-medium text-white hover:bg-sky-600 transition">
                                            <i class="bi bi-pencil-square"></i>
                                            Lihat dan edit profil
                                        </a>
                                    </div>

                                    <div class="h-px bg-slate-100"></div>

                                    <div class="p-2">
                                        <a href="{{ route('seller.me') }}?tab=iklan"
                                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">
                                            <i class="bi bi-megaphone text-slate-500"></i>
                                            Iklan saya
                                        </a>

                                        <a href="{{ route('posting.kebutuhan') }}"
                                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">
                                            <i class="bi bi-plus-circle text-slate-500"></i>
                                            Posting Kebutuhan
                                        </a>

                                        <div class="my-2 h-px bg-slate-100"></div>

                                        <a href="#"
                                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">
                                            <i class="bi bi-question-circle text-slate-500"></i>
                                            Bantuan
                                        </a>

                                        <a href="#"
                                           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">
                                            <i class="bi bi-gear text-slate-500"></i>
                                            Pengaturan
                                        </a>

                                        <form method="POST" action="{{ route('auth.logout') }}" class="px-0 pb-0">
                                            @csrf
                                            <button type="submit"
                                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">
                                                <i class="bi bi-box-arrow-right text-slate-500"></i>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>

                    {{-- + Jual (selalu tampil, sesuai gambar) --}}
                    @auth
                        <a href="{{ route('mobil.create') }}"
                        class="inline-flex items-center gap-1 rounded-full bg-[#51A3EE] px-4 sm:px-5 py-2 text-sm sm:text-base font-medium text-white hover:bg-sky-600 transition">
                            <span class="inline-flex h-6 w-6 items-center text-2xl justify-center">
                                <i class="bi bi-plus"></i>
                            </span>
                            <span class="inline">Jual</span>
                            <span class="hidden sm:inline">Mobil</span>
                        </a>
                    @else
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full bg-[#51A3EE] px-4 sm:px-5 py-2 text-sm sm:text-base font-medium text-white hover:bg-sky-600 transition"
                            @click="window.dispatchEvent(
                                new CustomEvent('open-auth-modal', { detail: { tab: 'login' } })
                            )"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center">
                                <i class="bi bi-plus"></i>
                            </span>
                            <span class="inline">Jual</span>
                            <span class="hidden sm:inline">Mobil</span>
                        </button>
                    @endauth

                </div>

            </div>
        </div>
        <div class="h-px bg-slate-100"></div>
    </div>

    {{-- SPACER topbar --}}
    <div class="h-16"></div>

    {{-- LOWERBAR (desktop only) --}}
    <div
        id="lowerbar"
        class="fixed inset-x-0 top-0 z-40 bg-[#fafafa] translate-y-16 transition-transform duration-300 shadow-md will-change-transform hidden lg:block"
    >
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6">
            <div class="flex h-12 items-center justify-between text-base">

                {{-- NAVLINKS (sesuai gambar) --}}
                <div class="flex items-center gap-8 text-slate-600">

                    {{-- Beli dropdown only --}}
                    <div class="relative" x-data="{ open:false }" @click.outside="open=false" @keydown.escape.window="open=false">
                        <button type="button"
                                class="inline-flex items-center gap-2 hover:text-slate-900"
                                @click="open=!open">
                            <span>Beli</span>
                            <i class="bi bi-chevron-down text-sm"></i>
                        </button>

                        <div x-show="open" x-cloak
                             x-transition.opacity.scale.origin.top.left
                             class="absolute left-0 mt-3 w-56 rounded-2xl bg-white shadow-[0_20px_60px_rgba(2,6,23,0.18)] ring-1 ring-slate-200 overflow-hidden">
                            <a href="{{ route('rekomendasi.mobil', ['kondisi' => 'baru']) }}" class="block px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50 ">Mobil Baru</a>
                            <a href="{{ route('rekomendasi.mobil', ['kondisi' => 'bekas']) }}" class="block px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">Mobil Bekas</a>
                            <a href="{{ route('rekomendasi.motor', ['kondisi' => 'baru']) }}" class="block px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">Motor Baru</a>
                            <a href="{{ route('rekomendasi.motor', ['kondisi' => 'bekas']) }}" class="block px-4 py-3 text-base text-slate-700 hover:text-[#51A3EE] hover:bg-slate-50">Motor Bekas</a>

                        </div>
                    </div>

                    <a href="{{ route('sewa.index') }}" class="hover:text-slate-900">Sewa</a>
                    <a href="{{ route('tentang-kami') }}" class="hover:text-slate-900">Tentang kami</a>
                    <a href="{{ route('artikel.index') }}" class="hover:text-slate-900">Artikel</a>
                </div>

                {{-- location --}}
                @auth
                    <div class="inline-flex items-center gap-2 text-slate-600">
                        <i class="bi bi-geo-alt"></i>

                        @php
                            $lokasiUser = trim(auth()->user()->lokasi ?? '');
                            $lokasiTampil = $lokasiUser !== '' ? $lokasiUser . ', Indonesia' : 'Indonesia';
                        @endphp

                        <span>{{ $lokasiTampil }}</span>
                    </div>
                @endauth
            </div>
        </div>
        <div class="h-px bg-slate-100"></div>
    </div>

    {{-- SPACER lowerbar (desktop only) --}}
    <div class="hidden lg:block h-12"></div>

    {{-- ✅ MOBILE DRAWER (sesuai gambar ke-2/3/4) --}}
    <div>
        {{-- overlay --}}
        <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-[60] bg-black/30" @click="closeDrawer()"></div>

        {{-- panel --}}
        <aside x-show="drawerOpen" x-cloak
               class="fixed inset-y-0 left-0 z-[70] w-[320px] max-w-[86vw] bg-white shadow-2xl"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">

            <div class="h-full flex flex-col">

                {{-- top: logo + close --}}
                <div class="px-5 pt-4 flex items-start justify-between">
                    <img src="{{ asset('assets/img/icons/logo-navbar.png') }}" class="h-8 w-8" alt="logo">
                    <button type="button"
                            class="h-10 w-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50 transition"
                            @click="closeDrawer()">
                        <i class="bi bi-x text-xl"></i>
                    </button>
                </div>

                {{-- header auth --}}
                <div class="px-5 pt-4">
                    @guest
                        <div class="text-center text-sm text-slate-600 mb-3">Daftar sebagai Dealer</div>

                        <div class="rounded-2xl bg-slate-100 p-1 flex items-center">
                            <button type="button"
                                    class="flex-1 rounded-xl bg-[#51A3EE] text-white text-sm font-semibold py-2.5 text-center"
                                    @click="
                                        closeDrawer();
                                        fetch('{{ route('auth.intended') }}', {
                                            method:'POST',
                                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                            body: JSON.stringify({ url: window.location.href })
                                        }).finally(() => $store.authModal.open('login'));
                                    ">
                                Masuk
                            </button>

                            <button type="button"
                                    class="flex-1 rounded-xl text-slate-600 text-sm font-semibold py-2.5 text-center"
                                    @click="
                                        closeDrawer();
                                        fetch('{{ route('auth.intended') }}', {
                                            method:'POST',
                                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                                            body: JSON.stringify({ url: window.location.href })
                                        }).finally(() => $store.authModal.open('register'));
                                    ">
                                Daftar
                            </button>
                        </div>
                    @endguest

                    @auth
                        @php
                            $u = auth()->user();
                            $avatar = $u->foto_profile
                                ? asset('storage/'.$u->foto_profile)
                                : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=E2E8F0&color=0F172A';
                        @endphp

                        <button type="button"
                                class="w-full flex items-center justify-between gap-3"
                                @click="accountOpen = !accountOpen">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $avatar }}" class="h-10 w-10 rounded-full object-cover bg-slate-100" alt="avatar">
                                <div class="min-w-0 text-left">
                                    <div class="font-semibold text-slate-900 truncate">{{ $u->name }}</div>
                                    <div class="text-sm text-slate-500 truncate">{{ $u->email }}</div>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down text-slate-400 transition" :class="accountOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="accountOpen" x-cloak class="mt-4 space-y-3">
                            <a href="{{ route('settings.edit') }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#51A3EE] px-4 py-3 text-base font-semibold text-white hover:bg-sky-600 transition">
                                <i class="bi bi-pencil-square"></i>
                                Lihat dan edit profil
                            </a>

                            <div class="space-y-2">
                                <a href="{{ route('seller.me') }}?tab=iklan"
                                   class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                                    <i class="bi bi-megaphone text-slate-500"></i>
                                    <span class="font-medium">Iklan saya</span>
                                </a>

                                <a href="{{ route('posting.kebutuhan') }}"
                                   class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                                    <i class="bi bi-plus-circle text-slate-500"></i>
                                    <span class="font-medium">Posting Kebutuhan</span>
                                </a>

                                <div class="my-1 h-px bg-slate-100"></div>

                                <a href="#"
                                   class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                                    <i class="bi bi-question-circle text-slate-500"></i>
                                    <span class="font-medium">Bantuan</span>
                                </a>

                                <a href="#"
                                   class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                                    <i class="bi bi-gear text-slate-500"></i>
                                    <span class="font-medium">Pengaturan</span>
                                </a>

                                <form method="POST" action="{{ route('auth.logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                                        <i class="bi bi-box-arrow-right text-slate-500"></i>
                                        <span class="font-medium">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- drawer nav list --}}
                <nav class="px-5 pt-6">
                    <div class="space-y-1 text-base text-slate-700">

                        {{-- Beli dropdown (only) --}}
                        <details class="group">
                            <summary class="flex cursor-pointer items-center justify-between py-3 border-b border-slate-100">
                                <span>Beli</span>
                                <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition"></i>
                            </summary>
                            <div class="pt-2 pb-3 space-y-2 text-sm text-slate-600">
                                <a href="#" class="block py-1">Mobil Baru</a>
                                <a href="#" class="block py-1">Mobil Bekas</a>
                                <a href="#" class="block py-1">Motor Baru</a>
                                <a href="#" class="block py-1">Motor Bekas</a>
                            </div>
                        </details>

                        <a href="#" class="block py-3 border-b border-slate-100">Sewa</a>
                        <a href="{{ route('tentang-kami') }}" class="block py-3 border-b border-slate-100">Tentang kami</a>
                        <a href="{{ route('artikel.index') }}" class="block py-3 border-b border-slate-100">Artikel</a>
                    </div>
                </nav>

                {{-- drawer location footer --}}
                <div class="mt-auto px-5 py-4">
                    <button type="button"
                            class="w-full flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 hover:bg-slate-100 transition">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-geo-alt text-slate-500"></i>
                            <span>Jakarta, Indonesia</span>
                        </div>
                        <i class="bi bi-chevron-down text-slate-400"></i>
                    </button>
                </div>

            </div>
        </aside>
    </div>

    {{-- ✅ INLINE SCRIPT: scroll topbar/lowerbar (punyamu, tetap) --}}
    <script>
      (function () {
        const topbar = document.getElementById('topbar');
        const lowerbar = document.getElementById('lowerbar');
        if (!topbar || !lowerbar) return;

        let lastY = window.scrollY || 0;

        function apply() {
          const y = window.scrollY || 0;
          const goingDown = y > lastY;
          const past = y > 10;

          if (goingDown && past) {
            topbar.style.transform = 'translateY(-100%)';
            lowerbar.classList.remove('translate-y-16');
            lowerbar.classList.add('translate-y-0');
          } else {
            topbar.style.transform = 'translateY(0)';
            lowerbar.classList.remove('translate-y-0');
            lowerbar.classList.add('translate-y-16');
          }

          lastY = y;
        }

        let ticking = false;
        window.addEventListener('scroll', function () {
          if (!ticking) {
            window.requestAnimationFrame(function () {
              apply();
              ticking = false;
            });
            ticking = true;
          }
        }, { passive: true });

        apply();
      })();

      function mobileDrawerNav() {
        return {
          drawerOpen: false,
          accountOpen: false,
          openDrawer() {
            this.drawerOpen = true;
            this.accountOpen = false;
            document.body.classList.add('overflow-hidden');
          },
          closeDrawer() {
            this.drawerOpen = false;
            this.accountOpen = false;
            document.body.classList.remove('overflow-hidden');
          },
        }
      }
    </script>
</nav>
