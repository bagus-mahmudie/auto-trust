{{-- AUTH MODAL - UI mirip gambar (Login default, switch ke Signup) --}}
<div
    x-show="$store.authModal.isOpen"
    x-cloak
    class="fixed inset-0 z-[99999]"
    x-on:open-auth-modal.window="
        $store.authModal.open(($event.detail && $event.detail.tab) ? $event.detail.tab : 'login')
    "
>
    {{-- overlay --}}
    <div class="absolute inset-0 bg-black/40" @click="$store.authModal.close()"></div>

    {{-- dialog wrapper --}}
    <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-6">
        <div
            x-show="$store.authModal.isOpen"
            x-transition.opacity.scale
            class="
                w-full
                max-w-[420px]
                rounded-3xl
                bg-white
                shadow-[0_20px_60px_rgba(2,6,23,0.25)]
                ring-1 ring-slate-200
                overflow-hidden
            "
            @click.stop
            x-data="{
                loginPw: false,
                regPw: false,
                regPw2: false,
            }"
        >
            {{-- header --}}
            <div class="relative px-5 pt-5 sm:px-6 sm:pt-6">
                <button type="button"
                        class="absolute right-4 top-4 sm:right-5 sm:top-5 flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100"
                        @click="$store.authModal.close()"
                        aria-label="close">
                    <i class="bi bi-x-lg text-slate-500"></i>
                </button>

                <div class="flex flex-col items-center text-center gap-2">
                    {{-- logo --}}
                    <div class="flex items-center gap-2">
                        <div class="h-9 w-9 flex items-center justify-center">
                            {{-- pakai logo kamu kalau mau --}}
                            <img src="{{ asset('assets/img/icons/logo-navbar.png') }}" class="h-6 w-6" alt="Logo">
                        </div>
                        <div class="text-2xl font-semibold text-slate-900">AutoTrust</div>
                    </div>

                    <div class="text-base text-slate-500 leading-relaxed px-6"
                         x-show="$store.authModal.tab === 'login'">
                        Masuk untuk mulai mencari<br class="hidden sm:block"> kendaraan impianmu.
                    </div>

                    <div class="text-base text-slate-500 leading-relaxed px-6"
                         x-show="$store.authModal.tab === 'register'">
                        Buat akun sekarang dan beli kendaraan<br class="hidden sm:block">
                        dengan aman, cepat, dan terpercaya.
                    </div>
                </div>
            </div>

            {{-- content --}}
            <div class="px-5 pb-5 sm:px-6 sm:pb-6 pt-4">
                {{-- ============ LOGIN ============ --}}
                <div x-show="$store.authModal.tab === 'login'" x-cloak>
                    <form method="POST" action="{{ route('auth.login.post') }}" class="space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div class="space-y-1">
                            <label class="text-base font-medium text-slate-700">Alamat Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    class="
                                        w-full rounded-2xl bg-slate-50
                                        border border-slate-200
                                        pl-11 pr-4 py-3
                                        text-base text-slate-700
                                        placeholder:text-slate-400
                                        focus:border-slate-300 focus:ring-slate-200
                                    "
                                    placeholder="alex.thompson@email.com"
                                >
                            </div>
                            @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <label class="text-base font-medium text-slate-700">Kata Sandi</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    name="password"
                                    :type="loginPw ? 'text' : 'password'"
                                    required
                                    class="
                                        w-full rounded-2xl bg-slate-50
                                        border border-slate-200
                                        pl-11 pr-12 py-3
                                        text-base text-slate-700
                                        placeholder:text-slate-400
                                        focus:border-slate-300 focus:ring-slate-200
                                    "
                                    placeholder="••••••••"
                                >

                                <button type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                        @click="loginPw = !loginPw"
                                        aria-label="toggle password">
                                    <i class="bi" :class="loginPw ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- remember + forgot --}}
                        <div class="flex items-center justify-between pt-1">
                            <label class="inline-flex items-center gap-2 text-base text-slate-600">
                                <input type="checkbox" name="remember"
                                       class="rounded border-slate-300 text-sky-600 focus:ring-sky-200">
                                Ingat Saya
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-base text-sky-600 hover:text-sky-700">
                                    Lupa Kata Sandi?
                                </a>
                            @else
                                <a href="#" class="text-base text-sky-600 hover:text-sky-700">Lupa Kata Sandi?</a>
                            @endif
                        </div>

                        {{-- submit --}}
                        <button type="submit"
                                class="w-full rounded-full bg-[#51A3EE] hover:bg-sky-600 text-white py-3 text-base font-semibold transition">
                            Masuk
                        </button>

                        {{-- divider --}}
                        <div class="pt-2">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <div class="text-xs text-slate-500">Atau lanjutkan dengan</div>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>
                        </div>

                        {{-- social --}}
                        <div class="grid grid-cols-3 gap-3">
                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="google">
                                {{-- icon google (svg) --}}
                                <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.7 1.22 9.18 3.61l6.84-6.84C35.9 2.38 30.4 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.4 13.56 17.73 9.5 24 9.5z"/>
                                    <path fill="#4285F4" d="M46.5 24c0-1.64-.15-3.21-.43-4.73H24v9.01h12.7c-.55 2.96-2.2 5.47-4.68 7.16l7.16 5.56C43.3 36.98 46.5 30.98 46.5 24z"/>
                                    <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.13.78-4.59l-7.98-6.19A23.93 23.93 0 0 0 0 24c0 3.86.92 7.52 2.56 10.78l7.98-6.19z"/>
                                    <path fill="#34A853" d="M24 48c6.4 0 11.9-2.12 15.87-5.76l-7.16-5.56c-1.99 1.34-4.54 2.13-8.71 2.13-6.27 0-11.6-4.06-13.46-9.72l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                </svg>
                            </a>

                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="facebook">
                                <i class="bi bi-facebook text-[#1877F2] text-lg"></i>
                            </a>

                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="apple">
                                <i class="bi bi-apple text-black text-lg"></i>
                            </a>
                        </div>

                        {{-- bottom link --}}
                        <div class="pt-1 text-center text-xs text-slate-500">
                            Don’t have an account?
                            <button type="button"
                                    class="text-sky-600 hover:text-sky-700 font-medium"
                                    @click="$store.authModal.switch('register')">
                                Sign Up
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ============ REGISTER ============ --}}
                <div x-show="$store.authModal.tab === 'register'" x-cloak x-data="{ email: '{{ old('email') }}' }">
                    <form method="POST" action="{{ route('auth.register.post') }}" class="space-y-4">
                        @csrf

                        {{-- name wajib di DB kamu -> generate dari email --}}
                        <input type="hidden" name="name"
                               :value="(email || '').split('@')[0] ? (email.split('@')[0]).replace(/[._-]+/g,' ') : 'User'">

                        {{-- Email --}}
                        <div class="space-y-1">
                            <label class="text-base font-medium text-slate-700">Alamat Email</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    name="email"
                                    type="email"
                                    x-model="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    class="
                                        w-full rounded-2xl bg-slate-50
                                        border border-slate-200
                                        pl-11 pr-4 py-3
                                        text-base text-slate-700
                                        placeholder:text-slate-400
                                        focus:border-slate-300 focus:ring-slate-200
                                    "
                                    placeholder="alex.thompson@email.com"
                                >
                            </div>
                            @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <label class="text-base font-medium text-slate-700">Kata Sandi</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    name="password"
                                    :type="regPw ? 'text' : 'password'"
                                    required
                                    class="
                                        w-full rounded-2xl bg-slate-50
                                        border border-slate-200
                                        pl-11 pr-12 py-3
                                        text-base text-slate-700
                                        placeholder:text-slate-400
                                        focus:border-slate-300 focus:ring-slate-200
                                    "
                                    placeholder="••••••••"
                                >

                                <button type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                        @click="regPw = !regPw"
                                        aria-label="toggle password">
                                    <i class="bi" :class="regPw ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="space-y-1">
                            <label class="text-base font-medium text-slate-700">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    name="password_confirmation"
                                    :type="regPw2 ? 'text' : 'password'"
                                    required
                                    class="
                                        w-full rounded-2xl bg-slate-50
                                        border border-slate-200
                                        pl-11 pr-12 py-3
                                        text-base text-slate-700
                                        placeholder:text-slate-400
                                        focus:border-slate-300 focus:ring-slate-200
                                    "
                                    placeholder="••••••••"
                                >

                                <button type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                        @click="regPw2 = !regPw2"
                                        aria-label="toggle password">
                                    <i class="bi" :class="regPw2 ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- remember + forgot (sesuai gambar) --}}
                        <div class="flex items-center justify-between pt-1">
                            <label class="inline-flex items-center gap-2 text-base text-slate-600">
                                <input type="checkbox"
                                       class="rounded border-slate-300 text-sky-600 focus:ring-sky-200">
                                Ingat Saya
                            </label>

                            <a href="#" class="text-base text-sky-600 hover:text-sky-700">
                                Lupa Kata Sandi?
                            </a>
                        </div>

                        {{-- submit --}}
                        <button type="submit"
                                class="w-full rounded-full bg-[#51A3EE] hover:bg-sky-600 text-white py-3 text-base font-semibold transition">
                            Buat Akun
                        </button>

                        {{-- divider --}}
                        <div class="pt-2">
                            <div class="flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <div class="text-xs text-slate-500">Atau lanjutkan dengan</div>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>
                        </div>

                        {{-- social --}}
                        <div class="grid grid-cols-3 gap-3">
                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="google">
                                <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.7 1.22 9.18 3.61l6.84-6.84C35.9 2.38 30.4 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.4 13.56 17.73 9.5 24 9.5z"/>
                                    <path fill="#4285F4" d="M46.5 24c0-1.64-.15-3.21-.43-4.73H24v9.01h12.7c-.55 2.96-2.2 5.47-4.68 7.16l7.16 5.56C43.3 36.98 46.5 30.98 46.5 24z"/>
                                    <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.13.78-4.59l-7.98-6.19A23.93 23.93 0 0 0 0 24c0 3.86.92 7.52 2.56 10.78l7.98-6.19z"/>
                                    <path fill="#34A853" d="M24 48c6.4 0 11.9-2.12 15.87-5.76l-7.16-5.56c-1.99 1.34-4.54 2.13-8.71 2.13-6.27 0-11.6-4.06-13.46-9.72l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                </svg>
                            </a>

                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="facebook">
                                <i class="bi bi-facebook text-[#1877F2] text-lg"></i>
                            </a>

                            <a href="#"
                               class="flex items-center justify-center rounded-2xl bg-slate-50 border border-slate-200 py-3 hover:bg-slate-100 transition"
                               aria-label="apple">
                                <i class="bi bi-apple text-black text-lg"></i>
                            </a>
                        </div>

                        {{-- bottom link --}}
                        <div class="pt-1 text-center text-xs text-slate-500">
                            Don’t have an account?
                            <button type="button"
                                    class="text-sky-600 hover:text-sky-700 font-medium"
                                    @click="$store.authModal.switch('login')">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
