@extends('layouts.main')

@section('content')
@php
    $heroImg = asset('assets/img/homepage/hero-sewa.png'); 
    $fmtRupiah = function($n){
        if(!$n) return 'Rp 0';
        return 'Rp ' . number_format((int)$n, 0, ',', '.');
    };
@endphp

<div class="mx-auto max-w-[1200px] px-4 sm:px-6 pt-8 pb-10">
    {{-- HERO --}}
    <section class="grid items-center gap-10 lg:grid-cols-2">
        <div>

            <h1 class="text-[30px] font-medium tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                    <span class="block leading-tight">Butuh Jasa Sewa</span>
                    <span class="block leading-tight">Mobil Terdekat?</span>
                    <span class="block leading-tight">Cek Armada Kami!</span>
            </h1>
            <p class="mt-4 max-w-md text-slate-500">
                Temukan berbagai pilihan mobil terbaik dengan harga bersahabat. Pesan sekarang dan rasakan kenyamanannya!
            </p>
        </div>

        <div class="relative">
            <div class="absolute inset-0 -z-10 opacity-60"
                 style="background-image: radial-gradient(circle at 30% 40%, rgba(81,163,238,0.20), transparent 55%);">
            </div>
            <img src="{{ $heroImg }}" alt="hero" class="w-full max-h-[360px] object-contain">
        </div>
    </section>

    {{-- SEARCH BAR --}}
    <section class="mt-8">
    <div class="mx-auto max-w-full">
        <div class="rounded-2xl bg-white shadow-md px-2 py-2 sm:px-3">
        <form method="GET" action="{{ route('sewa.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:flex lg:items-center lg:gap-0">

            @php
            $itemClass  = "rounded-xl bg-white px-5 py-2 pb-0 lg:rounded-none lg:flex-1";
            $labelClass = "text-base leading-none text-slate-400";
            $controlBase = "mt-0 w-full bg-transparent text-base font-medium text-slate-900
                            border-0 outline-none ring-0 focus:ring-0 focus:outline-none
                            focus-visible:outline-none pl-0 pr-2";
            @endphp

            {{-- Lokasi --}}
            <div class="{{ $itemClass }}">
            <div class="{{ $labelClass }}">Lokasi</div>
            <div class="mt-0 relative">
                <select name="lokasi"
                class="select-bi w-full bg-transparent text-base font-medium text-slate-900
                        border-0 outline-none ring-0 focus:ring-0 focus:outline-none
                        focus-visible:outline-none pl-0 pr-8">
                <option value="" {{ request('lokasi')=='' ? 'selected' : '' }}>Pilih lokasi</option>
                @foreach($lokasis as $lok)
                    <option value="{{ $lok }}" {{ request('lokasi')==$lok ? 'selected' : '' }}>
                    {{ $lok }}
                    </option>
                @endforeach
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-900 text-base"></i>
            </div>
            </div>

            <div class="hidden lg:block h-10 w-px bg-slate-200/70"></div>

            {{-- Tanggal (calendar) --}}
            <div class="{{ $itemClass }}">
            <div class="{{ $labelClass }}">Tanggal</div>
            <input
                name="tanggal"
                type="date"
                value="{{ request('tanggal') }}"
                class="{{ $controlBase }} [color-scheme:light]"
            />
            </div>

            <div class="hidden lg:block h-10 w-px bg-slate-200/70"></div>

            {{-- Transmisi --}}
            <div class="{{ $itemClass }}">
            <div class="{{ $labelClass }}">Transmisi</div>
            <div class="mt-0 relative">
                <select name="transmisi"
                class="select-bi w-full bg-transparent text-base font-medium text-slate-900
                        border-0 outline-none ring-0 focus:ring-0 focus:outline-none
                        focus-visible:outline-none pr-8 pl-0">
                <option value="" {{ request('transmisi')=='' ? 'selected' : '' }}>Pilih transmisi</option>
                @foreach($transmisis as $t)
                    <option value="{{ $t }}" {{ request('transmisi')==$t ? 'selected' : '' }}>
                    {{ $t }}
                    </option>
                @endforeach
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-900 text-base"></i>
            </div>
            </div>

            <div class="hidden lg:block h-10 w-px bg-slate-200/70"></div>

            {{-- Mobil Jenis --}}
            <div class="{{ $itemClass }}">
            <div class="{{ $labelClass }}">Mobil Jenis</div>
            <div class="mt-0 relative">
                <select name="jenis_mobil"
                class="select-bi w-full bg-transparent text-base font-medium text-slate-900
                        border-0 outline-none ring-0 focus:ring-0 focus:outline-none
                        focus-visible:outline-none pr-8 pl-0">
                <option value="" {{ request('jenis_mobil')=='' ? 'selected' : '' }}>Pilih jenis</option>
                @foreach($jenisMobils as $j)
                    <option value="{{ $j }}" {{ request('jenis_mobil')==$j ? 'selected' : '' }}>
                    {{ $j }}
                    </option>
                @endforeach
                </select>
                <i class="bi bi-chevron-down pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-900 text-base"></i>
            </div>
            </div>

            {{-- Button --}}
            <div class="lg:pl-4">
            <button type="submit"
                class="inline-flex w-full items-center justify-center rounded-xl bg-[#51A3EE] text-white shadow-sm hover:bg-sky-600
                    h-11 lg:h-11 lg:w-11 lg:rounded-full">
                <i class="bi bi-search"></i>
            </button>
            </div>

        </form>
        </div>
    </div>
    </section>

    {{-- WHY --}}
    <section class="mt-14 sm:mt-20 text-center">
        <h2 class="text-lg font-medium text-slate-800 md:text-3xl md:leading-normal">
            Mengapa Harus Sewa Mobil di <br class="hidden sm:block"> Autotrust Sekarang?
        </h2>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-md ">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-50 text-[#51A3EE]">
                    <i class="bi bi-wrench-adjustable text-3xl"></i>
                </div>
                <div class="mt-4 font-semibold text-lg text-slate-900">Armada Terawat</div>
                <p class="mt-2 text-base text-slate-500">
                    Kendaraan selalu prima, perjalanan aman setiap waktu.
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-md ">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-50 text-[#51A3EE]">
                    <i class="bi bi-diagram-3 text-3xl"></i>
                </div>
                <div class="mt-4 font-semibold text-lg text-slate-900">Jaringan Luas</div>
                <p class="mt-2 text-base text-slate-500">
                    Tersedia di mana pun, layanan tetap cepat maksimal.
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-md ">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-50 text-[#51A3EE]">
                    <i class="bi bi-journal-check text-3xl"></i>
                </div>
                <div class="mt-4 font-semibold text-lg text-slate-900">Pemesanan Praktis</div>
                <p class="mt-2 text-base text-slate-500">
                    Pesan cepat tanpa ribet, langsung jalan sekarang juga.
                </p>
            </div>
        </div>
    </section>

    {{-- PILIHAN --}}
    @php
        $brandAvatar = function($brand){
            $initial = strtoupper(mb_substr($brand ?? 'A', 0, 1));
            return "https://ui-avatars.com/api/?name=".urlencode($initial)."&background=FFFFFF&color=0F172A&rounded=true&bold=true&size=128";
        };

        $brandLogo = function($brand) {
        $file = strtolower(str_replace(' ', '', $brand)) . '.png';
        $path = public_path('assets/img/merk/' . $file);

        if (file_exists($path)) {
            return asset('assets/img/merk/' . $file);
        }

        return asset('assets/img/merk/default.png'); // fallback
        };
    @endphp

    <section class="mt-14 sm:mt-20">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                Pilihan untuk Kebutuhan Anda
            </h2>

        <div x-data="{ tab:'mobil' }" class="mt-5">
            {{-- Tabs --}}
            <div class="flex items-center gap-8 border-b border-slate-100 text-base">
                <button @click="tab='mobil'" class="pb-3"
                        :class="tab==='mobil' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-semibold' : 'text-slate-500'">
                    Mobil
                </button>
                <button @click="tab='motor'" class="pb-3"
                        :class="tab==='motor' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-semibold' : 'text-slate-500'">
                    Motor
                </button>
            </div>

            {{-- Cards (STYLE seperti gambar) --}}
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                {{-- MOBIL --}}
                <template x-if="tab==='mobil'">
                    <div class="contents">
                        @foreach($mobils as $m)
                            @php
                                $img = $m->gambar_mobil ? asset('storage/'.$m->gambar_mobil) : 'https://placehold.co/900x520?text=Mobil';
                                $brand = $m->merk_mobil ?? 'Brand';
                                $title = trim(($m->merk_mobil ?? '').' '.($m->model_mobil ?? ''));
                                $subtitle = ($m->kapasitas_mesin_mobil ? $m->kapasitas_mesin_mobil.'cc' : '1.0 VVT-i') . ' • ' . ($m->transmisi_mobil ?? 'Automatic');
                            @endphp

                            <div class="rounded-2xl bg-white shadow-md overflow-hidden">
                                {{-- header --}}
                                <div class="flex items-start justify-between gap-3 px-5 pt-5">
                                    <div class="flex items-center gap-5">
                                        <img src="{{ $brandLogo($brand) }}"
                                            class="h-12 w-12 object-contain"
                                            alt="{{ $brand }}">
                                        <div class="min-w-0">
                                            <div class="truncate text-base font-semibold text-slate-900">{{ $title ?: $brand }}</div>
                                            <div class="truncate text-sm text-slate-500">{{ $subtitle }}</div>
                                        </div>
                                    </div>

                                    <a href="{{ route('sewa.mobil.show', $m->id) }}"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-100 text-slate-700 shadow-sm">
                                        <i class="bi bi-arrow-up-right"></i>
                                    </a>
                                </div>

                                {{-- image --}}
                                <div class="px-5 pt-4">
                                    <div class="h-[170px] w-full bg-slate-100 rounded-xl">
                                        <img src="{{ $img }}" class="h-full w-full object-contain" alt="mobil">
                                    </div>
                                </div>

                                {{-- footer --}}
                                <div class="px-5 pb-5 pt-3">
                                    <div class="text-sm text-slate-500">Harga</div>

                                    <div class="mt-1 flex items-center justify-between gap-3">
                                        <div class="truncate text-base font-semibold text-slate-900">
                                            {{ $fmtRupiah($m->harga_mobil) }}
                                            <span class="text-base font-normal text-slate-500">/ Hari</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-base text-slate-500">
                                            <span class="inline-flex items-center gap-1">
                                                <i class="bi bi-person"></i>
                                                {{ $m->tempat_duduk_mobil ?? '—' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <i class="bi bi-star-fill text-yellow-400"></i>
                                                4.9
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </template>

                {{-- MOTOR --}}
                <template x-if="tab==='motor'">
                    <div class="contents">
                        @foreach($motors as $m)
                            @php
                                $img = $m->gambar_motor ? asset('storage/'.$m->gambar_motor) : 'https://placehold.co/900x520?text=Motor';
                                $brand = $m->merk_motor ?? 'Brand';
                                $title = trim(($m->merk_motor ?? '').' '.($m->model_motor ?? ''));
                                $subtitle = ($m->kapasitas_mesin_motor ? $m->kapasitas_mesin_motor.'cc' : '150cc') . ' • ' . ($m->transmisi_motor ?? 'Automatic');
                            @endphp

                            <div class="rounded-2xl bg-white shadow-md overflow-hidden">
                                <div class="flex items-start justify-between gap-3 px-5 pt-5">
                                    <div class="flex items-center gap-5">
                                    <img src="{{ $brandLogo($brand) }}"
                                            class="h-12 w-12 object-contain"
                                            alt="{{ $brand }}">
                                        <div class="min-w-0">
                                            <div class="truncate text-base font-semibold text-slate-900">{{ $title ?: $brand }}</div>
                                            <div class="truncate text-sm text-slate-500">{{ $subtitle }}</div>
                                        </div>
                                    </div>

                                    <a href="{{ route('sewa.motor.show', $m->id) }}"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-100 text-slate-700 shadow-sm">
                                        <i class="bi bi-arrow-up-right"></i>
                                    </a>
                                </div>

                                <div class="px-5 pt-4">
                                    <div class="h-[170px] w-full">
                                        <img src="{{ $img }}" class="h-full w-full object-contain" alt="motor">
                                    </div>
                                </div>

                                <div class="px-5 pb-5 pt-3">
                                    <div class="text-sm text-slate-500">Harga</div>

                                    <div class="mt-1 flex items-center justify-between gap-3">
                                        <div class="truncate text-base font-semibold text-slate-900">
                                            {{ $fmtRupiah($m->harga_motor) }}
                                            <span class="text-base font-normal text-slate-500">/ Hari</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-base text-slate-500">

                                            <span class="inline-flex items-center gap-1">
                                                <i class="bi bi-star-fill text-yellow-400"></i>
                                                4.9
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </template>

            </div>

        </div>
    </section>
</div>
@endsection
