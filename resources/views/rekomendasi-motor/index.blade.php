@extends('layouts.main')

@section('content')
<section
  class="relative mx-auto max-w-[1200px] px-4 pb-16 pt-6 sm:pt-8"
  x-data="{
    openFilter: false,

    // Multi accordion state (bisa banyak terbuka bersamaan)
    openAccs: {
      jenis_kendaraan: false,
      transmisi: false,
      bahan_bakar: false,
      merk: false,
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
        transmisi: 'transmisi[]',
        bahan_bakar: 'bahan_bakar[]',
        merk: 'merk[]',
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
      this.openAccs[key] = willOpen;

      // saat membuka section baru (selain jenis_kendaraan):
      // tutup section lain yang BELUM punya pilihan
      if (willOpen && key !== 'jenis_kendaraan') {
        Object.keys(this.openAccs).forEach(k => {
          if (k === 'jenis_kendaraan') return;
          if (k === key) return;
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
          if (k.startsWith(base + '[')) return true;
        }
        return false;
      }

      const v = params.get(name);
      return v !== null && v !== '';
    },

    syncOpenAccsFromQuery() {
      Object.keys(this.openAccs).forEach(k => this.openAccs[k] = false);

      if (this.hasAny('jenis_kendaraan')) this.openAccs.jenis_kendaraan = true;
      if (this.hasAny('transmisi[]')) this.openAccs.transmisi = true;
      if (this.hasAny('bahan_bakar[]')) this.openAccs.bahan_bakar = true;
      if (this.hasAny('merk[]')) this.openAccs.merk = true;
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
      this.syncOpenAccsFromQuery();

      this.$watch('openFilter', (v) => {
        if (v) {
          this.syncOpenAccsFromQuery();
          this.openAccs.jenis_kendaraan = true;
        }
      });
    }
  }"
  @keydown.escape.window="openFilter=false"
