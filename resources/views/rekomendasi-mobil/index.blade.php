@extends('layouts.main')

@section('content')
<section
  class="relative mx-auto max-w-[1200px] px-4 pb-16 pt-6 sm:pt-8"
  x-data="{
    openFilter: false,

    // Multi accordion state (bisa banyak terbuka bersamaan)
    openAccs: {
      jenis_kendaraan: false,
      jenis_mobil: false,
      transmisi: false,
      bahan_bakar: false,
      merk: false,
      tempat_duduk: false,
      kapasitas: false,
      konsumsi: false,
      harga: false,
      jarak: false,
      security: false,
      exterior: false,
      convenience: false,
      performance: false,
    },

    sectionHasValue(key) {
    const map = {
        jenis_kendaraan: 'jenis_kendaraan',
        jenis_mobil: 'jenis_mobil[]',
        transmisi: 'transmisi[]',
        bahan_bakar: 'bahan_bakar[]',
        merk: 'merk[]',
        tempat_duduk: 'tempat_duduk[]',
        kapasitas: 'kapasitas[]',
        konsumsi: 'konsumsi[]',
        harga: 'harga[]',
        jarak: 'jarak[]',
        security: 'security[]',
        exterior: 'exterior[]',
        convenience: 'convenience[]',
        performance: 'performance[]',
    };

    const param = map[key];
    if (!param) return false;
    return this.hasAny(param);
    },

    toggleAcc(key) {
    const willOpen = !this.openAccs[key];

    // toggle target
    this.openAccs[key] = willOpen;

    // kalau membuka section baru (selain jenis_kendaraan):
    // tutup section lain yang BELUM punya pilihan (kecuali jenis_kendaraan dan section yang dibuka)
    if (willOpen && key !== 'jenis_kendaraan') {
        Object.keys(this.openAccs).forEach(k => {
        if (k === 'jenis_kendaraan') return; // jenis_kendaraan tidak ikut auto-close
        if (k === key) return;

        // kalau belum ada value (belum dipilih) → tutup
        if (!this.sectionHasValue(k)) this.openAccs[k] = false;
        });
    }
    },



    hasAny(name) {
    const params = new URLSearchParams(window.location.search);

    if (name.endsWith('[]')) {
        const base = name.slice(0, -2);

        if (params.getAll(name).length > 0) return true;

        if (params.getAll(base).length > 0) return true;

        for (const k of params.keys()) {
        if (k === base) return true;
        if (k.startsWith(base + '[')) return true; // <- ini kuncinya
        }

        return false;
    }

    // normal input
    const v = params.get(name);
    return v !== null && v !== '';
    },

    syncOpenAccsFromQuery() {
    Object.keys(this.openAccs).forEach(k => this.openAccs[k] = false);

    // buka section yang punya value
    if (this.hasAny('jenis_kendaraan')) this.openAccs.jenis_kendaraan = true;
    if (this.hasAny('jenis_mobil[]')) this.openAccs.jenis_mobil = true;
    if (this.hasAny('transmisi[]')) this.openAccs.transmisi = true;
    if (this.hasAny('bahan_bakar[]')) this.openAccs.bahan_bakar = true;
    if (this.hasAny('merk[]')) this.openAccs.merk = true;
    if (this.hasAny('tempat_duduk[]')) this.openAccs.tempat_duduk = true;
    if (this.hasAny('kapasitas[]')) this.openAccs.kapasitas = true;
    if (this.hasAny('konsumsi[]')) this.openAccs.konsumsi = true;
    if (this.hasAny('harga[]')) this.openAccs.harga = true;
    if (this.hasAny('jarak[]')) this.openAccs.jarak = true;

    if (this.hasAny('security[]')) this.openAccs.security = true;
    if (this.hasAny('exterior[]')) this.openAccs.exterior = true;
    if (this.hasAny('convenience[]')) this.openAccs.convenience = true;
    if (this.hasAny('performance[]')) this.openAccs.performance = true;
    },


    init() {
      // Sync accordion open state saat load
      this.syncOpenAccsFromQuery();

      // Setiap kali panel filter dibuka, sync lagi (biar setelah refresh/klik filter konsisten)
      this.$watch('openFilter', (v) => {
    if (v) {
        this.syncOpenAccsFromQuery();      // buka yang punya value
        this.openAccs.jenis_kendaraan = true; // paksa jenis kendaraan terbuka saat drawer baru dibuka
    }
    });
    }
  }"
  @keydown.escape.window="openFilter=false"
