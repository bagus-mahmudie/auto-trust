<footer class="bg-[#2F2F2F] text-white">
    {{-- TOP AREA --}}
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

            {{-- LEFT: brand + desc + socials --}}
            <div>
                <div class="flex items-center gap-3">
                    {{-- Logo icon (svg) --}}
                    <div class="h-10 w-10 flex items-center justify-center">
                        <img src="{{ asset('assets/img/icons/logo-footer.png') }}" class="h-8 w-8" alt="logo">
                    </div>

                    <div class="text-2xl font-semibold tracking-tight">AutoTrust</div>
                </div>

                <p class="mt-4 max-w-[460px] text-base text-white/80 leading-relaxed">
                    Lorem ipsum dolor sit amet consectetur.
                    Blandit pellentesque tortor lectus morbi
                </p>

                <div class="mt-6 flex items-center gap-3">
                    {{-- Social buttons --}}
                    <a href="#"
                       class="h-11 w-11 rounded-full bg-[#51A3EE] flex items-center justify-center hover:bg-sky-600 transition"
                       aria-label="Instagram">
                        <i class="bi bi-instagram text-lg"></i>
                    </a>
                    <a href="#"
                       class="h-11 w-11 rounded-full bg-[#51A3EE] flex items-center justify-center hover:bg-sky-600 transition"
                       aria-label="Facebook">
                        <i class="bi bi-facebook text-lg"></i>
                    </a>
                    <a href="#"
                       class="h-11 w-11 rounded-full bg-[#51A3EE] flex items-center justify-center hover:bg-sky-600 transition"
                       aria-label="TikTok">
                        <i class="bi bi-tiktok text-lg"></i>
                    </a>
                    <a href="#"
                       class="h-11 w-11 rounded-full bg-[#51A3EE] flex items-center justify-center hover:bg-sky-600 transition"
                       aria-label="X">
                        <span class="font-bold text-lg leading-none">X</span>
                    </a>
                </div>
            </div>

            {{-- RIGHT: text + button --}}
            <div class="lg:justify-self-end w-full lg:max-w-[520px]">
                <p class="text-base text-white/80 leading-relaxed
                        text-left
                        lg:text-right lg:ml-auto lg:max-w-[300px]">
                    Lorem ipsum dolor sit amet consectetur. Blandit pellentesque
                </p>

                <div class="mt-6 flex lg:justify-end">
                    <a href="#"
                       class="inline-flex w-full sm:w-auto items-center justify-center rounded-full bg-[#51A3EE]
                              px-10 py-3 text-lg font-medium text-white hover:bg-sky-600 transition">
                        Hubungi Kami
                    </a>
                </div>
            </div>

        </div>

        {{-- Divider --}}
        <div class="mt-10 h-px bg-white/20"></div>

        {{-- LINKS GRID --}}
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Layanan --}}
            <div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-[2px] bg-[#51A3EE] rounded-full"></span>
                    <h3 class="text-xl font-semibold">Layanan</h3>
                </div>

                <ul class="mt-6 space-y-3 text-base text-white/80">
                    <li><a href="#" class="hover:text-white transition">Jual Kendaraan</a></li>
                    <li><a href="#" class="hover:text-white transition">Beli Mobil</a></li>
                    <li><a href="#" class="hover:text-white transition">Beli Motor</a></li>
                    <li><a href="#" class="hover:text-white transition">Sewa Kendaraan</a></li>
                    <li><a href="#" class="hover:text-white transition">Kendaraan Bekas</a></li>
                </ul>
            </div>

            {{-- Tentang Kami --}}
            <div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-[2px] bg-[#51A3EE] rounded-full"></span>
                    <h3 class="text-xl font-semibold">Tentang Kami</h3>
                </div>

                <ul class="mt-6 space-y-3 text-base text-white/80">
                    <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-white transition">Partner &amp; Dealer</a></li>
                    <li><a href="#" class="hover:text-white transition">Blog / Insight Otomotif</a></li>
                    <li><a href="#" class="hover:text-white transition">Testimoni Pengguna</a></li>
                </ul>
            </div>

            {{-- Bantuan --}}
            <div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-[2px] bg-[#51A3EE] rounded-full"></span>
                    <h3 class="text-xl font-semibold">Bantuan</h3>
                </div>

                <ul class="mt-6 space-y-3 text-base text-white/80">
                    <li><a href="#" class="hover:text-white transition">Pusat Bantuan (FAQ)</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Beli Kendaraan</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Jual Kendaraan</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Sewa Kendaraan</a></li>
                </ul>
            </div>

            {{-- Keamanan & Legal --}}
            <div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-[2px] bg-[#51A3EE] rounded-full"></span>
                    <h3 class="text-xl font-semibold">Keamanan &amp; Legal</h3>
                </div>

                <ul class="mt-6 space-y-3 text-base text-white/80">
                    <li><a href="#" class="hover:text-white transition">Syarat &amp; Ketentuan</a></li>
                    <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-white transition">Kebijakan Penggunaan</a></li>
                    <li><a href="#" class="hover:text-white transition">Disclaimer Produk Finansial</a></li>
                </ul>
            </div>

        </div>
    </div>

    {{-- BOTTOM BAR --}}
    <div class="bg-[#51A3EE]">
        <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-5 text-base text-white">
            All Rights Reserved ©2025 Auto trust | PT. Autotrust
        </div>
    </div>
</footer>