>

  {{-- Header --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">
      Motor Populer dan Terbaru
    </h1>

    {{-- Search bar --}}
    <form method="GET" x-ref="filterForm"
      action="{{ route('motor.index') }}"
      class="flex w-full items-center gap-3 sm:w-[520px]">

      <div class="relative w-full">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          placeholder="Cari Motor Sekarang"
          class="w-full rounded-full bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 ring-1 ring-slate-200 outline-none focus:ring-sky-500"
        />
      </div>

      {{-- Tombol filter --}}
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

    {{-- LIST MOTOR (NO CAROUSEL) --}}
    @if($motors->count() === 0)
    <div class="mt-6 rounded-2xl bg-slate-50 p-6 text-slate-600 ring-1 ring-slate-100">
        Tidak ada motor yang sesuai filter.
    </div>
    @else
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($motors as $motor)
        {{-- pakai card homepage --}}
        <x-motor-card
            :motor="$motor"
            :likedMotorIds="$likedMotorIds"
            :showPopulerBadge="false"
        />
        @endforeach
    </div>

  {{-- PAGINATION --}}
  <div class="mt-8">
    {{ $motors->links() }}
  </div>
@endif


  {{-- ================================================== --}}
  {{-- FILTER PANEL (DESKTOP) - sama seperti mobil, versi motor --}}
  {{-- ================================================== --}}
  <div x-show="openFilter" x-cloak class="hidden lg:block">
    <div
      class="absolute right-0 top-[76px] z-[9999] w-[360px] rounded-3xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.12)] ring-1 ring-slate-200 overflow-hidden"
      @click.stop
    >
      {{-- ✅ FORM dibuat flex column + fixed height --}}
      <form x-ref="filterFormDesktop" method="GET" action="{{ route('motor.index') }}"
            class="flex flex-col h-[520px]">

        <input type="hidden" name="q" value="{{ request('q') }}"/>

        @php
          $cb = "h-5 w-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500";
          $rowBtn = "flex items-center justify-between py-3 text-sm text-slate-800 w-full";
          $listWrap = "pb-3 space-y-3";
          $labelCls = "flex items-center gap-3 text-sm text-slate-700 cursor-pointer select-none";
        @endphp

        {{-- ✅ CONTENT: yang scroll hanya ini --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 pb-4">

          {{-- JENIS KENDARAAN (GRID 3x3) --}}
          @php
            $jenisOrder = ['Sport', 'Off Road', 'Cafe Racer', 'Cruiser', 'Street', 'Moped', 'Scooter'];

            $jenisImages = [
              'Sport'      => 'sport.png',
              'Off Road'   => 'offroad.png',
              'Cafe Racer' => 'cafe-racer.png',
              'Cruiser'    => 'cruiser.png',
              'Street'     => 'street.png',
              'Moped'      => 'moped.png',
              'Scooter'    => 'scooter.png',
            ];

            $selectedJenis = request('jenis_kendaraan');
          @endphp

          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jenis_kendaraan')">
              <span>Jenis Kendaraan</span>
              <i class="bi" :class="openAccs.jenis_kendaraan ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jenis_kendaraan" class="pb-4">
              <div class="grid grid-cols-3 gap-3">
                @foreach($jenisOrder as $label)
                  @php
                    $isActive = ($selectedJenis === $label);
                    $img = $jenisImages[$label] ?? null;
                    $imgUrl = $img ? asset('assets/img/motor-populer/' . $img) : null;
                  @endphp

                  <label class="cursor-pointer select-none">
                    <input
                      type="radio"
                      name="jenis_kendaraan"
                      value="{{ $label }}"
                      class="hidden"
                      {{ $isActive ? 'checked' : '' }}
                      @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    />

                    <div class="rounded-2xl bg-slate-50 p-3 ring-1 transition
                                {{ $isActive ? 'ring-sky-500 bg-sky-50/50' : 'ring-slate-200 hover:ring-slate-300' }}">
                      <div class="h-[70px] w-full rounded-xl flex items-center justify-center overflow-hidden">
                        @if($imgUrl)
                          <img
                            src="{{ $imgUrl }}"
                            alt="{{ $label }}"
                            class="h-full w-full object-contain p-2"
                            onerror="this.style.display='none'"
                          />
                        @endif
                      </div>

                      <div class="mt-0 text-center text-[12px] font-medium text-slate-800 leading-tight">
                        {{ $label }}
                      </div>
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
                  <input type="checkbox" name="transmisi[]" value="{{ $opt }}" class="{{ $cb }}"
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
                  <input type="checkbox" name="bahan_bakar[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Merk --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('merk')">
              <span>Pilih Merek Favorit Anda</span>
              <i class="bi" :class="openAccs.merk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.merk" class="{{ $listWrap }}">
              @foreach(($filters['merk'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('merk', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="merk[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Kapasitas --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('kapasitas')">
              <span>Kapasitas Mesin</span>
              <i class="bi" :class="openAccs.kapasitas ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.kapasitas" class="{{ $listWrap }}">
              @php
                $kapRanges = [
                  ['key' => 'lt_100',   'label' => 'Dibawah 100 CC'],
                  ['key' => '100_150',  'label' => '100 CC - 150 CC'],
                  ['key' => '150_250',  'label' => '150 CC - 250 CC'],
                  ['key' => 'gt_250',   'label' => 'Di atas 250 CC'],
                ];
                $selectedKap = (array) request('kapasitas', []);
              @endphp

              <div class="space-y-3">
                @foreach($kapRanges as $r)
                  @php $checked = in_array($r['key'], $selectedKap, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="kapasitas[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Harga --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('harga')">
              <span>Harga</span>
              <i class="bi" :class="openAccs.harga ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.harga" class="{{ $listWrap }}">
              @php
                $hargaRanges = [
                  ['key' => 'lt_10',  'label' => 'Dibawah 10 Juta'],
                  ['key' => '10_20',  'label' => '10 - 20 Juta'],
                  ['key' => '20_40',  'label' => '20 - 40 Juta'],
                  ['key' => '40_80',  'label' => '40 - 80 Juta'],
                  ['key' => 'gt_80',  'label' => 'Di atas 80 Juta'],
                ];
                $selectedHarga = (array) request('harga', []);
              @endphp

              <div class="space-y-3">
                @foreach($hargaRanges as $r)
                  @php $checked = in_array($r['key'], $selectedHarga, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="harga[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Jarak --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jarak')">
              <span>Jarak Tempuh</span>
              <i class="bi" :class="openAccs.jarak ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jarak" class="{{ $listWrap }}">
              @php
                $jarakRanges = [
                  ['key' => 'lt_5k',     'label' => 'Dibawah 5.000 KM'],
                  ['key' => '5k_15k',    'label' => '5.000 - 15.000 KM'],
                  ['key' => '15k_30k',   'label' => '15.001 - 30.000 KM'],
                  ['key' => 'gt_30k',    'label' => 'Di atas 30.000 KM'],
                ];
                $selectedJarak = (array) request('jarak', []);
              @endphp

              <div class="space-y-3">
                @foreach($jarakRanges as $r)
                  @php $checked = in_array($r['key'], $selectedJarak, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="jarak[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Security --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('security')">
              <span>Security</span>
              <i class="bi" :class="openAccs.security ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.security" class="{{ $listWrap }}">
              @foreach(($filters['security'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('security', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="security[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Exterior --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('exterior')">
              <span>Exterior</span>
              <i class="bi" :class="openAccs.exterior ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.exterior" class="{{ $listWrap }}">
              @foreach(($filters['exterior'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('exterior', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="exterior[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Convenience --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('convenience')">
              <span>Convenience</span>
              <i class="bi" :class="openAccs.convenience ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.convenience" class="{{ $listWrap }}">
              @foreach(($filters['convenience'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('convenience', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="convenience[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Performance --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('performance')">
              <span>Performance</span>
              <i class="bi" :class="openAccs.performance ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.performance" class="{{ $listWrap }}">
              @foreach(($filters['performance'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('performance', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="performance[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDesktop || $refs.filterForm).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

        </div> {{-- END CONTENT --}}

        {{-- ✅ FOOTER: sticky bottom (tidak ikut scroll) --}}
        <div class="shrink-0 bg-white px-5 pb-5 pt-4 border-t border-slate-100 sticky bottom-0">
          <div class="grid grid-cols-2 gap-3">
            <button type="button"
              class="h-11 rounded-xl bg-slate-100 text-sm font-medium text-slate-700 hover:bg-slate-200 transition"
              @click="openFilter=false"
            >
              Close
            </button>

            <a
              href="{{ route('motor.index') }}"
              class="h-11 rounded-xl bg-[#51A3EE] text-center text-sm font-semibold text-white leading-[44px] hover:bg-sky-600 transition"
            >
              RESET
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>


  {{-- ================================================== --}}
  {{-- FILTER DRAWER (MOBILE + TABLET) + STICKY FOOTER --}}
  {{-- ================================================== --}}
  <div x-show="openFilter" x-cloak class="lg:hidden fixed inset-0 z-[9999]">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30" @click="openFilter=false"></div>

    {{-- Drawer --}}
    <div
      class="absolute right-0 top-0 h-full w-[92%] max-w-[420px] bg-white shadow-2xl rounded-l-3xl overflow-hidden"
      @click.stop
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="translate-x-full"
      x-transition:enter-end="translate-x-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="translate-x-0"
      x-transition:leave-end="translate-x-full"
    >
      <form x-ref="filterFormDrawer" method="GET" action="{{ route('motor.index') }}"
            class="flex flex-col h-full">
        <input type="hidden" name="q" value="{{ request('q') }}"/>

        @php
          $cb = "h-5 w-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500";
          $rowBtn = "flex items-center justify-between py-3 text-sm text-slate-800 w-full";
          $listWrap = "pb-3 space-y-3";
          $labelCls = "flex items-center gap-3 text-sm text-slate-700 cursor-pointer select-none";
        @endphp



        {{-- CONTENT (scrollable) --}}
        <div class="flex-1 overflow-y-auto px-5 py-4">

          {{-- JENIS KENDARAAN (GRID 3x3) --}}
          @php
            $jenisOrder = ['Sport', 'Off Road', 'Cafe Racer', 'Cruiser', 'Street', 'Moped', 'Scooter'];

            $jenisImages = [
              'Sport'      => 'sport.png',
              'Off Road'   => 'offroad.png',
              'Cafe Racer' => 'cafe-racer.png',
              'Cruiser'    => 'cruiser.png',
              'Street'     => 'street.png',
              'Moped'      => 'moped.png',
              'Scooter'    => 'scooter.png',
            ];

            $selectedJenis = request('jenis_kendaraan');
          @endphp

          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jenis_kendaraan')">
              <span>Jenis Kendaraan</span>
              <i class="bi" :class="openAccs.jenis_kendaraan ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jenis_kendaraan" class="pb-4">
              <div class="grid grid-cols-3 gap-3">
                @foreach($jenisOrder as $label)
                  @php
                    $isActive = ($selectedJenis === $label);
                    $img = $jenisImages[$label] ?? null;
                    $imgUrl = $img ? asset('assets/img/motor-populer/' . $img) : null;
                  @endphp

                  <label class="cursor-pointer select-none">
                    <input
                      type="radio"
                      name="jenis_kendaraan"
                      value="{{ $label }}"
                      class="hidden"
                      {{ $isActive ? 'checked' : '' }}
                      @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    />

                    <div class="rounded-2xl bg-slate-50 p-3 ring-1 transition
                                {{ $isActive ? 'ring-sky-500 bg-sky-50/50' : 'ring-slate-200 hover:ring-slate-300' }}">
                      <div class="h-[70px] w-full rounded-xl flex items-center justify-center overflow-hidden">
                        @if($imgUrl)
                          <img src="{{ $imgUrl }}" alt="{{ $label }}" class="h-full w-full object-contain p-2"
                              onerror="this.style.display='none'" />
                        @endif
                      </div>
                      <div class="mt-2 text-center text-[12px] font-medium text-slate-800 leading-tight">
                        {{ $label }}
                      </div>
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
                  <input type="checkbox" name="transmisi[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Bahan Bakar --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('bahan_bakar')">
              <span>Jenis Bahan Bakar</span>
              <i class="bi" :class="openAccs.bahan_bakar ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.bahan_bakar" class="{{ $listWrap }}">
              @foreach(($filters['bahan_bakar'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('bahan_bakar', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="bahan_bakar[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Merk --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('merk')">
              <span>Pilih Merek Favorit Anda</span>
              <i class="bi" :class="openAccs.merk ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.merk" class="{{ $listWrap }}">
              @foreach(($filters['merk'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('merk', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="merk[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Kapasitas --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('kapasitas')">
              <span>Kapasitas Mesin</span>
              <i class="bi" :class="openAccs.kapasitas ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.kapasitas" class="{{ $listWrap }}">
              @php
                $kapRanges = [
                  ['key' => 'lt_100',   'label' => 'Dibawah 100 CC'],
                  ['key' => '100_150',  'label' => '100 CC - 150 CC'],
                  ['key' => '150_250',  'label' => '150 CC - 250 CC'],
                  ['key' => 'gt_250',   'label' => 'Di atas 250 CC'],
                ];
                $selectedKap = (array) request('kapasitas', []);
              @endphp

              <div class="space-y-3">
                @foreach($kapRanges as $r)
                  @php $checked = in_array($r['key'], $selectedKap, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="kapasitas[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Harga --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('harga')">
              <span>Harga</span>
              <i class="bi" :class="openAccs.harga ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.harga" class="{{ $listWrap }}">
              @php
                $hargaRanges = [
                  ['key' => 'lt_10',  'label' => 'Dibawah 10 Juta'],
                  ['key' => '10_20',  'label' => '10 - 20 Juta'],
                  ['key' => '20_40',  'label' => '20 - 40 Juta'],
                  ['key' => '40_80',  'label' => '40 - 80 Juta'],
                  ['key' => 'gt_80',  'label' => 'Di atas 80 Juta'],
                ];
                $selectedHarga = (array) request('harga', []);
              @endphp

              <div class="space-y-3">
                @foreach($hargaRanges as $r)
                  @php $checked = in_array($r['key'], $selectedHarga, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="harga[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Jarak --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('jarak')">
              <span>Jarak Tempuh</span>
              <i class="bi" :class="openAccs.jarak ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.jarak" class="{{ $listWrap }}">
              @php
                $jarakRanges = [
                  ['key' => 'lt_5k',     'label' => 'Dibawah 5.000 KM'],
                  ['key' => '5k_15k',    'label' => '5.000 - 15.000 KM'],
                  ['key' => '15k_30k',   'label' => '15.001 - 30.000 KM'],
                  ['key' => 'gt_30k',    'label' => 'Di atas 30.000 KM'],
                ];
                $selectedJarak = (array) request('jarak', []);
              @endphp

              <div class="space-y-3">
                @foreach($jarakRanges as $r)
                  @php $checked = in_array($r['key'], $selectedJarak, true); @endphp
                  <label class="{{ $labelCls }}">
                    <input type="checkbox" name="jarak[]" value="{{ $r['key'] }}" class="{{ $cb }}"
                      @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                      {{ $checked ? 'checked' : '' }}>
                    <span>{{ $r['label'] }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Security --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('security')">
              <span>Security</span>
              <i class="bi" :class="openAccs.security ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.security" class="{{ $listWrap }}">
              @foreach(($filters['security'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('security', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="security[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Exterior --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('exterior')">
              <span>Exterior</span>
              <i class="bi" :class="openAccs.exterior ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.exterior" class="{{ $listWrap }}">
              @foreach(($filters['exterior'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('exterior', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="exterior[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Convenience --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('convenience')">
              <span>Convenience</span>
              <i class="bi" :class="openAccs.convenience ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.convenience" class="{{ $listWrap }}">
              @foreach(($filters['convenience'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('convenience', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="convenience[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Performance --}}
          <div class="border-b border-slate-100">
            <button type="button" class="{{ $rowBtn }}" @click="toggleAcc('performance')">
              <span>Performance</span>
              <i class="bi" :class="openAccs.performance ? 'bi-chevron-up text-slate-500' : 'bi-chevron-down text-slate-500'"></i>
            </button>

            <div x-show="openAccs.performance" class="{{ $listWrap }}">
              @foreach(($filters['performance'] ?? collect()) as $opt)
                @php $checked = in_array($opt, (array)request('performance', []), true); @endphp
                <label class="{{ $labelCls }}">
                  <input type="checkbox" name="performance[]" value="{{ $opt }}" class="{{ $cb }}"
                    @change="$nextTick(() => ($refs.filterFormDrawer).submit())"
                    {{ $checked ? 'checked' : '' }}>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
          </div>

        </div>

        {{-- FOOTER sticky --}}
        <div class="shrink-0 bg-white px-5 pb-5 pt-4 border-t border-slate-100 sticky bottom-0">
          <div class="grid grid-cols-2 gap-3">
            <button type="button"
              class="h-11 rounded-xl bg-slate-100 text-sm font-medium text-slate-700 hover:bg-slate-200 transition"
              @click="openFilter=false">
              Close
            </button>

            <a href="{{ route('motor.index') }}"
              class="h-11 rounded-xl bg-[#51A3EE] text-center text-sm font-semibold text-white leading-[44px] hover:bg-sky-600 transition">
              RESET
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>


</section>
@endsection