>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">
            {{ ($pageMode ?? 'rekomendasi') === 'populer' ? 'Mobil Populer dan Terbaru' : 'Direkomendasikan untuk anda' }}
        </h1>

        {{-- Search bar --}}
        <form method="GET" x-ref="filterForm"
            action="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}"
            class="flex w-full items-center gap-3 sm:w-[520px]">

            <div class="relative w-full">
                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari Kendaraan Sekarang"
                    class="w-full rounded-full bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 ring-1 ring-slate-200 outline-none focus:ring-sky-500"
                />
            </div>

            {{-- Tombol filter (ikon) - placeholder UI seperti screenshot --}}
            <button
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#51A3EE] text-white shadow-sm hover:bg-sky-600 transition"
                aria-label="filter"
                @click="openFilter = !openFilter; if (openFilter) syncOpenAccsFromQuery()"
            >
                <i class="bi bi-sliders"></i>
            </button>

        </form>
    </div>

    {{-- Grid Cards --}}
    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($mobils as $mobil)
            @php
                $imgUrl = function ($path) {
                    if (empty($path)) return 'https://placehold.co/1200x800?text=No+Image';
                    if (str_starts_with($path, 'http')) return $path;

                    // kalau sudah "storage/..." jangan double
                    if (str_starts_with($path, 'storage/')) return asset(ltrim($path,'/'));

                    // kalau memang path seharusnya dari storage
                    return asset('storage/' . ltrim($path, '/'));
                };
                $images = [];

                if (!empty($mobil->gambar_mobil)) $images[] = $mobil->gambar_mobil;

                if (is_array($mobil->galery_mobil)) {
                    foreach ($mobil->galery_mobil as $g) {
                        if (!empty($g)) $images[] = $g;
                    }
                }

                if (count($images) === 0) $images[] = 'assets/img/homepage/hero.png';

                $lokasi = $mobil->lokasi ?? 'Indonesia';
                $title  = trim(($mobil->merk_mobil ?? '') . ' ' . ($mobil->model_mobil ?? ''));
                $ulasan = (int)($mobil->likes_mobil ?? 10);
            @endphp

            <div class="rounded-2xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] ring-1 ring-slate-100 overflow-hidden">
                {{-- IMAGE SLIDER --}}
                <div class="relative" data-carousel>
                    @php
                        $isLiked = auth()->check() && in_array($mobil->id, $likedMobilIds ?? []);
                    @endphp
                    <button
                        type="button"
                        class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow ring-1 ring-slate-200 hover:bg-white"
                        data-like-btn="{{ $mobil->slug ?? $mobil->id }}"
                        data-mobil-id="{{ $mobil->slug ?? $mobil->id }}"
                        @click.prevent
                        aria-label="favorite"
                    >
                        <i class="bi bi-heart-fill {{ $isLiked ? 'text-red-500' : 'text-slate-400' }}"></i>
                    </button>

                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" data-carousel-viewport>
                        <div class="flex transition-transform duration-300 ease-out"
                             data-carousel-track
                             data-index="0">
                            @foreach($images as $img)
                                <div class="min-w-full">
                                    <img
                                        src="{{ $imgUrl($img) }}"
                                        alt="{{ $title }}"
                                        class="h-[200px] w-full object-cover select-none"
                                        draggable="false"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="absolute bottom-3 left-0 right-0 z-10 flex items-center justify-center gap-1.5"
                         data-carousel-dots>
                        @foreach($images as $i => $img)
                            <button type="button"
                                class="h-1.5 w-1.5 rounded-full {{ $i===0 ? 'bg-white' : 'bg-white/50' }}"
                                data-dot="{{ $i }}"
                                aria-label="slide {{ $i+1 }}"></button>
                        @endforeach
                    </div>
                </div>

                        {{-- CONTENT --}}
                        <div class="p-4">
                            {{-- Title + rating (sejajar seperti contoh) --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-lg font-medium text-slate-900 truncate">
                                        {{ $title ?: 'Mobil' }}
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-2 text-lg">
                                    <i class="bi bi-star-fill text-amber-400"></i>
                                    <span class="font-medium text-slate-800">4.9</span>
                                </div>
                            </div>

                            {{-- lokasi --}}
                            <div class="mt-2 flex items-center gap-2 text-base text-slate-500">
                                <i class="bi bi-geo-alt"></i>
                                <span class="truncate">{{ $lokasi }}</span>
                            </div>

                            {{-- BADGES: 1 baris, overflow bisa digeser --}}
                            @php
                                $badges = [];

                                if (!empty($mobil->jenis_kendaraan)) {
                                    $badges[] = [
                                        'icon' => 'bi-car-front',
                                        'text' => $mobil->jenis_kendaraan,
                                    ];
                                }

                                if (!empty($mobil->merk_mobil)) {
                                    $badges[] = [
                                        'icon' => 'bi-award', // boleh diganti bi-building kalau mau lebih "brand"
                                        'text' => $mobil->merk_mobil,
                                    ];
                                }

                                if (!empty($mobil->transmisi_mobil)) {
                                    $badges[] = [
                                        'icon' => 'bi-gear',
                                        'text' => $mobil->transmisi_mobil,
                                    ];
                                }
                            @endphp

                            <div class="mt-3 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                                {{-- IMPORTANT: jangan pakai w-full & justify-between, biar scroll jalan natural --}}
                                <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                    @foreach($badges as $badge)
                                        <span
                                            class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1 text-base font-normal text-[#707070] ring-1 ring-slate-100 whitespace-nowrap"
                                        >
                                            <i class="bi {{ $badge['icon'] }} text-slate-500"></i>
                                            <span>{{ $badge['text'] }}</span>
                                        </span>
                                    @endforeach

                                    {{-- +3 badge --}}
                                    <span
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1 text-base font-normal text-[#707070] ring-1 ring-slate-100 whitespace-nowrap"
                                    >
                                        <span>+3</span>
                                    </span>
                                </div>
                            </div>

                        </div>

                <div class="h-px bg-slate-100"></div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-between p-4">
                    <div class="flex flex-col">
                        <span class="text-base text-slate-500">Harga</span>
                        <span class="mt-1 text-lg font-medium text-slate-900">
                            {{ rupiahJuta($mobil->harga_mobil) }}
                        </span>
                    </div>

                    <a href="{{ route('mobil.show', $mobil) }}"
                       class="inline-flex items-center justify-center rounded-full bg-slate-50 px-5 py-2 text-base font-medium text-sky-500 ring-1 ring-slate-100 hover:bg-sky-50 hover:text-sky-600 transition">
                        Detail
                    </a>
                </div>
            </div>

            @empty
                {{-- Empty state --}}
                <div class="col-span-full">
                    <div class="rounded-2xl bg-slate-50 shadow-md p-8 text-center">
                        <div class="text-xl font-semibold text-slate-900">
                            Kendaraan yang anda cari belum tersedia
                        </div>
                        <div class="mt-2 text-sm text-slate-500">
                            Coba ubah kata kunci atau kurangi filter yang dipilih.
                        </div>

                        <a
                            href="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}"
                            class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-[#51A3EE] px-5 text-base font-semibold text-white hover:bg-sky-600 transition"
                        >
                            Reset Filter
                        </a>
                    </div>
                </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $mobils->links('components.pagination-circle') }}
    </div>

    {{-- FILTER (FINAL: Desktop inside container, Mobile/Tablet drawer) --}}

    {{-- ========================= --}}
    {{-- DESKTOP PANEL (inside container) --}}
    {{-- ========================= --}}
    <div x-show="openFilter" x-cloak class="hidden lg:block">
        <div
            class="absolute right-0 top-[76px] z-[9999] w-[360px] rounded-3xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.12)] ring-1 ring-slate-200 overflow-hidden"
            @click.stop>

            <form
            x-ref="filterFormDesktop"
            method="GET"
            action="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}">
            <input type="hidden" name="q" value="{{ request('q') }}"/>

            {{-- Scroll area (desktop) --}}
            <div class="max-h-[520px] overflow-y-auto px-5 py-4 pb-4">

                @php
                $cb = "h-5 w-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500";
                $rowBtn = "flex items-center justify-between py-3 text-sm text-slate-800 w-full";
                $listWrap = "pb-3 space-y-3";
                $labelCls = "flex items-center gap-3 text-sm text-slate-700 cursor-pointer select-none";
                @endphp

                {{-- Jenis Kendaraan (accordion seperti filter lain, tapi default expand) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jenis_kendaraan')">
                    <span>Jenis Kendaraan</span>
                    <i class="bi" :class="openAccs.jenis_kendaraan ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.jenis_kendaraan" class="{{ $listWrap }}">
                    @php
                    $iconMap = [
                        'MPV' => 'assets/img/tipe-body/mpv.png',
                        'SUV' => 'assets/img/tipe-body/suv.png',
                        'Crossover' => 'assets/img/tipe-body/crossover.png',
                        'Minivans' => 'assets/img/tipe-body/minivans.png',
                        'Wagon' => 'assets/img/tipe-body/wagon.png',
                        'Sedan' => 'assets/img/tipe-body/sedan.png',
                        'Coupe' => 'assets/img/tipe-body/coupe.png',
                        'Pickup Truck' => 'assets/img/tipe-body/pickup.png',
                        'Van' => 'assets/img/tipe-body/van.png',
                        'Convertible' => 'assets/img/tipe-body/convertible.png',
                        'Sportback' => 'assets/img/tipe-body/sportback.png',
                        'Hatchback' => 'assets/img/tipe-body/hatchback.png',
                    ];
                    @endphp

                    <div class="grid grid-cols-3 gap-3">
                    @foreach(($filters['jenis_kendaraan'] ?? collect()) as $jk)
                        @php
                        $img = $iconMap[$jk] ?? 'assets/img/homepage/hero.png';
                        $active = request('jenis_kendaraan') === $jk;
                        @endphp

                        <label class="cursor-pointer select-none">
                        <input
                            type="radio"
                            name="jenis_kendaraan"
                            value="{{ $jk }}"
                            class="hidden"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $active ? 'checked' : '' }}
                        />
                        <div class="rounded-2xl bg-slate-50 p-2 ring-1 transition {{ $active ? 'ring-sky-500' : 'ring-slate-200 hover:ring-slate-300' }}">
                            <div class="flex h-12 items-center justify-center">
                            <img src="{{ asset($img) }}" class="h-8 w-auto object-contain" alt="{{ $jk }}">
                            </div>
                            <div class="mt-1 text-center text-[12px] font-medium text-slate-700">{{ $jk }}</div>
                        </div>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Mobil Jenis --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jenis_mobil')">
                    <span>Mobil Jenis</span>
                    <i class="bi" :class="openAccs.jenis_mobil ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.jenis_mobil" class="{{ $listWrap }}">
                    @foreach(($filters['jenis_mobil'] ?? collect()) as $opt)
                    @php $checked = in_array($opt, (array)request('jenis_mobil', []), true); @endphp
                    <label class="{{ $labelCls }}">
                        <input type="checkbox" name="jenis_mobil[]" value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                        {{ $checked ? 'checked' : '' }}>
                        <span>{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
                </div>

                {{-- Transmisi --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('transmisi')">
                    <span>Transmisi</span>
                    <i class="bi" :class="openAccs.transmisi ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.transmisi" class="{{ $listWrap }}">
                    @foreach(($filters['transmisi'] ?? collect()) as $opt)
                    @php $checked = in_array($opt, (array)request('transmisi', []), true); @endphp
                    <label class="{{ $labelCls }}">
                        <input type="checkbox" name="transmisi[]" value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                        {{ $checked ? 'checked' : '' }}>
                        <span>{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
                </div>

                {{-- Jenis Bahan Bakar --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('bahan_bakar')">
                    <span>Jenis Bahan Bakar</span>
                    <i class="bi" :class="openAccs.bahan_bakar ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.bahan_bakar" class="{{ $listWrap }}">
                    @foreach(($filters['bahan_bakar'] ?? collect()) as $opt)
                    @php $checked = in_array($opt, (array)request('bahan_bakar', []), true); @endphp
                    <label class="{{ $labelCls }}">
                        <input type="checkbox" name="bahan_bakar[]" value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                        {{ $checked ? 'checked' : '' }}>
                        <span>{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
                </div>

                {{-- Pilih Merek Favorit Anda --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('merk')">
                    <span>Pilih Merek Favorit Anda</span>
                    <i class="bi" :class="openAccs.merk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.merk" class="{{ $listWrap }}">
                    @foreach(($filters['merks'] ?? collect()) as $opt)
                    @php $checked = in_array($opt, (array)request('merk', []), true); @endphp
                    <label class="{{ $labelCls }}">
                        <input type="checkbox" name="merk[]" value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                        {{ $checked ? 'checked' : '' }}>
                        <span>{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
                </div>

                {{-- Tempat Duduk --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('tempat_duduk')">
                    <span>Tempat Duduk</span>
                    <i class="bi" :class="openAccs.tempat_duduk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.tempat_duduk" class="{{ $listWrap }}">
                    @foreach(($filters['seats'] ?? collect()) as $opt)
                    @php $checked = in_array((string)$opt, array_map('strval',(array)request('tempat_duduk', [])), true); @endphp
                    <label class="{{ $labelCls }}">
                        <input type="checkbox" name="tempat_duduk[]" value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                        {{ $checked ? 'checked' : '' }}>
                        <span>{{ $opt }}</span>
                    </label>
                    @endforeach
                </div>
                </div>

                {{-- Kapasitas Mesin (range) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('kapasitas')">
                    <span>Kapasitas Mesin</span>
                    <i class="bi" :class="openAccs.kapasitas ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.kapasitas" class="{{ $listWrap }}">
                    @php
                    $kapRanges = [
                        ['key' => 'lt_1000',   'label' => 'Dibawah 1000 Cc'],
                        ['key' => '1000_2000', 'label' => '1000 Cc to 2000 Cc'],
                        ['key' => '2000_3000', 'label' => '2000 Cc to 3000 Cc'],
                        ['key' => 'gt_3000',   'label' => 'Di Atas 3000 Cc'],
                    ];
                    $selectedKap = (array) request('kapasitas', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($kapRanges as $r)
                        @php $checked = in_array($r['key'], $selectedKap, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="kapasitas[]"
                            value="{{ $r['key'] }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $r['label'] }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Konsumsi BBM Jalan Tol (range + irit) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('konsumsi')">
                    <span>Konsumsi BBM Jalan Tol</span>
                    <i class="bi" :class="openAccs.konsumsi ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.konsumsi" class="{{ $listWrap }}">
                    @php
                    $kmplRanges = [
                        ['key' => '3_10',   'label' => '3 kmpl to 10 kmpl'],
                        ['key' => '10_15',  'label' => '11 kmpl to 15 kmpl'],
                        ['key' => 'irit',   'label' => 'Paling Irit BBM'],
                    ];
                    $selectedKonsumsi = (array) request('konsumsi', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($kmplRanges as $r)
                        @php $checked = in_array($r['key'], $selectedKonsumsi, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="konsumsi[]"
                            value="{{ $r['key'] }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $r['label'] }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Harga OTR (range) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('harga')">
                    <span>Harga OTR</span>
                    <i class="bi" :class="openAccs.harga ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.harga" class="{{ $listWrap }}">
                    @php
                    $hargaRanges = [
                        ['key' => 'lt_150',    'label' => 'Dibawah Rp 150 Juta'],
                        ['key' => 'lt_200',    'label' => 'Dibawah Rp 200 Juta'],
                        ['key' => '200_400',   'label' => 'Rp 200 – 400 Juta'],
                        ['key' => '400_800',   'label' => 'Rp 400 – 800 Juta'],
                        ['key' => '800_1200',  'label' => 'Rp 800 – Rp 1.2 M'],
                    ];
                    $selectedHarga = (array) request('harga', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($hargaRanges as $r)
                        @php $checked = in_array($r['key'], $selectedHarga, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="harga[]"
                            value="{{ $r['key'] }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $r['label'] }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Security (fixed 7 checkbox) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('security')">
                    <span>Security</span>
                    <i class="bi" :class="openAccs.security ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.security" class="{{ $listWrap }}">
                    @php
                    $securityOptions = [
                        'ABS',
                        'Bantuan Rem',
                        'EBD',
                        'Kontrol Stabilitas Kendaraan',
                        'Airbags',
                        'Monitor Tekanan Ban',
                        'Bantuan Hill Start',
                    ];
                    $selectedSecurity = (array) request('security', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($securityOptions as $opt)
                        @php $checked = in_array($opt, $selectedSecurity, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="security[]"
                            value="{{ $opt }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $opt }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Exterior (fixed checkbox) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('exterior')">
                    <span>Exterior</span>
                    <i class="bi" :class="openAccs.exterior ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.exterior" class="{{ $listWrap }}">
                    @php
                    $exteriorOptions = [
                        'Sunroof',
                        'Moonroof',
                        'Headlamp Otomatis',
                        'Wiper Penginderaan Hujan',
                        'High Ground Clearance',
                    ];
                    $selectedExterior = (array) request('exterior', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($exteriorOptions as $opt)
                        @php $checked = in_array($opt, $selectedExterior, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="exterior[]"
                            value="{{ $opt }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $opt }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Convenience (fixed checkbox) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('convenience')">
                    <span>Convenience</span>
                    <i class="bi" :class="openAccs.convenience ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.convenience" class="{{ $listWrap }}">
                    @php
                    $convenienceOptions = [
                        'Cruise Control',
                        'Paddle Shifters',
                        'Pengaturan Setir',
                        'Pembersih Udara',
                        'Kontrol Iklim Otomatis',
                        'Tekan Tombol Mulai',
                        'Ventilasi Ac belakang',
                        'Entri Tanpa Kunci',
                    ];
                    $selectedConvenience = (array) request('convenience', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($convenienceOptions as $opt)
                        @php $checked = in_array($opt, $selectedConvenience, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="convenience[]"
                            value="{{ $opt }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $opt }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Performance (fixed checkbox) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('performance')">
                    <span>Performance</span>
                    <i class="bi" :class="openAccs.performance ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.performance" class="{{ $listWrap }}">
                    @php
                    $performanceOptions = [
                        'Turbo Charger',
                        'AWD',
                        '4WD',
                    ];
                    $selectedPerformance = (array) request('performance', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($performanceOptions as $opt)
                        @php $checked = in_array($opt, $selectedPerformance, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="performance[]"
                            value="{{ $opt }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $opt }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

                {{-- Jarak Mengemudi (range fixed) --}}
                <div class="border-b border-slate-100">
                <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jarak')">
                    <span>Jarak Mengemudi</span>
                    <i class="bi" :class="openAccs.jarak ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
                </button>

                <div x-show="openAccs.jarak" class="{{ $listWrap }}">
                    @php
                    $jarakRanges = [
                        ['key' => 'lt_300',   'label' => 'Dibawah 300 Km'],
                        ['key' => '300_500',  'label' => '300–500 Km'],
                        ['key' => 'gt_500',   'label' => 'Diatas 500 Km'],
                    ];
                    $selectedJarak = (array) request('jarak', []);
                    @endphp

                    <div class="space-y-3">
                    @foreach($jarakRanges as $r)
                        @php $checked = in_array($r['key'], $selectedJarak, true); @endphp
                        <label class="{{ $labelCls }}">
                        <input
                            type="checkbox"
                            name="jarak[]"
                            value="{{ $r['key'] }}"
                            class="{{ $cb }}"
                            @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                            {{ $checked ? 'checked' : '' }}
                        >
                        <span>{{ $r['label'] }}</span>
                        </label>
                    @endforeach
                    </div>
                </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="bg-white px-5 pb-5 pt-4">
                <div class="h-px bg-slate-100"></div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                <button type="button"
                    class="h-11 rounded-xl bg-slate-100 text-sm font-medium text-slate-700 hover:bg-slate-200 transition"
                    @click="openFilter=false"
                >
                    Close
                </button>

                <a
                    href="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}"
                    class="h-11 rounded-xl bg-[#51A3EE] text-center text-sm font-semibold text-white leading-[44px] hover:bg-sky-600 transition"
                >
                    RESET
                </a>
                </div>
            </div>
            </form>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MOBILE/TABLET DRAWER (fullscreen) --}}
    {{-- ========================= --}}
    <div x-show="openFilter" x-cloak class="fixed inset-0 z-[9999] lg:hidden">
    <div class="absolute inset-0 bg-black/40" @click="openFilter=false"></div>

    <div
        class="absolute inset-y-0 right-0 w-[92%] max-w-[360px] bg-white shadow-2xl ring-1 ring-slate-200 rounded-l-3xl overflow-hidden"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.stop
    >

        <form
        x-ref="filterForm"
        method="GET"
        action="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}"
        class="h-full"
        >
        <input type="hidden" name="q" value="{{ request('q') }}"/>

        {{-- Scroll area --}}
        <div class="max-h-[calc(100vh-60px-92px)] overflow-y-auto px-5 py-4 pb-24">

            @php
            $iconMap = [
                'MPV' => 'assets/img/tipe-body/mpv.png',
                'SUV' => 'assets/img/tipe-body/suv.png',
                'Crossover' => 'assets/img/tipe-body/crossover.png',
                'Minivans' => 'assets/img/tipe-body/minivans.png',
                'Wagon' => 'assets/img/tipe-body/wagon.png',
                'Sedan' => 'assets/img/tipe-body/sedan.png',
                'Coupe' => 'assets/img/tipe-body/coupe.png',
                'Pickup Truck' => 'assets/img/tipe-body/pickup.png',
                'Van' => 'assets/img/tipe-body/van.png',
                'Convertible' => 'assets/img/tipe-body/convertible.png',
                'Sportback' => 'assets/img/tipe-body/sportback.png',
                'Hatchback' => 'assets/img/tipe-body/hatchback.png',
            ];

            $cb = "h-5 w-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500";
            $rowBtn = "flex items-center justify-between py-3 text-sm text-slate-800 w-full";
            $listWrap = "pb-3 space-y-3";
            $labelCls = "flex items-center gap-3 text-sm text-slate-700 cursor-pointer select-none";
            @endphp

            {{-- ACCORDION LIST --}}
            <div class="mt-5">

            {{-- Jenis Kendaraan (accordion seperti filter lain, default expand via openAcc) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jenis_kendaraan')">
                <span>Jenis Kendaraan</span>
                <i class="bi" :class="openAccs.jenis_kendaraan ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jenis_kendaraan" class="{{ $listWrap }}">
                <div class="grid grid-cols-3 gap-3">
                @foreach(($filters['jenis_kendaraan'] ?? collect()) as $jk)
                    @php
                    $img = $iconMap[$jk] ?? 'assets/img/homepage/hero.png';
                    $active = request('jenis_kendaraan') === $jk;
                    @endphp

                    <label class="cursor-pointer select-none">
                    <input
                        type="radio"
                        name="jenis_kendaraan"
                        value="{{ $jk }}"
                        class="hidden"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $active ? 'checked' : '' }}
                    />
                    <div class="rounded-2xl bg-slate-50 p-2 ring-1 transition {{ $active ? 'ring-sky-500' : 'ring-slate-200 hover:ring-slate-300' }}">
                        <div class="flex h-12 items-center justify-center">
                        <img src="{{ asset($img) }}" class="h-8 w-auto object-contain" alt="{{ $jk }}">
                        </div>
                        <div class="mt-1 text-center text-[12px] font-medium text-slate-700">{{ $jk }}</div>
                    </div>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Transmisi --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('transmisi')">
                <span>Transmisi</span>
                <i class="bi" :class="openAccs.transmisi ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.transmisi" class="{{ $listWrap }}">
                @foreach(($filters['transmisi'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('transmisi', []), true); @endphp
                <label class="{{ $labelCls }}">
                    <input type="checkbox" name="transmisi[]" value="{{ $opt }}"
                    class="{{ $cb }}"
                    @change="$nextTick(() => $refs.filterForm.submit())"
                    {{ $checked ? 'checked' : '' }}>
                    <span>{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            </div>

            {{-- Jenis Bahan Bakar --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('bahan_bakar')">
                <span>Jenis Bahan Bakar</span>
                <i class="bi" :class="openAccs.bahan_bakar ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.bahan_bakar" class="{{ $listWrap }}">
                @foreach(($filters['bahan_bakar'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('bahan_bakar', []), true); @endphp
                <label class="{{ $labelCls }}">
                    <input type="checkbox" name="bahan_bakar[]" value="{{ $opt }}"
                    class="{{ $cb }}"
                    @change="$nextTick(() => $refs.filterForm.submit())"
                    {{ $checked ? 'checked' : '' }}>
                    <span>{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            </div>

            {{-- Pilih Merek Favorit Anda --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('merk')">
                <span>Pilih Merek Favorit Anda</span>
                <i class="bi" :class="openAccs.merk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.merk" class="{{ $listWrap }}">
                @foreach(($filters['merks'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('merk', []), true); @endphp
                <label class="{{ $labelCls }}">
                    <input type="checkbox" name="merk[]" value="{{ $opt }}"
                    class="{{ $cb }}"
                    @change="$nextTick(() => $refs.filterForm.submit())"
                    {{ $checked ? 'checked' : '' }}>
                    <span>{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            </div>

            {{-- Tempat Duduk --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('tempat_duduk')">
                <span>Tempat Duduk</span>
                <i class="bi" :class="openAccs.tempat_duduk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.tempat_duduk" class="{{ $listWrap }}">
                @foreach(($filters['seats'] ?? collect()) as $opt)
                @php $checked = in_array((string)$opt, array_map('strval',(array)request('tempat_duduk', [])), true); @endphp
                <label class="{{ $labelCls }}">
                    <input type="checkbox" name="tempat_duduk[]" value="{{ $opt }}"
                    class="{{ $cb }}"
                    @change="$nextTick(() => $refs.filterForm.submit())"
                    {{ $checked ? 'checked' : '' }}>
                    <span>{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            </div>

            {{-- Kapasitas Mesin (range) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('kapasitas')">
                <span>Kapasitas Mesin</span>
                <i class="bi" :class="openAccs.kapasitas ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.kapasitas" class="{{ $listWrap }}">
                @php
                $kapRanges = [
                    ['key' => 'lt_1000',   'label' => 'Dibawah 1000 Cc'],
                    ['key' => '1000_2000', 'label' => '1000 Cc to 2000 Cc'],
                    ['key' => '2000_3000', 'label' => '2000 Cc to 3000 Cc'],
                    ['key' => 'gt_3000',   'label' => 'Di Atas 3000 Cc'],
                ];
                $selectedKap = (array) request('kapasitas', []);
                @endphp

                <div class="space-y-3">
                @foreach($kapRanges as $r)
                    @php $checked = in_array($r['key'], $selectedKap, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="kapasitas[]"
                        value="{{ $r['key'] }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $r['label'] }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Konsumsi BBM Jalan Tol (range + irit) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('konsumsi')">
                <span>Konsumsi BBM Jalan Tol</span>
                <i class="bi" :class="openAccs.konsumsi ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.konsumsi" class="{{ $listWrap }}">
                @php
                $kmplRanges = [
                    ['key' => '3_10',   'label' => '3 kmpl to 10 kmpl'],
                    ['key' => '10_15',  'label' => '11 kmpl to 15 kmpl'],
                    ['key' => 'irit',   'label' => 'Paling Irit BBM'],
                ];
                $selectedKonsumsi = (array) request('konsumsi', []);
                @endphp

                <div class="space-y-3">
                @foreach($kmplRanges as $r)
                    @php $checked = in_array($r['key'], $selectedKonsumsi, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="konsumsi[]"
                        value="{{ $r['key'] }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $r['label'] }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Harga OTR (range) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('harga')">
                <span>Harga OTR</span>
                <i class="bi" :class="openAccs.harga ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.harga" class="{{ $listWrap }}">
                @php
                $hargaRanges = [
                    ['key' => 'lt_150',    'label' => 'Dibawah Rp 150 Juta'],
                    ['key' => 'lt_200',    'label' => 'Dibawah Rp 200 Juta'],
                    ['key' => '200_400',   'label' => 'Rp 200 – 400 Juta'],
                    ['key' => '400_800',   'label' => 'Rp 400 – 800 Juta'],
                    ['key' => '800_1200',  'label' => 'Rp 800 – Rp 1.2 M'],
                ];
                $selectedHarga = (array) request('harga', []);
                @endphp

                <div class="space-y-3">
                @foreach($hargaRanges as $r)
                    @php $checked = in_array($r['key'], $selectedHarga, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="harga[]"
                        value="{{ $r['key'] }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $r['label'] }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Security (fixed 7 checkbox) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('security')">
                <span>Security</span>
                <i class="bi" :class="openAccs.security ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.security" class="{{ $listWrap }}">
                @php
                $securityOptions = [
                    'ABS',
                    'Bantuan Rem',
                    'EBD',
                    'Kontrol Stabilitas Kendaraan',
                    'Airbags',
                    'Monitor Tekanan Ban',
                    'Bantuan Hill Start',
                ];
                $selectedSecurity = (array) request('security', []);
                @endphp

                <div class="space-y-3">
                @foreach($securityOptions as $opt)
                    @php $checked = in_array($opt, $selectedSecurity, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="security[]"
                        value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $opt }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Exterior (fixed checkbox) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('exterior')">
                <span>Exterior</span>
                <i class="bi" :class="openAccs.exterior ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.exterior" class="{{ $listWrap }}">
                @php
                $exteriorOptions = [
                    'Sunroof',
                    'Moonroof',
                    'Headlamp Otomatis',
                    'Wiper Penginderaan Hujan',
                    'High Ground Clearance',
                ];
                $selectedExterior = (array) request('exterior', []);
                @endphp

                <div class="space-y-3">
                @foreach($exteriorOptions as $opt)
                    @php $checked = in_array($opt, $selectedExterior, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="exterior[]"
                        value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $opt }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Convenience (fixed checkbox) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('convenience')">
                <span>Convenience</span>
                <i class="bi" :class="openAccs.convenience ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.convenience" class="{{ $listWrap }}">
                @php
                $convenienceOptions = [
                    'Cruise Control',
                    'Paddle Shifters',
                    'Pengaturan Setir',
                    'Pembersih Udara',
                    'Kontrol Iklim Otomatis',
                    'Tekan Tombol Mulai',
                    'Ventilasi Ac belakang',
                    'Entri Tanpa Kunci',
                ];
                $selectedConvenience = (array) request('convenience', []);
                @endphp

                <div class="space-y-3">
                @foreach($convenienceOptions as $opt)
                    @php $checked = in_array($opt, $selectedConvenience, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="convenience[]"
                        value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $opt }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Performance (fixed checkbox) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('performance')">
                <span>Performance</span>
                <i class="bi" :class="openAccs.performance ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.performance" class="{{ $listWrap }}">
                @php
                $performanceOptions = [
                    'Turbo Charger',
                    'AWD',
                    '4WD',
                ];
                $selectedPerformance = (array) request('performance', []);
                @endphp

                <div class="space-y-3">
                @foreach($performanceOptions as $opt)
                    @php $checked = in_array($opt, $selectedPerformance, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="performance[]"
                        value="{{ $opt }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $opt }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            {{-- Jarak Mengemudi (range fixed) --}}
            <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jarak')">
                <span>Jarak Mengemudi</span>
                <i class="bi" :class="openAccs.jarak ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jarak" class="{{ $listWrap }}">
                @php
                $jarakRanges = [
                    ['key' => 'lt_300',   'label' => 'Dibawah 300 Km'],
                    ['key' => '300_500',  'label' => '300–500 Km'],
                    ['key' => 'gt_500',   'label' => 'Diatas 500 Km'],
                ];
                $selectedJarak = (array) request('jarak', []);
                @endphp

                <div class="space-y-3">
                @foreach($jarakRanges as $r)
                    @php $checked = in_array($r['key'], $selectedJarak, true); @endphp
                    <label class="{{ $labelCls }}">
                    <input
                        type="checkbox"
                        name="jarak[]"
                        value="{{ $r['key'] }}"
                        class="{{ $cb }}"
                        @change="$nextTick(() => $refs.filterForm.submit())"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <span>{{ $r['label'] }}</span>
                    </label>
                @endforeach
                </div>
            </div>
            </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white px-5 pb-5 pt-4">
            <div class="h-px bg-slate-100"></div>
            <div class="mt-4 grid grid-cols-2 gap-3">
            <button type="button"
                class="h-11 rounded-xl bg-slate-100 text-sm font-medium text-slate-700 hover:bg-slate-200 transition"
                @click="openFilter=false"
            >
                Close
            </button>

            <a
                href="{{ ($pageMode ?? 'rekomendasi') === 'populer' ? route('populer.mobil') : route('rekomendasi.mobil') }}"
                class="h-11 rounded-xl bg-[#51A3EE] text-center text-sm font-semibold text-white leading-[44px] hover:bg-sky-600 transition"
            >
                RESET
            </a>
            </div>
        </div>
        </form>
    </div>
    </div>

</section>
@endsection
