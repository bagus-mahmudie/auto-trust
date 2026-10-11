@extends('layouts.main')

@section('content')
<div class="bg-white">
    <!-- Hero -->
    <section class="mx-auto max-w-[1200px] px-4 pt-6 sm:pt-8 lg:pt-10">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-center lg:gap-10">
            <!-- Left text -->
            <div class="max-w-xl">
                <h1 class="text-[30px] font-medium tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                    <span class="block leading-tight">Lorem ipsum dolor sit</span>
                    <span class="block leading-tight">amet consectetur.</span>
                </h1>

                <p class="mt-4 max-w-lg text-[14px] leading-6 text-slate-500 sm:mt-5 sm:text-base">
                    Lorem ipsum dolor sit amet consectetur. Placerat tristique lectus augue turpis sit sollicitudin neque lobortis massa. Risus donec senectus quam
                </p>

                <!-- Rating card -->
                <div class="mt-6 inline-flex items-center gap-4 rounded-2xl bg-white px-4 py-3 shadow-[0_10px_30px_rgba(2,6,23,0.08)] ring-1 ring-slate-100 sm:mt-7">
                    <div class="flex -space-x-2">
                        <img src="{{ asset('assets/img/homepage/hero-1.png') }}" alt="" class="h-9 w-9 rounded-full ring-2 ring-white" />
                        <img src="{{ asset('assets/img/homepage/hero-2.png') }}" alt="" class="h-9 w-9 rounded-full ring-2 ring-white" />
                        <img src="{{ asset('assets/img/profile.png') }}" alt="" class="h-9 w-9 rounded-full ring-2 ring-white" />
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base font-semibold text-slate-800">4.9</span>
                            <div class="flex items-center text-amber-400">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.999 1.5l2.67 5.83 6.33.55-4.78 4.14 1.42 6.23L10 15.74 4.36 18.25l1.42-6.23L1 7.88l6.33-.55L10 1.5z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <div class="text-[14px] text-slate-500 sm:text-base">Trusted by Thousands</div>
                    </div>
                </div>
            </div>

            <!-- Right image -->
            <div class="relative order-last block md:hidden lg:block lg:order-none">
                <div class="absolute -inset-3 -z-10 rounded-[28px] bg-slate-50 sm:-inset-4 md:-inset-5 lg:-inset-6 lg:rounded-[40px]"></div>
                <img src="{{ asset('assets/img/homepage/hero.png') }}"
                    alt="Mobil"
                    class="w-full object-contain md:max-h-[320px] lg:max-h-none" />
            </div>

        </div>
    </section>

    <!-- Lower section -->
    <section class="mx-auto max-w-[1200px] px-4 pb-12 pt-8 sm:pb-14 sm:pt-10" x-data="{ openAllTypes:false, openAllBrands:false }" @keydown.escape.window="openAllTypes=false">
        <div class="rounded-3xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] ring-1 ring-slate-100 overflow-hidden">
            {{-- Desktop/tablet: 60:40 + divider vertikal
                 Mobile: stack 1 kolom --}}
            <div class="grid grid-cols-1 lg:grid-cols-[3fr_1px_2fr]">

                {{-- LEFT (60%) --}}
                <div class="p-5 sm:p-6 md:p-8">
                    {{-- Title row --}}
                    <div class="flex items-center justify-between">
                        <h2 class="text-[16px] font-medium text-slate-800 sm:text-lg md:text-xl">
                            <span class="block sm:inline">Mobil Berdasarkan</span>
                            <span class="block sm:inline">Tipe Mobil</span>
                        </h2>
                        <a href="#"
                        class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg"
                        @click.prevent="openAllTypes = true"
                        >
                            <span class="sm:hidden">Lihat semua</span>
                            <span class="hidden sm:inline">Lihat semua tipe</span>
                            <i class="bi bi-chevron-right text-[16px]"></i>
                        </a>
                    </div>

                    {{-- Body type (responsive grid) --}}
                    @php
                        $bodyTypesShow = [
                            ['label' => 'MPV',       'img' => 'assets/img/tipe-body/mpv.png'],
                            ['label' => 'SUV',       'img' => 'assets/img/tipe-body/suv.png'],
                            ['label' => 'Crossover', 'img' => 'assets/img/tipe-body/crossover.png'],
                            ['label' => 'Minivans',  'img' => 'assets/img/tipe-body/minivans.png'],
                            ['label' => 'Wagon',     'img' => 'assets/img/tipe-body/wagon.png'],
                            ['label' => 'Hatchback', 'img' => 'assets/img/tipe-body/hatchback.png'],
                        ];
                    @endphp

                    <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-5 md:mt-6">
                        @foreach ($bodyTypesShow as $t)
                            <a
                                href="{{ url('/rekomendasi-mobil') . '?jenis_kendaraan=' . urlencode($t['label']) }}"
                                class="
                                    block
                                    rounded-2xl bg-slate-50 px-4 pb-4 pt-5 text-center
                                    ring-1 ring-slate-100
                                    transition
                                    hover:ring-sky-400 hover:shadow-sm
                                "
                            >
                                <img
                                    src="{{ asset($t['img']) }}"
                                    alt="{{ $t['label'] }}"
                                    class="mx-auto h-[54px] w-auto sm:h-[58px]"
                                />
                                <div class="mt-3 text-[13px] font-medium text-slate-700 sm:text-sm">
                                    {{ $t['label'] }}
                                </div>
                            </a>

                        @endforeach
                    </div>

                    {{-- Brand title row --}}
                    <div class="mt-7 flex items-center justify-between sm:mt-8">
                        <h2 class="text-[16px] font-medium text-slate-800 sm:text-lg md:text-xl">
                            <span class="block sm:inline">Mobil Berdasarkan</span>
                            <span class="block sm:inline">Merek Mobil</span>
                        </h2>
                        <a href="#"
                        class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg"
                        @click.prevent="openAllBrands = true"
                        >
                            <span class="sm:hidden">Lihat semua</span>
                            <span class="hidden sm:inline">Lihat semua merek</span>
                            <i class="bi bi-chevron-right text-[16px]"></i>
                        </a>
                    </div>

                    {{-- Brand logos (responsive) --}}
                    @php
                        $brandsShow = [
                            ['alt' => 'Toyota',  'img' => 'assets/img/merk/toyota.png'],
                            ['alt' => 'Honda',   'img' => 'assets/img/merk/honda.png'],
                            ['alt' => 'BMW',     'img' => 'assets/img/merk/bmw.png'],
                            ['alt' => 'Hyundai', 'img' => 'assets/img/merk/hyundai.png'],
                        ];
                    @endphp

                    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4 sm:gap-5 md:mt-5">
                        @foreach ($brandsShow as $b)
                            <a
                                href="{{ url('/rekomendasi-mobil') . '?merk=' . urlencode($b['alt']) }}"
                                class="
                                    flex h-[58px] items-center justify-center
                                    rounded-2xl bg-slate-50
                                    ring-1 ring-slate-100
                                    transition
                                    hover:ring-sky-400 hover:shadow-sm
                                "
                            >
                                <img
                                    src="{{ asset($b['img']) }}"
                                    alt="{{ $b['alt'] }}"
                                    class="h-10 w-auto sm:h-12"
                                />
                            </a>

                        @endforeach
                    </div>
                </div>

                {{-- DIVIDER:
                     - md ke atas: divider vertikal
                     - mobile: divider horizontal --}}
                <div class="md:bg-slate-100 lg:w-px">
                    <div class="h-px bg-slate-100 md:hidden"></div>
                </div>

                {{-- RIGHT (40%) --}}
                <div class="p-5 sm:p-6 md:p-8" x-data="{ tab: 'mobil' }">
                    {{-- Tabs --}}
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-3 border-b border-slate-100 pb-4 text-[14px] font-medium sm:text-base">
                        {{-- Mobil --}}
                        <button type="button"
                            @click="tab='mobil'"
                            class="inline-flex items-center gap-2"
                            :class="tab==='mobil' ? 'text-sky-600' : 'text-slate-500 hover:text-slate-700'">
                            <i class="bi bi-car-front text-[16px]"></i>
                            Mobil
                        </button>

                        {{-- Motor --}}
                        <button type="button"
                            @click="tab='motor'"
                            class="inline-flex items-center gap-2"
                            :class="tab==='motor' ? 'text-sky-600' : 'text-slate-500 hover:text-slate-700'">
                            <i class="bi bi-bicycle text-[16px]"></i>
                            Motor
                        </button>

                        {{-- Truck (tidak berfungsi) --}}
                        <button type="button"
                            class="inline-flex items-center gap-2 text-slate-300 cursor-not-allowed"
                            title="Coming soon">
                            <i class="bi bi-truck text-[16px]"></i>
                            Truck
                        </button>

                        {{-- Mini Bus (tidak berfungsi) --}}
                        <button type="button"
                            class="inline-flex items-center gap-2 text-slate-300 cursor-not-allowed"
                            title="Coming soon">
                            <i class="bi bi-bus-front text-[16px]"></i>
                            Mini Bus
                        </button>
                    </div>

                    {{-- =======================
                        FORM MOBIL
                    ======================== --}}
                    <form x-show="tab==='mobil'" x-cloak
                        method="GET" action="{{ url('/rekomendasi-mobil') }}"
                        class="mt-6 space-y-6">

                        <div>
                            <label class="text-[14px] font-medium text-slate-800 sm:text-base">Kondisi</label>
                            <select name="kondisi"
                                class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                <option value="">Pilih kondisi</option>
                                @foreach(($kondisis ?? []) as $k)
                                    <option value="{{ $k }}" @selected(request('kondisi')===$k)>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Merek</label>
                                <select name="merk"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($merks ?? []) as $m)
                                        <option value="{{ $m }}" @selected(request('merk')===$m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Model</label>
                                <select name="model"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($models ?? []) as $m)
                                        <option value="{{ $m }}" @selected(request('model')===$m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Semua Lokasi</label>
                                <select name="lokasi"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($lokasis ?? []) as $l)
                                        <option value="{{ $l }}" @selected(request('lokasi')===$l)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Harga</label>
                                <select name="harga"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    <option value="lt_150" @selected(request('harga')==='lt_150')>&lt; 150 Juta</option>
                                    <option value="lt_200" @selected(request('harga')==='lt_200')>&lt; 200 Juta</option>
                                    <option value="200_400" @selected(request('harga')==='200_400')>200 - 400 Juta</option>
                                    <option value="400_800" @selected(request('harga')==='400_800')>400 - 800 Juta</option>
                                    <option value="800_1200" @selected(request('harga')==='800_1200')>800 - 1.2 M</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center gap-3 rounded-full bg-[#51A3EE] py-4 text-[15px] font-medium text-white shadow-sm hover:bg-sky-600 transition sm:text-base">
                            <i class="bi bi-search text-[16px]"></i>
                            Cari Kendaraan Sekarang
                        </button>
                    </form>

                    {{-- =======================
                        FORM MOTOR
                    ======================== --}}
                    <form x-show="tab==='motor'" x-cloak
                        method="GET" action="{{ url('/rekomendasi-motor') }}"
                        class="mt-6 space-y-6">

                        <div>
                            <label class="text-[14px] font-medium text-slate-800 sm:text-base">Kondisi</label>
                            <select name="kondisi"
                                class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                <option value="">Pilih kondisi</option>
                                @foreach(($kondisisMotor ?? []) as $k)
                                    <option value="{{ $k }}" @selected(request('kondisi')===$k)>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Merek</label>
                                <select name="merk"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($merksMotor ?? []) as $m)
                                        <option value="{{ $m }}" @selected(request('merk')===$m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Model</label>
                                <select name="model"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($modelsMotor ?? []) as $m)
                                        <option value="{{ $m }}" @selected(request('model')===$m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Semua Lokasi</label>
                                <select name="lokasi"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    @foreach(($lokasisMotor ?? []) as $l)
                                        <option value="{{ $l }}" @selected(request('lokasi')===$l)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[14px] font-medium text-slate-800 sm:text-base">Harga</label>
                                <select name="harga"
                                    class="mt-3 w-full rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[14px] text-slate-600 outline-none focus:border-sky-500 focus:ring-0">
                                    <option value="">Semua</option>
                                    <option value="lt_10" @selected(request('harga')==='lt_10')>&lt; 10 Juta</option>
                                    <option value="10_20" @selected(request('harga')==='10_20')>10 - 20 Juta</option>
                                    <option value="20_40" @selected(request('harga')==='20_40')>20 - 40 Juta</option>
                                    <option value="40_80" @selected(request('harga')==='40_80')>40 - 80 Juta</option>
                                    <option value="gt_80" @selected(request('harga')==='gt_80')>&gt; 80 Juta</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center gap-3 rounded-full bg-[#51A3EE] py-4 text-[15px] font-medium text-white shadow-sm hover:bg-sky-600 transition sm:text-base">
                            <i class="bi bi-search text-[16px]"></i>
                            Cari Kendaraan Sekarang
                        </button>
                    </form>
                </div>


            </div>
        </div>

        {{-- MODAL: LIHAT SEMUA TIPE --}}
        <div x-show="openAllTypes" x-cloak class="fixed inset-0 z-[9999]">
            {{-- overlay --}}
            <div class="absolute inset-0 bg-black/40" @click="openAllTypes=false"></div>

            {{-- dialog --}}
            <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-6">
                <div
                    x-show="openAllTypes"
                    x-transition.opacity.scale
                    class="
                        w-full
                        max-w-[820px]
                        rounded-3xl
                        bg-white
                        shadow-[0_20px_60px_rgba(2,6,23,0.25)]
                        ring-1 ring-slate-200
                        overflow-hidden
                    "
                    @click.stop
                >
                    {{-- header --}}
                    <div class="flex items-center justify-between px-5 py-4 sm:px-6">
                        <div class="text-lg font-semibold text-slate-900">
                            Semua Tipe Mobil
                        </div>

                        <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100"
                            @click="openAllTypes=false"
                            aria-label="close"
                        >
                            <i class="bi bi-x-lg text-slate-600"></i>
                        </button>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- content (scrollable) --}}
                    <div class="max-h-[75vh] overflow-y-auto p-4 sm:p-6">
                        @php
                            // Sesuaikan daftar ini dengan kebutuhan kamu
                            $allBodyTypes = [
                                ['label' => 'MPV',          'img' => 'assets/img/tipe-body/mpv.png'],
                                ['label' => 'SUV',          'img' => 'assets/img/tipe-body/suv.png'],
                                ['label' => 'Crossover',    'img' => 'assets/img/tipe-body/crossover.png'],
                                ['label' => 'Minivans',     'img' => 'assets/img/tipe-body/minivans.png'],
                                ['label' => 'Wagon',        'img' => 'assets/img/tipe-body/wagon.png'],
                                ['label' => 'Sedan',        'img' => 'assets/img/tipe-body/sedan.png'],
                                ['label' => 'Coupe',        'img' => 'assets/img/tipe-body/coupe.png'],
                                ['label' => 'Pickup Truck', 'img' => 'assets/img/tipe-body/pickup.png'],
                                ['label' => 'Van',          'img' => 'assets/img/tipe-body/van.png'],
                                ['label' => 'Convertible',  'img' => 'assets/img/tipe-body/convertible.png'],
                                ['label' => 'Sportback',    'img' => 'assets/img/tipe-body/sportback.png'],
                                ['label' => 'Hatchback',    'img' => 'assets/img/tipe-body/hatchback.png'],
                            ];
                        @endphp

                        {{-- Grid responsive:
                            mobile: 2 kolom
                            tablet+: 3 kolom
                        --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                            @foreach($allBodyTypes as $t)
                                <button
                                        type="button"
                                        class="
                                            group w-full rounded-2xl bg-slate-50 p-3 sm:p-4 ring-1 ring-slate-200 transition
                                            hover:ring-sky-500 hover:shadow-[0_10px_24px_rgba(2,132,199,0.20)]
                                            focus:outline-none focus:ring-2 focus:ring-sky-500
                                        "
                                        @click="
                                            openAllTypes=false;
                                            window.location.href='{{ url('/rekomendasi-mobil') }}?jenis_kendaraan={{ urlencode($t['label']) }}';
                                        "
                                    >

                                    <div class="flex items-center justify-center rounded-2xl">
                                        <img
                                            src="{{ asset($t['img']) }}"
                                            alt="{{ $t['label'] }}"
                                            class="h-[64px] w-full object-contain select-none sm:h-[70px]"
                                            draggable="false"
                                        />
                                    </div>

                                    <div class="mt-2 text-center text-sm font-medium text-slate-700">
                                        {{ $t['label'] }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL: LIHAT SEMUA MEREK --}}
        <div x-show="openAllBrands" x-cloak class="fixed inset-0 z-[9999]">
            {{-- overlay --}}
            <div class="absolute inset-0 bg-black/40" @click="openAllBrands=false"></div>

            {{-- dialog --}}
            <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-6">
                <div
                    x-show="openAllBrands"
                    x-transition.opacity.scale
                    class="
                        w-full
                        max-w-[820px]
                        rounded-3xl
                        bg-white
                        shadow-[0_20px_60px_rgba(2,6,23,0.25)]
                        ring-1 ring-slate-200
                        overflow-hidden
                    "
                    @click.stop
                >
                    {{-- header --}}
                    <div class="flex items-center justify-between px-5 py-4 sm:px-6">
                        <div class="text-lg font-semibold text-slate-900">
                            Semua Merek Mobil
                        </div>

                        <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100"
                            @click="openAllBrands=false"
                            aria-label="close"
                        >
                            <i class="bi bi-x-lg text-slate-600"></i>
                        </button>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    {{-- content --}}
                    <div class="max-h-[75vh] overflow-y-auto p-4 sm:p-6">
                        @php
                            // Sesuaikan path logo brand sesuai assets kamu
                            // Contoh folder: public/assets/img/merk/
                            $allBrands = [
                                ['label' => 'Toyota',      'img' => 'assets/img/merk/toyota.png'],
                                ['label' => 'Hyundai',     'img' => 'assets/img/merk/hyundai.png'],
                                ['label' => 'Honda',       'img' => 'assets/img/merk/honda.png'],
                                ['label' => 'BMW',         'img' => 'assets/img/merk/bmw.png'],
                                ['label' => 'Mercedes',    'img' => 'assets/img/merk/mercedes.png'],
                                ['label' => 'Nissan',      'img' => 'assets/img/merk/nissan.png'],
                                ['label' => 'Mitsubishi',  'img' => 'assets/img/merk/mitsubishi.png'],
                                ['label' => 'Suzuki',      'img' => 'assets/img/merk/suzuki.png'],
                                ['label' => 'Mazda',       'img' => 'assets/img/merk/mazda.png'],
                                ['label' => 'Isuzu',       'img' => 'assets/img/merk/isuzu.png'],
                                ['label' => 'Wuling',      'img' => 'assets/img/merk/wuling.png'],
                                ['label' => 'Daihatsu',    'img' => 'assets/img/merk/daihatsu.png'],
                            ];
                        @endphp

                        {{-- grid responsive: mobile 2 kolom, tablet+ 3 kolom --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                            @foreach($allBrands as $b)
                                    <button
                                        type="button"
                                        class="
                                            group w-full rounded-2xl bg-slate-50 p-3 sm:p-4 ring-1 ring-slate-200 transition
                                            hover:ring-sky-500 hover:shadow-[0_10px_24px_rgba(2,132,199,0.20)]
                                            focus:outline-none focus:ring-2 focus:ring-sky-500
                                        "
                                        @click="
                                            openAllBrands=false;
                                            window.location.href='{{ url('/rekomendasi-mobil') }}?merk={{ urlencode($b['label']) }}';
                                        "
                                    >
                                    <div class="flex h-[90px] items-center justify-center rounded-2xl">
                                        <img
                                            src="{{ asset($b['img']) }}"
                                            alt="{{ $b['label'] }}"
                                            class="
                                                h-full
                                                w-auto
                                                object-contain
                                                select-none
                                            "
                                            draggable="false"
                                        />
                                    </div>

                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- REKOMENDASI --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Direkomendasikan</span>
                <span class="block sm:inline">untuk anda</span>
            </h2>

            <a href="{{ route('rekomendasi.mobil') }}"
            class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg">
                <span class="sm:hidden">Lihat semua</span>
                <span class="hidden sm:inline">Lihat semua mobil</span>
                <i class="bi bi-chevron-right text-[16px]"></i>
            </a>
        </div>

        @if(empty($rekomendasis) || $rekomendasis->count() === 0)
            <div class="mt-6 rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
                Belum ada data mobil untuk rekomendasi.
            </div>
        @else
            @php
                // tablet dots: 1 dot per halaman (2 mobil)
                $rekomendasiPages = $rekomendasis->chunk(2);
            @endphp

            {{-- MOBILE: carousel (1 card per view) --}}
            <div class="mt-6 sm:hidden">
                <div class="relative" data-cards-carousel>
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($rekomendasis as $mobil)
                                <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" />
                            @endforeach
                        </div>
                    </div>

                    {{-- dots (1 dot per card) --}}
                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($rekomendasis as $i => $m)
                            <button type="button"
                                    class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                    data-cards-dot="{{ $i }}"
                                    aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TABLET (SM): carousel (2 card per view / per halaman) --}}
            <div class="mt-6 hidden sm:block lg:hidden">
                <div class="relative" data-cards-carousel data-cards-mode="paged">
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($rekomendasiPages as $page)
                                <div class="min-w-full px-1">
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($page as $mobil)
                                            <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- dots (1 dot per PAGE) --}}
                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($rekomendasiPages as $i => $p)
                            <button type="button"
                                    class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                    data-cards-dot="{{ $i }}"
                                    aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- DESKTOP: grid 3 kolom --}}
            <div class="mt-6 hidden lg:grid grid-cols-3 gap-6">
                @foreach($rekomendasis as $mobil)
                    <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- POPULER --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Mobil Populer dan</span>
                <span class="block sm:inline">Terbaru</span>
            </h2>

            <a href="{{ route('populer.mobil') }}"
            class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg">
                <span class="sm:hidden">Lihat semua</span>
                <span class="hidden sm:inline">Lihat semua mobil</span>
                <i class="bi bi-chevron-right text-[16px]"></i>
            </a>
        </div>

        @if(empty($populers) || $populers->count() === 0)
            <div class="mt-6 rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
                Belum ada data mobil populer.
            </div>
        @else
            @php
                $populerPages = $populers->chunk(2);
            @endphp

            {{-- MOBILE: carousel (1 card per view) --}}
            <div class="mt-6 sm:hidden">
                <div class="relative" data-cards-carousel>
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($populers as $mobil)
                                <x-mobil-card
                                    :mobil="$mobil"
                                    :likedMobilIds="$likedMobilIds"
                                    :showPopulerBadge="true"
                                />
                            @endforeach
                        </div>
                    </div>

                    {{-- dots (1 dot per card) --}}
                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($populers as $i => $m)
                            <button type="button"
                                    class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                    data-cards-dot="{{ $i }}"
                                    aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TABLET (SM): carousel (2 card per halaman) --}}
            <div class="mt-6 hidden sm:block lg:hidden">
                <div class="relative" data-cards-carousel data-cards-mode="paged">
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($populerPages as $page)
                                <div class="min-w-full px-1">
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($page as $mobil)
                                            <x-mobil-card
                                                :mobil="$mobil"
                                                :likedMobilIds="$likedMobilIds"
                                                :showPopulerBadge="true"
                                                class="min-w-0 px-0"
                                            />
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- dots (1 dot per PAGE) --}}
                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($populerPages as $i => $p)
                            <button type="button"
                                    class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                    data-cards-dot="{{ $i }}"
                                    aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- DESKTOP: grid 3 kolom --}}
            <div class="mt-6 hidden lg:grid grid-cols-3 gap-6">
                @foreach($populers as $mobil)
                    <x-mobil-card
                        :mobil="$mobil"
                        :likedMobilIds="$likedMobilIds"
                        :showPopulerBadge="true"
                        class="min-w-0 px-0"
                    />
                @endforeach
            </div>
        @endif
    </section>

    {{-- CARI MOBIL BERDASARKAN HARGA (NO REFRESH) --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6" data-price-section>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                Cari mobil berdasarkan harga
            </h2>
        </div>

        {{-- Tabs --}}
        <div class="mt-4 flex items-center gap-3 sm:gap-6 overflow-x-auto border-b border-slate-100 pb-2">
            @foreach(($priceTabs ?? []) as $key => $tab)
                @php $isActive = ($activePrice ?? '300') === $key; @endphp

                <button type="button"
                    data-price-tab="{{ $key }}"
                    class="relative whitespace-nowrap px-1 pb-3 text-sm sm:text-base font-medium transition
                        {{ $isActive ? 'text-sky-600' : 'text-slate-500 hover:text-slate-700' }}">
                    {{ $tab['label'] }}

                    <span class="absolute left-0 right-0 -bottom-[1px] h-[2px]
                                {{ $isActive ? 'bg-sky-600' : 'bg-transparent' }}"
                        data-price-underline="{{ $key }}"></span>
                </button>
            @endforeach
        </div>

        {{-- Visible cards container (akan di-swap via JS) --}}
        <div class="mt-6" id="price-cards" data-price-target></div>

        {{-- Hidden templates per tab (render dari DB) --}}
        @foreach(($priceTabs ?? []) as $key => $tab)
            <div class="hidden" data-price-template="{{ $key }}">
                @php $list = $priceSections[$key] ?? collect(); @endphp

                @if($list->count() === 0)
                    <div class="rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
                        Tidak ada mobil pada range harga ini.
                    </div>
                @else
                    @php
                        $pages = $list->chunk(2); // tablet: 2 card per page
                    @endphp

                    {{-- MOBILE: carousel (1 card per view) --}}
                    <div class="sm:hidden">
                        <div class="relative" data-cards-carousel>
                            <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                                <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                                    @foreach($list as $mobil)
                                        <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" />
                                    @endforeach
                                </div>
                            </div>

                            {{-- dots (1 dot per card) --}}
                            <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                                @foreach($list as $i => $m)
                                    <button type="button"
                                        class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                        data-cards-dot="{{ $i }}"
                                        aria-label="slide {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- TABLET (SM): carousel (2 card per page) --}}
                    <div class="hidden sm:block lg:hidden">
                        <div class="relative" data-cards-carousel data-cards-mode="paged">
                            <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                                <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                                    @foreach($pages as $page)
                                        <div class="min-w-full px-1">
                                            <div class="grid grid-cols-2 gap-4">
                                                @foreach($page as $mobil)
                                                    <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- dots (1 dot per PAGE) --}}
                            <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                                @foreach($pages as $i => $p)
                                    <button type="button"
                                        class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                        data-cards-dot="{{ $i }}"
                                        aria-label="slide {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- DESKTOP: grid 3 kolom --}}
                    <div class="hidden lg:grid grid-cols-3 gap-6">
                        @foreach($list as $mobil)
                            <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                        @endforeach
                    </div>

                @endif
            </div>
        @endforeach
    </section>

    {{-- CARI MOBIL BERDASARKAN TAHUN (NO REFRESH) --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6" data-year-section>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Cari Mobil</span>
                <span class="block sm:inline">Berdasarkan Tahun</span>
            </h2>

            <a href="#"
            class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg">
                <span class="sm:hidden">Lihat semua</span>
                <span class="hidden sm:inline">Lihat semua mobil</span>
                <i class="bi bi-chevron-right text-[16px]"></i>
            </a>
        </div>

        {{-- Tabs --}}
        <div class="mt-4 flex items-center gap-3 sm:gap-6 overflow-x-auto border-b border-slate-100 pb-2">
            @foreach(($yearTabs ?? []) as $key => $tab)
                @php $isActive = ($activeYear ?? '3') === $key; @endphp

                <button type="button"
                    data-year-tab="{{ $key }}"
                    class="relative whitespace-nowrap px-1 pb-3 text-sm sm:text-base font-medium transition
                        {{ $isActive ? 'text-sky-600' : 'text-slate-500 hover:text-slate-700' }}">
                    {{ $tab['label'] }}

                    <span class="absolute left-0 right-0 -bottom-[1px] h-[2px]
                                {{ $isActive ? 'bg-sky-600' : 'bg-transparent' }}"></span>
                </button>
            @endforeach
        </div>

        {{-- Visible cards container --}}
        <div class="mt-6" id="year-cards" data-year-target></div>

        {{-- Hidden templates --}}
        @foreach(($yearTabs ?? []) as $key => $tab)
            <div class="hidden" data-year-template="{{ $key }}">
                @php $list = $yearSections[$key] ?? collect(); @endphp

                @if($list->count() === 0)
                    <div class="rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
                        Tidak ada mobil pada range tahun ini.
                    </div>
                @else
                    @php
                        $pages = $list->chunk(2); // tablet: 2 card per page
                    @endphp

                    {{-- MOBILE: carousel (1 card per view) --}}
                    <div class="sm:hidden">
                        <div class="relative" data-cards-carousel>
                            <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                                <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                                    @foreach($list as $mobil)
                                        <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" />
                                    @endforeach
                                </div>
                            </div>

                            {{-- dots (1 dot per card) --}}
                            <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                                @foreach($list as $i => $m)
                                    <button type="button"
                                        class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                        data-cards-dot="{{ $i }}"
                                        aria-label="slide {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- TABLET (SM): carousel (2 card per page) --}}
                    <div class="hidden sm:block lg:hidden">
                        <div class="relative" data-cards-carousel data-cards-mode="paged">
                            <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                                <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                                    @foreach($pages as $page)
                                        <div class="min-w-full px-1">
                                            <div class="grid grid-cols-2 gap-4">
                                                @foreach($page as $mobil)
                                                    <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- dots (1 dot per PAGE) --}}
                            <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                                @foreach($pages as $i => $p)
                                    <button type="button"
                                        class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                        data-cards-dot="{{ $i }}"
                                        aria-label="slide {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- DESKTOP: grid 3 kolom --}}
                    <div class="hidden lg:grid grid-cols-3 gap-6">
                        @foreach($list as $mobil)
                            <x-mobil-card :mobil="$mobil" :likedMobilIds="$likedMobilIds" class="min-w-0 px-0" />
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </section>

 {{-- ===================================================== --}}
    {{-- SECTION: LIHAT SIAPA YANG LAGI BUTUH MOBIL (REQUEST) --}}
    {{-- ===================================================== --}}
    @if(isset($requests) && count($requests))
    <section class="mx-auto max-w-[1200px] px-4 sm:px-6 pb-16 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Lihat Siapa yang</span>
                <span class="block sm:inline">Lagi Butuh Mobil</span>
            </h2>

            <a href="{{ route('butuh_mobil.index') }}"
            class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg">
                Lihat Semua
                <i class="bi bi-chevron-right text-[16px]"></i>
            </a>
        </div>

        {{-- MOBILE: carousel (1 card per view) --}}
        <div class="mt-6 sm:hidden">
            <div class="relative" data-cards-carousel>
                <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                    <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                        @foreach($requests as $r)
                            <div class="min-w-full px-1">
                                <div class="rounded-2xl bg-white my-4 shadow-md ring-1 ring-slate-100 overflow-hidden">
                                    <div class="p-5">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $r['avatar'] }}" alt="{{ $r['name'] }}"
                                                class="h-10 w-10 rounded-full object-cover ring-2 ring-white bg-slate-100" />
                                            <div class="leading-tight">
                                                <div class="text-lg font-medium text-slate-900">{{ $r['name'] }}</div>
                                                <div class=" text-sm text-slate-500">{{ $r['city'] }}</div>
                                            </div>
                                        </div>

                                        <div class="my-4 h-px bg-slate-100"></div>

                                        <div class="text-base text-slate-500">{{ $r['car'] }}</div>
                                        <div class="mt-1 text-xl font-semibold text-slate-900">{{ $r['price'] }}</div>

                                        <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                                            <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                                @foreach($r['tags'] as $t)
                                                    <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1 text-sm text-slate-600 whitespace-nowrap">
                                                        {{ $t }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        @php
                                            $chatUrl = !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current();
                                        @endphp

                                        @auth
                                            @if(!empty($r['user_id']) && auth()->id() != $r['user_id'])
                                                <a href="{{ $chatUrl }}"
                                                class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition">
                                                    Tawarkan Mobil Saya
                                                </a>
                                            @else
                                                <button type="button" disabled
                                                    class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-slate-300 px-4 py-3 text-base font-medium text-white cursor-not-allowed">
                                                    Tawarkan Mobil Saya
                                                </button>
                                            @endif
                                        @endauth

                                        @guest
                                            <button type="button"
                                                class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition"
                                                @click="
                                                    fetch('{{ route('auth.intended') }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ url: '{{ !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current() }}' })
                                                    }).finally(() => $store.authModal.open('login'))
                                                ">
                                                Tawarkan Mobil Saya
                                            </button>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- dots --}}
                <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                    @foreach($requests as $i => $r)
                        <button type="button"
                            class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                            data-cards-dot="{{ $i }}"
                            aria-label="slide {{ $i+1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABLET: carousel (2 card per view, per halaman) --}}
        <div class="mt-6 hidden sm:block lg:hidden">
            <div class="relative" data-cards-carousel data-cards-mode="paged">
                <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                    <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                        @foreach(collect($requests)->chunk(2) as $chunk)
                            {{-- 1 PAGE = 2 cards --}}
                            <div class="min-w-full px-1">
                                <div class="my-4 grid grid-cols-2 gap-4">
                                    @foreach($chunk as $r)
                                        <div class="rounded-2xl bg-white shadow-md ring-1 ring-slate-100 overflow-hidden">
                                            <div class="p-5">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $r['avatar'] }}" alt="{{ $r['name'] }}"
                                                        class="h-12 w-12 rounded-full object-cover ring-2 ring-white bg-slate-100" />
                                                    <div class="leading-tight min-w-0">
                                                        <div class="text-lg font-medium text-slate-900 truncate">{{ $r['name'] }}</div>
                                                        <div class="text-sm text-slate-500 truncate">{{ $r['city'] }}</div>
                                                    </div>
                                                </div>

                                                <div class="my-4 h-px bg-slate-100"></div>

                                                <div class="text-base text-slate-500 truncate">{{ $r['car'] }}</div>
                                                <div class="mt-1 text-xl font-semibold text-slate-900">{{ $r['price'] }}</div>

                                                <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                                                    <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                                        @foreach($r['tags'] as $t)
                                                            <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-base text-slate-600 whitespace-nowrap">
                                                                {{ $t }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @php
                        $chatUrl = !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current();
                    @endphp

                    @auth
                        @if(!empty($r['user_id']) && auth()->id() != $r['user_id'])
                            <a href="{{ $chatUrl }}"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition">
                                Tawarkan Mobil Saya
                            </a>
                        @else
                            <button type="button" disabled
                                class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-slate-300 px-4 py-3 text-base font-medium text-white cursor-not-allowed">
                                Tawarkan Mobil Saya
                            </button>
                        @endif
                    @endauth

                    @guest
                        <button type="button"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition"
                            @click="
                                fetch('{{ route('auth.intended') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ url: '{{ !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current() }}' })
                                }).finally(() => $store.authModal.open('login'))
                            ">
                            Tawarkan Mobil Saya
                        </button>
                    @endguest
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- dots --}}
                <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                    @foreach(collect($requests)->chunk(2) as $i => $chunk)
                        <button type="button"
                            class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                            data-cards-dot="{{ $i }}"
                            aria-label="page {{ $i+1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DESKTOP: grid --}}
        <div class="mt-6 hidden lg:grid grid-cols-4 gap-4">
            @foreach($requests as $r)
                <div class="rounded-2xl bg-white shadow-md ring-1 ring-slate-100 overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $r['avatar'] }}" alt="{{ $r['name'] }}"
                                class="h-12 w-12 rounded-full object-cover ring-2 ring-white bg-slate-100" />
                            <div class="leading-tight min-w-0">
                                <div class="text-lg font-medium text-slate-900 truncate">{{ $r['name'] }}</div>
                                <div class="text-sm text-slate-500 truncate">{{ $r['city'] }}</div>
                            </div>
                        </div>

                        <div class="my-4 h-px bg-slate-100"></div>

                        <div class="text-base text-slate-500 truncate">{{ $r['car'] }}</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">{{ $r['price'] }}</div>

                        <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                            <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                @foreach($r['tags'] as $t)
                                    <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm text-slate-600 whitespace-nowrap">
                                        {{ $t }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        @php
                        $chatUrl = !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current();
                    @endphp

                    @auth
                        @if(!empty($r['user_id']) && auth()->id() != $r['user_id'])
                            <a href="{{ $chatUrl }}"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition">
                                Tawarkan Mobil Saya
                            </a>
                        @else
                            <button type="button" disabled
                                class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-slate-300 px-4 py-3 text-base font-medium text-white cursor-not-allowed">
                                Tawarkan Mobil Saya
                            </button>
                        @endif
                    @endauth

                    @guest
                        <button type="button"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-medium text-white hover:bg-sky-600 transition"
                            @click="
                                fetch('{{ route('auth.intended') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ url: '{{ !empty($r['user_id']) ? route('chat.show', $r['user_id']) : url()->current() }}' })
                                }).finally(() => $store.authModal.open('login'))
                            ">
                            Tawarkan Mobil Saya
                        </button>
                    @endguest
                    </div>
                </div>
            @endforeach
        </div>

    </section>
    @endif

    {{-- MOTOR POPULER BERDASARKAN TIPE BODY (CAROUSEL ICON) --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-10 pt-6">
        <h2 class="text-lg font-medium text-slate-900 md:text-2xl">
            Motor Populer Berdasarkan Tipe Body
        </h2>

        @php
            $motorBodyTypes = [
                ['label' => 'Scooter',    'img' => 'assets/img/motor-populer/scooter.png'],
                ['label' => 'Sport',      'img' => 'assets/img/motor-populer/sport.png'],
                ['label' => 'Off Road',   'img' => 'assets/img/motor-populer/offroad.png'],
                ['label' => 'Cafe Racer', 'img' => 'assets/img/motor-populer/cafe-racer.png'],
                ['label' => 'Cruiser',    'img' => 'assets/img/motor-populer/cruiser.png'],
                ['label' => 'Street',     'img' => 'assets/img/motor-populer/street.png'],
                ['label' => 'Moped',      'img' => 'assets/img/motor-populer/moped.png'],
            ];
        @endphp
        <div class="relative mt-5">
            {{-- tombol kiri --}}
            <button type="button"
                class="absolute left-0 top-1/2 z-10 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow ring-1 ring-slate-200 hover:bg-slate-50"
                data-icon-prev
                aria-label="prev">
                <i class="bi bi-chevron-left text-slate-700"></i>
            </button>

            {{-- VIEWPORT --}}
            <div
                class="
                    overflow-x-auto
                    px-0 sm:px-0 lg:px-0
                    cursor-grab active:cursor-grabbing
                    no-scrollbar
                "
                data-icon-carousel
                >

                <div
                    data-icon-track
                    class="
                        flex items-center
                        flex-nowrap
                        justify-start lg:justify-between
                        gap-2 sm:gap-4 lg:gap-6
                        py-2
                    "
                    style="scroll-snap-type: x proximity; -webkit-overflow-scrolling: touch;"
                >
                    @foreach($motorBodyTypes as $t)
                            <a href="{{ url('/lihat-semua-motor') . '?q=&jenis_kendaraan=' . urlencode($t['label']) }}"
                            draggable="false"
                            class="flex flex-col items-center justify-center shrink-0 text-center select-none"
                            style="scroll-snap-align: start;"
                            >
                            <div class="flex h-24 w-36 items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-100">
                                <img
                                    src="{{ asset($t['img']) }}"
                                    alt="{{ $t['label'] }}"
                                    class="h-18 w-18 object-contain"
                                    draggable="false"
                                />
                            </div>

                            <div class="mt-2 text-md font-medium text-slate-700">
                                {{ $t['label'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>


            {{-- tombol kanan --}}
            <button type="button"
                class="absolute right-0 top-1/2 z-10 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow ring-1 ring-slate-200 hover:bg-slate-50"
                data-icon-next
                aria-label="next">
                <i class="bi bi-chevron-right text-slate-700"></i>
            </button>
        </div>

    </section>

    {{-- MOTOR POPULER DAN TERBARU --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Motor Populer dan</span>
                <span class="block sm:inline">Terbaru</span>
            </h2>

            {{-- sesuaikan route kalau sudah ada --}}
            <a href="{{ route('motor.index') }}"
            class="inline-flex items-center gap-2 text-[14px] font-normal text-sky-500 hover:text-sky-600 sm:text-base md:text-lg">
                <span class="sm:hidden">Lihat semua</span>
                <span class="hidden sm:inline">Lihat semua motor</span>
                <i class="bi bi-chevron-right text-[16px]"></i>
            </a>
        </div>

        @if(empty($populerMotors) || $populerMotors->count() === 0)
            <div class="mt-6 rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
                Belum ada data motor populer.
            </div>
        @else
            @php $motorPages = $populerMotors->chunk(2); @endphp

            {{-- MOBILE --}}
            <div class="mt-6 sm:hidden">
                <div class="relative" data-cards-carousel>
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($populerMotors as $motor)
                                <div class="min-w-full px-1">
                                    <x-motor-card :motor="$motor" :likedMotorIds="$likedMotorIds" :showPopulerBadge="true" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($populerMotors as $i => $m)
                            <button type="button"
                                class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                data-cards-dot="{{ $i }}"
                                aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TABLET --}}
            <div class="mt-6 hidden sm:block lg:hidden">
                <div class="relative" data-cards-carousel data-cards-mode="paged">
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-cards-viewport>
                        <div class="flex transition-transform duration-300 ease-out" data-cards-track data-index="0">
                            @foreach($motorPages as $page)
                                <div class="min-w-full px-1">
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach($page as $motor)
                                            <x-motor-card :motor="$motor" :likedMotorIds="$likedMotorIds" :showPopulerBadge="true" />
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-2" data-cards-dots>
                        @foreach($motorPages as $i => $p)
                            <button type="button"
                                class="h-2 w-2 rounded-full {{ $i===0 ? 'bg-sky-600' : 'bg-slate-300' }}"
                                data-cards-dot="{{ $i }}"
                                aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- DESKTOP --}}
            <div class="mt-6 hidden lg:grid grid-cols-3 gap-6">
                @foreach($populerMotors as $motor)
                    <x-motor-card :motor="$motor" :likedMotorIds="$likedMotorIds" :showPopulerBadge="true" />
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection
