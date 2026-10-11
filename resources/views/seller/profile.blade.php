@extends('layouts.main')

@section('content')
@php
  $avatar = $seller->foto_profile
    ? asset('storage/'.$seller->foto_profile)
    : 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=E2E8F0&color=0F172A';
@endphp

<div class="bg-[#f6f8fb] min-h-[calc(100vh-112px)]">
  <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

      {{-- LEFT SIDEBAR --}}
      <aside class="lg:col-span-3 space-y-4">

        {{-- Seller card --}}
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">

        {{-- cover image --}}
        <div
            class="relative h-24 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/img/sampul-profile.png') }}')"
        >
            {{-- avatar --}}
            <div class="absolute left-1/2 top-14 -translate-x-1/2">
            <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-sm">
                <div class="h-[74px] w-[74px] rounded-full overflow-hidden bg-slate-100">
                <img src="{{ $avatar }}" class="h-full w-full object-cover" alt="avatar">
                </div>
            </div>
            </div>
        </div>

        {{-- content --}}
        <div class="pt-14 pb-5 px-5 text-center">
            <div class="text-xl font-semibold text-slate-900">{{ $seller->name }}</div>
            <div class="mt-1 text-sm text-slate-500">
            Anggota sejak {{ optional($seller->created_at)->translatedFormat('M Y') }}
            </div>

            <div class="mt-5 h-px bg-slate-200/70"></div>

            <div class="mt-4 flex items-center gap-3">
            <button
                class="flex-1 h-12 rounded-2xl bg-slate-100 text-slate-600 font-medium hover:bg-slate-200/70 transition flex items-center justify-center gap-2">
                <span class="text-lg">+</span>
                <span>Ikuti</span>
            </button>

            @php
                $wa = $seller->no_hp ?? null;
                $waNumber = $wa ? preg_replace('/[^0-9]/', '', $wa) : null;

                if ($waNumber && str_starts_with($waNumber, '08')) {
                    $waNumber = '62' . substr($waNumber, 1);
                }

                $waLink = $waNumber ? 'https://wa.me/' . $waNumber : null;
            @endphp

            @if($waLink)
                <a href="{{ $waLink }}" target="_blank"
                  class="h-12 w-12 inline-flex items-center justify-center rounded-full bg-[#51A3EE] text-white hover:bg-sky-600 transition">
                    <i class="bi bi-whatsapp text-lg"></i>
                </a>
            @else
                <button disabled
                    class="h-12 w-12 inline-flex items-center justify-center rounded-full bg-slate-200 text-slate-400 cursor-not-allowed">
                    <i class="bi bi-whatsapp text-lg"></i>
                </button>
            @endif


            </div>
        </div>
        </div>



        {{-- FILTER --}}
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-5">
          <div class="text-base font-semibold text-slate-900">Customer Filter</div>

          <form method="GET" action="{{ request()->url() }}" class="mt-2 space-y-2">
            <input type="hidden" name="tab" value="{{ $tab }}">

            {{-- Search --}}
            <div>
              <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input name="q" value="{{ request('q') }}"
                  class="w-full rounded-2xl bg-slate-50 border border-slate-200 pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-0 focus:border-slate-300"
                  placeholder="Cari mobil...">
              </div>
            </div>

            {{-- Rentang Harga --}}
            <div>
              <div class="text-base font-semibold text-slate-700 mt-4 mb-2">Rentang Harga</div>

              <div class="space-y-2">
                <div class="rounded-2xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                  <div class="text-sm text-slate-500">Harga Minimum</div>
                  <input type="number" name="min_price" value="{{ request('min_price') }}"
                    class="mt-1 w-full bg-transparent border-0 p-0 text-base focus:outline-none focus:ring-0"
                    placeholder="Rp 10.000.000">
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-200 px-3 py-2.5">
                  <div class="text-sm text-slate-500">Harga Maksimum</div>
                  <input type="number" name="max_price" value="{{ request('max_price') }}"
                    class="mt-1 w-full bg-transparent border-0 p-0 text-base focus:outline-none focus:ring-0"
                    placeholder="Rp 70.000.000">
                </div>
              </div>

              <button type="submit"
                class="mt-3 w-full rounded-2xl bg-[#51A3EE] text-white py-2.5 text-base font-semibold hover:bg-sky-600 transition">
                Apply
              </button>
            </div>

            {{-- Transmisi --}}
            <details class="group" open>
              <summary class="flex cursor-pointer items-center justify-between text-base font-semibold mt-4 text-slate-700">
                <span>Transmisi</span>
                <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition"></i>
              </summary>
              <div class="mt-3 space-y-2 text-base text-slate-600">
                @foreach(['Manual','Automatic','CVT','Dual Clutch'] as $t)
                  <label class="flex items-center justify-between">
                    <span>{{ $t }}</span>
                    <input type="checkbox" name="transmission[]" value="{{ $t }}"
                      @checked(in_array($t, (array)request('transmission', [])))
                      class="rounded border-slate-300 text-sky-600 focus:ring-sky-200">
                  </label>
                @endforeach
              </div>
            </details>

            {{-- Tempat Duduk --}}
            <details class="group" open>
              <summary class="flex cursor-pointer items-center justify-between text-base font-semibold mt-4 text-slate-700">
                <span>Tempat Duduk</span>
                <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition"></i>
              </summary>
              <div class="mt-3 space-y-2 text-base text-slate-600">
                @foreach([2,4,6,8] as $s)
                  <label class="flex items-center justify-between">
                    <span>{{ $s }} Kursi</span>
                    <input type="checkbox" name="seats[]" value="{{ $s }}"
                      @checked(in_array((string)$s, array_map('strval',(array)request('seats', []))))
                      class="rounded border-slate-300 text-sky-600 focus:ring-sky-200">
                  </label>
                @endforeach
              </div>
            </details>

            {{-- Jenis Bahan bakar --}}
            <details class="group" open>
              <summary class="flex cursor-pointer items-center justify-between text-base font-semibold mt-4 text-slate-700">
                <span>Jenis Bahan bakar</span>
                <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition"></i>
              </summary>
              <div class="mt-3 space-y-2 text-base text-slate-600">
                @foreach(['Bensin','Diesel','Elektrik','Hybrid','PHEV'] as $f)
                  <label class="flex items-center justify-between">
                    <span>{{ $f }}</span>
                    <input type="checkbox" name="fuel[]" value="{{ $f }}"
                      @checked(in_array($f, (array)request('fuel', [])))
                      class="rounded border-slate-300 text-sky-600 focus:ring-sky-200">
                  </label>
                @endforeach
              </div>
            </details>

          </form>
        </div>
      </aside>

        {{-- MAIN --}}
        <section class="lg:col-span-9 space-y-4">

        {{-- CARD: DETAIL PENJUAL (stats pindah ke bawah deskripsi) --}}
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-lg font-semibold text-slate-900">Detail Penjual</div>
            <p class="mt-2 text-base text-slate-600 leading-snug">
            {{ $seller->deskripsi_user ?: 'Belum ada deskripsi penjual.' }}
            </p>

            {{-- STATS DI BAWAH DESKRIPSI --}}
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Produk</div>
                <div class="text-sm text-emerald-600">+2.67%</div>
                </div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $produkCount }}</div>
                <div class="mt-1 text-sm text-slate-500">Lorem ipsum</div>
            </div>

            <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Pengikut</div>
                <div class="text-sm text-emerald-600">+2.67%</div>
                </div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $pengikut }}</div>
                <div class="mt-1 text-sm text-slate-500">Lorem ipsum</div>
            </div>

            <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">Penilaian</div>
                <div class="text-sm text-emerald-600">+2.67%</div>
                </div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($penilaian, 1) }}</div>
                <div class="mt-1 text-sm text-slate-500">Lorem ipsum</div>
            </div>
            </div>
        </div>

        {{-- WRAPPER: TAB + CONTENT (jadi 1 card seperti gambar kedua) --}}
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">

            {{-- TAB HEADER --}}
            <div class="px-6 pt-5 border-b border-slate-100">
            <div class="flex items-center gap-6 text-base">
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'iklan','page'=>null]) }}"
                class="pb-3 {{ ($tab==='iklan') ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'text-slate-500 hover:text-slate-700' }}">
                Iklan
                </a>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'ulasan','page'=>null]) }}"
                class="pb-3 {{ ($tab==='ulasan') ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'text-slate-500 hover:text-slate-700' }}">
                Ulasan Penjual
                </a>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'posting','page'=>null]) }}"
                  class="pb-3 {{ ($tab==='posting') ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'text-slate-500 hover:text-slate-700' }}">
                    Postingan
                </a>
            </div>
            </div>

            {{-- CONTENT --}}
            <div class="p-6">
            @if($tab === 'ulasan')
              <div class="py-14 text-center">
                  <div class="text-base font-semibold text-slate-900">Belum ada ulasan</div>
                  <div class="mt-2 text-base text-slate-500">
                      Ulasan dari pembeli untuk penjual ini akan tampil di sini.
                  </div>
              </div>

            @elseif($tab === 'posting')
                {{-- TAB POSTINGAN --}}
                @php
                    $avatarSeller = $seller->foto_profile
                        ? asset('storage/'.$seller->foto_profile)
                        : 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=E2E8F0&color=0F172A';
                @endphp

                @if(!isset($postings) || $postings->count() === 0)
                    <div class="py-14 text-center">
                        <div class="text-base font-semibold text-slate-900">Belum ada postingan</div>
                        <div class="mt-2 text-base text-slate-500">
                            Postingan kebutuhan dari penjual ini akan tampil di sini.
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($postings as $p)
                        <div class="rounded-2xl bg-white my-1 shadow-md ring-1 ring-slate-100 overflow-hidden">
                            <div class="p-5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $avatarSeller }}"
                                        alt="{{ $seller->name }}"
                                        class="h-10 w-10 rounded-full object-cover ring-2 ring-white bg-slate-100" />
                                    <div class="leading-tight min-w-0">
                                        <div class="text-base font-semibold text-slate-900 truncate">{{ $p->nama }}</div>
                                        <div class="text-sm text-slate-500 truncate">{{ $p->kota ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="my-4 h-px bg-slate-100"></div>

                                <div class="text-base text-slate-500 truncate">{{ $p->judul }}</div>
                                <div class="mt-1 text-lg font-semibold text-slate-900">{{ $p->range_harga ?: '—' }}</div>

                                <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                                    @php
                                        $tags = array_values(array_filter([
                                            $p->tipe_bodi,
                                            $p->merek,
                                            $p->transmisi,
                                        ]));
                                    @endphp

                                    <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                        @foreach($tags as $t)
                                            <span class="inline-flex items-center rounded-lg border bg-slate-50 border-slate-100 px-3 py-1 text-xs text-slate-600 whitespace-nowrap">
                                                {{ $t }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                @php
                                    $chatUrl = route('chat.show', $seller);
                                @endphp

                                @auth
                                    @if(auth()->id() !== $seller->id)
                                        <a href="{{ $chatUrl }}"
                                          class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-sm font-medium text-white hover:bg-sky-600 transition">
                                            Tawarkan Mobil Saya
                                        </a>
                                    @else
                                        <button type="button" disabled
                                          class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-slate-300 px-4 py-3 text-sm font-medium text-white cursor-not-allowed">
                                            Tawarkan Mobil Saya
                                        </button>
                                    @endif
                                @endauth

                                @guest
                                    <button type="button"
                                      class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-sm font-medium text-white hover:bg-sky-600 transition"
                                      @click="
                                        fetch('{{ route('auth.intended') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ url: '{{ $chatUrl }}' })
                                        }).finally(() => $store.authModal.open('login'))
                                      ">
                                        Tawarkan Mobil Saya
                                    </button>
                                @endguest
                            </div>
                        </div>
                    @endforeach

                    </div>

                    <div class="mt-6">
                        {{ $postings->links() }}
                    </div>
                @endif

            @else
                {{-- TAB IKLAN --}}
                @if($cars->count() === 0)
                    <div class="py-14 text-center">
                        <div class="text-base font-semibold text-slate-900">Belum ada iklan</div>
                        <div class="mt-2 text-base text-slate-500">
                            Iklan mobil dari penjual ini akan tampil di sini.
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                      @foreach($cars as $item)
                        @if($item->type === 'mobil')
                          @include('components.card-mobil-iklan', ['mobil' => $item->data])
                        @else
                          @include('components.card-motor-iklan', ['motor' => $item->data])
                        @endif
                      @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $cars->links() }}
                    </div>
                @endif
            @endif

            </div>
        </div>

        </section>

    </div>
  </div>
</div>
@endsection
