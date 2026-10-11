@extends('layouts.main')

@section('content')
@php
  $fmtRupiah = function($n){
    $n = (int) $n;
    return 'Rp ' . number_format($n, 0, ',', '.');
  };
        
@endphp

<div class="mx-auto max-w-[1200px] px-4 sm:px-6 pt-6 pb-10">

  {{-- SEARCH BAR: pakai yang kamu sudah punya (paste search bar kamu di sini) --}}
  {{-- Pastikan action ke route('sewa.index') method GET --}}
  @includeIf('pages.partials.sewa-searchbar') {{-- opsional kalau kamu mau pisah partial --}}

  <div class="mt-6 grid gap-6 lg:grid-cols-12">
    {{-- SIDEBAR FILTER --}}
    <aside class="lg:col-span-3">
      <form method="GET" action="{{ route('sewa.index') }}" class="space-y-4">
        {{-- keep searchbar params --}}
        <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
        <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
        <input type="hidden" name="transmisi" value="{{ request('transmisi') }}">
        <input type="hidden" name="jenis_mobil" value="{{ request('jenis_mobil') }}">

        <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-4 !mt-0">
          <div class="text-sm font-semibold text-slate-900">Rentang Harga</div>

          <div class="mt-3 space-y-3">
            <div>
              <div class="text-xs text-slate-500">Harga Minimum</div>
              <input name="harga_min" value="{{ request('harga_min') }}"
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-sky-200"
                     placeholder="Rp 0">
            </div>
            <div>
              <div class="text-xs text-slate-500">Harga Maksimum</div>
              <input name="harga_max" value="{{ request('harga_max') }}"
                     class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-sky-200"
                     placeholder="Rp 999.999.999">
            </div>

            <button class="mt-2 w-full rounded-xl bg-[#51A3EE] py-2.5 text-sm font-semibold text-white hover:bg-sky-600">
              Apply
            </button>
          </div>
        </div>

        {{-- TRANSMISI --}}
        <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-4">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-900">Transmisi</div>
            <i class="bi bi-chevron-down text-slate-400"></i>
          </div>

          @php
            $trOpt = request('transmisi_opt', []);
            $trOpt = is_array($trOpt) ? $trOpt : [];
          @endphp

          <div class="mt-3 space-y-2 text-sm text-slate-700">
            @foreach(['Manual','Automatic','CVT','Dual Clutch'] as $opt)
              <label class="flex items-center justify-between gap-3">
                <span>{{ $opt }}</span>
                <input type="checkbox" name="transmisi_opt[]" value="{{ $opt }}"
                       {{ in_array($opt, $trOpt) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-200">
              </label>
            @endforeach
          </div>
        </div>

        {{-- TEMPAT DUDUK --}}
        <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-4">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-900">Tempat Duduk</div>
            <i class="bi bi-chevron-down text-slate-400"></i>
          </div>

          @php
            $kursi = request('kursi', []);
            $kursi = is_array($kursi) ? $kursi : [];
          @endphp

          <div class="mt-3 space-y-2 text-sm text-slate-700">
            @foreach([2,4,6,8,10,15] as $k)
              <label class="flex items-center justify-between gap-3">
                <span>{{ $k }} Kursi</span>
                <input type="checkbox" name="kursi[]" value="{{ $k }}"
                       {{ in_array((string)$k, array_map('strval',$kursi)) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-200">
              </label>
            @endforeach
          </div>
        </div>

        {{-- BAHAN BAKAR --}}
        <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-4">
          <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-slate-900">Jenis Bahan bakar</div>
            <i class="bi bi-chevron-down text-slate-400"></i>
          </div>

          @php
            $bb = request('bahan_bakar', []);
            $bb = is_array($bb) ? $bb : [];
          @endphp

          <div class="mt-3 space-y-2 text-sm text-slate-700">
            @foreach(['Bensin','Diesel','Elektrik','Hybrid','PHEV'] as $opt)
              <label class="flex items-center justify-between gap-3">
                <span>{{ $opt }}</span>
                <input type="checkbox" name="bahan_bakar[]" value="{{ $opt }}"
                       {{ in_array($opt, $bb) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-200">
              </label>
            @endforeach
          </div>
        </div>
      </form>
    </aside>

    {{-- LIST --}}
    <section class="lg:col-span-9 space-y-5">
      @forelse($mobilsewa as $m)
        @php
          $img = $m->gambar_mobil ? asset('storage/'.$m->gambar_mobil) : 'https://placehold.co/640x360?text=Mobil';
          $title = trim(($m->merk_mobil ?? '').' '.($m->model_mobil ?? ''));
          $sub = ($m->jenis_mobil ?? '—') . ' / ' . ($m->tahun ?? '—');

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

        <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-4 sm:p-5">
          <div class="grid gap-4 sm:grid-cols-12 sm:items-center">
            {{-- image box left --}}
            <div class="sm:col-span-4">
              <div class="rounded-2xl bg-slate-50 p-3">
                <img src="{{ $img }}" class="h-36 w-full object-contain" alt="car">
              </div>
            </div>

            {{-- content --}}
            <div class="sm:col-span-8">
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-4 min-w-0">
                <img src="{{ $brandLogo($m->merk_mobil) }}"
                    class="h-12 w-12 object-contain"
                    alt="{{ $m->merk_mobil }}">
                  <div class="min-w-0">
                    <div class="truncate text-base font-semibold text-slate-900">{{ $title }}</div>
                    <div class="text-sm text-slate-500">{{ $sub }}</div>
                  </div>
                </div>

                <div class="flex items-center gap-3 text-sm text-slate-500">
                  <i class="bi bi-star-fill text-yellow-400"></i>
                  <span class="font-semibold text-slate-700">4.9</span>
                  <span>(10 Ulasan)</span>
                </div>
              </div>

              {{-- specs row --}}
              <div class="mt-5 grid gap-y-3 gap-x-6 sm:grid-cols-3 text-sm text-slate-600">
                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-car-front"></i></span>
                  <div><div class="text-sm text-slate-400">Tipe Body</div><div class="font-medium text-slate-700 text-sm">{{ $m->jenis_mobil ?? '-' }}</div></div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-person"></i></span>
                  <div><div class="text-sm text-slate-400">Jumlah Kursi</div><div class="font-medium text-slate-700 text-sm">{{ $m->tempat_duduk_mobil ?? '-' }}</div></div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-gear"></i></span>
                  <div><div class="text-sm text-slate-400">Transmisi</div><div class="font-medium text-slate-700 text-sm">{{ $m->transmisi_mobil ?? '-' }}</div></div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-fuel-pump"></i></span>
                  <div><div class="text-sm text-slate-400">Bahan Bakar</div><div class="font-medium text-slate-700 text-sm">{{ $m->jenis_bahan_bakar_mobil ?? '-' }}</div></div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-speedometer2"></i></span>
                  <div><div class="text-sm text-slate-400">Mesin</div><div class="font-medium text-slate-700 text-sm">{{ $m->kapasitas_mesin_mobil ? $m->kapasitas_mesin_mobil.' cc' : '-' }}</div></div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 text-xl items-center justify-center rounded-lg bg-slate-50 text-[#51A3EE]"><i class="bi bi-palette"></i></span>
                  <div><div class="text-sm text-slate-400">Warna</div><div class="font-medium text-slate-700 text-sm">{{ $m->warna_mobil ?? '-' }}</div></div>
                </div>
              </div>

              {{-- bottom --}}
              <div class="mt-4 flex items-center justify-between gap-3">
                <div>
                  <div class="text-sm text-slate-400">Harga</div>
                  <div class="text-base font-semibold text-slate-900">
                    {{ $fmtRupiah($m->harga_mobil) }} <span class="text-base font-normal text-slate-500">/ Hari</span>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700">
                    <i class="bi bi-telephone"></i>
                  </button>

                  {{-- arahkan ke detail sewa (sementara pakai show mobil sewa kalau belum ada) --}}
                    <a href="{{ route('sewa.mobil.show', $m->id) }}"
                    class="inline-flex items-center justify-center rounded-full bg-[#51A3EE] px-6 py-2 text-white font-semibold hover:bg-sky-600 transition">
                    Detail
                    </a>

                </div>
              </div>

            </div>
          </div>
        </div>
      @empty
        <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 text-sm text-slate-500">
          Tidak ada mobil sewa yang cocok dengan filter kamu.
        </div>
      @endforelse

      {{-- pagination --}}
      @if($mobilsewa->hasPages())
    <div class="pt-4">
        {{ $mobilsewa->links() }}
    </div>
    @endif
    </section>
  </div>
</div>
@endsection
