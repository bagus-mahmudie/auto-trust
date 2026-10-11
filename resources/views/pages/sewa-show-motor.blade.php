@extends('layouts.main')

@section('content')
@php
  $fmtRupiah = fn($n) => 'Rp ' . number_format((int)($n ?? 0), 0, ',', '.');

  $brand = $motor->merk_motor ?? '';
  $model = $motor->model_motor ?? '';
  $tahun = $motor->tahun_motor ?? null;

  $judul = trim(($brand ? $brand.' ' : '').($model ? $model.' ' : '').($tahun ?: ''));
  $subjudul = trim(($motor->jenis_motor ?? 'Motor') . ' / ' . ($tahun ?? '2022'));

  $sellerName  = $seller->name ?? 'Customer Service';
  $sellerCity  = $seller->lokasi ?? ($motor->lokasi_motor ?? 'Jakarta Selatan');
  $sellerPhone = $seller->no_hp ?? '';

  $sellerAvatar = !empty($seller->foto_profile)
      ? asset('storage/'.ltrim($seller->foto_profile,'/'))
      : 'https://ui-avatars.com/api/?name='.urlencode($sellerName).'&background=E2E8F0&color=0F172A&rounded=true&bold=true&size=128';

  $waLink = '';
  if (!empty($sellerPhone)) {
    $phone = preg_replace('/[^0-9]/', '', $sellerPhone);
    if (str_starts_with($phone, '0')) $phone = '62'.substr($phone, 1);
    $waLink = 'https://wa.me/'.$phone;
  }

  $alamatMap = $motor->lokasi_motor ?? $sellerCity ?? 'Jakarta, Indonesia';
  $mapEmbed = 'https://www.google.com/maps?q='.urlencode($alamatMap).'&output=embed';

  $imgs = collect([]);
  if (!empty($motor->gambar_motor)) $imgs->push($motor->gambar_motor);

  $gallery = $motor->galery_motor ?? [];
  if (is_string($gallery)) {
    $decoded = json_decode($gallery, true);
    if (json_last_error() === JSON_ERROR_NONE) $gallery = $decoded;
  }
  if (is_array($gallery)) foreach ($gallery as $g) if (!empty($g)) $imgs->push($g);

  $imgs = $imgs->map(function($p){
      if (!$p) return null;
      if (str_starts_with($p, 'http')) return $p;
      return asset('storage/'.ltrim($p,'/'));
  })->filter()->values();

  if ($imgs->isEmpty()) $imgs = collect(['https://placehold.co/1200x800?text=No+Image']);

  // Reviews dummy (UI)
  $reviewsData = [
    ['name'=>'Seleng','rating'=>5,'ago'=>'2 hari lalu','cat'=>'Semua','text'=>'Lorem ipsum dolor sit amet consectetur. Suspendisse nulla aliquet pulvinar nunc eleifend urna consequat in.'],
    ['name'=>'Bagus','rating'=>4,'ago'=>'2 hari lalu','cat'=>'Fitur','text'=>'Et nulla id libero neque viverra malesuada blandit quisque. Nec tristique et feugiat cursus tortor varius.'],
    ['name'=>'Putri','rating'=>5,'ago'=>'2 hari lalu','cat'=>'Specs','text'=>'Lorem ipsum dolor sit amet consectetur. Suspendisse nulla aliquet pulvinar nunc eleifend urna consequat in.'],
  ];
  $counts = [
    'Semua'   => count($reviewsData),
    'Fitur'   => collect($reviewsData)->where('cat','Fitur')->count(),
    'Specs'   => collect($reviewsData)->where('cat','Specs')->count(),
    'Performa'=> collect($reviewsData)->where('cat','Performa')->count(),
  ];

  $brandLogo = function($brand) {
    $file = strtolower(str_replace(' ', '', $brand)) . '.png';
    $path = public_path('assets/img/merk/' . $file);
    if (file_exists($path)) return asset('assets/img/merk/' . $file);
    return asset('assets/img/merk/default.png');
  };
@endphp

<div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">

  {{-- TOP BAR: Kembali + Bagikan (persis feel screenshot) --}}
  <div class="flex items-center justify-between mb-4"
       x-data="{
          shareOpen:false,
          copied:false,
          url: @js(url()->current()),
          title: @js($judul ?: 'Detail Sewa'),
          copyLink(){
            navigator.clipboard.writeText(this.url);
            this.copied = true;
            setTimeout(()=> this.copied=false, 1200);
          }
       }"
       @keydown.escape.window="shareOpen=false">

    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2 text-base text-slate-600 hover:text-slate-900">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <button type="button"
            class="h-10 px-4 rounded-xl bg-white ring-1 ring-slate-200 text-slate-600 hover:bg-slate-50 inline-flex items-center gap-2"
            @click="shareOpen=true">
      <i class="bi bi-share"></i> Bagikan
    </button>

    {{-- SHARE MODAL (pakai versi mobil/show supaya konsisten project) --}}
    <div x-show="shareOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center" aria-modal="true" role="dialog">
      <div class="absolute inset-0 bg-black/30" @click="shareOpen=false"></div>

      <div x-transition class="relative w-[92vw] max-w-[420px] rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div class="text-base sm:text-lg font-medium text-slate-900">Share</div>
          <button type="button" class="h-8 w-8 rounded-full hover:bg-slate-100 inline-flex items-center justify-center text-slate-500" @click="shareOpen=false">
            <i class="bi bi-x text-2xl leading-none"></i>
          </button>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-5 text-center">
          <a :href="`https://wa.me/?text=${encodeURIComponent(title+' - '+url)}`" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/whatsapp.png') }}" class="h-7 w-7 object-contain" alt="WhatsApp">
            </div>
            <div class="mt-2 text-sm text-slate-600">WhatsApp</div>
          </a>

          <a href="https://www.instagram.com/" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/instagram.png') }}" class="h-7 w-7 object-contain" alt="Instagram">
            </div>
            <div class="mt-2 text-sm text-slate-600">Instagram</div>
          </a>

          <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/facebook.png') }}" class="h-7 w-7 object-contain" alt="Facebook">
            </div>
            <div class="mt-2 text-sm text-slate-600">Facebook</div>
          </a>

          <a href="https://www.tiktok.com/" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/tiktok.png') }}" class="h-7 w-7 object-contain" alt="Tiktok">
            </div>
            <div class="mt-2 text-sm text-slate-600">Tiktok</div>
          </a>

          <a :href="`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/telegram.png') }}" class="h-7 w-7 object-contain" alt="Telegram">
            </div>
            <div class="mt-2 text-sm text-slate-600">Telegram</div>
          </a>

          <a href="https://discord.com/" target="_blank" class="group">
            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
              <img src="{{ asset('assets/img/icons/discord.png') }}" class="h-7 w-7 object-contain" alt="Discord">
            </div>
            <div class="mt-2 text-sm text-slate-600">Discord</div>
          </a>
        </div>

        <div class="mt-6 text-sm text-slate-900">Page Link</div>
        <div class="mt-2 flex items-center gap-2">
          <input type="text"
                 class="w-full rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500"
                 :value="url" readonly>
          <button type="button"
                  class="h-10 w-10 rounded-xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 inline-flex items-center justify-center"
                  @click="copyLink()" title="Copy">
            <i class="bi bi-clipboard"></i>
          </button>
        </div>
        <div class="mt-2 text-xs text-slate-500" x-show="copied" x-transition>Link disalin ✓</div>
      </div>
    </div>
  </div>

  <div class="grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-8">
      <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-4 sm:p-6"
           x-data="{ active: 0, imgs: @js($imgs->values()) }">
        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-100 overflow-hidden">
          <img :src="imgs[active]" class="w-full max-h-[420px] object-contain" alt="motor">
        </div>

        <div class="mt-4 grid grid-cols-3 gap-3">
          <template x-for="(src, idx) in imgs.slice(0, 3)" :key="idx">
            <button type="button"
                    class="rounded-xl bg-white ring-2 overflow-hidden"
                    :class="active===idx ? 'ring-sky-300' : 'ring-slate-100'"
                    @click="active=idx">
              <img :src="src" class="h-[90px] w-full object-contain bg-slate-50" alt="thumb">
            </button>
          </template>
        </div>
      </div>

      <div class="mt-8">
        <div class="text-2xl sm:text-3xl font-medium text-slate-900">{{ $judul ?: 'Detail Motor Sewa' }}</div>
        <div class="mt-1 text-slate-500">{{ $subjudul }}</div>
        <div class="mt-3 inline-flex items-center gap-2 text-slate-500">
          <i class="bi bi-geo-alt"></i>
          <span>{{ $motor->lokasi_motor ?? 'Jakarta, Indonesia' }}</span>
        </div>
      </div>

      <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @php
          $specs = [
            ['icon'=>'bi-gear-wide-connected','label'=>'Transmisi :','val'=>$motor->transmisi_motor ?? '-'],
            ['icon'=>'bi-fuel-pump','label'=>'Bahan Bakar :','val'=>$motor->jenis_bahan_bakar_motor ?? '-'],
            ['icon'=>'bi-speedometer2','label'=>'Mesin :','val'=>($motor->kapasitas_mesin_motor ? $motor->kapasitas_mesin_motor.' cc' : '-')],
            ['icon'=>'bi-palette','label'=>'Warna :','val'=>$motor->warna_motor ?? '-'],
            ['icon'=>'bi-bicycle','label'=>'Jenis :','val'=>$motor->jenis_motor ?? '-'],
            ['icon'=>'bi-calendar2','label'=>'Tahun :','val'=>$motor->tahun_motor ?? '-'],
          ];
        @endphp

        @foreach($specs as $s)
          <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-100 p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-sky-50 text-[#51A3EE] flex items-center justify-center">
              <i class="bi {{ $s['icon'] }} text-xl"></i>
            </div>
            <div class="text-sm">
              <div class="text-slate-400">{{ $s['label'] }}</div>
              <div class="font-medium text-slate-800">{{ $s['val'] }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-6 border-b border-slate-100" x-data="{ tab: 'overview' }">
        <div class="flex items-center gap-10 text-base text-slate-500">
          <button class="pb-3"
                  :class="tab==='overview' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : ''"
                  @click="tab='overview'">Overview</button>
          <button class="pb-3"
                  :class="tab==='asuransi' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : ''"
                  @click="tab='asuransi'">Informasi Asuransi</button>
        </div>

        <div class="pt-5 text-base text-slate-600 leading-relaxed" x-show="tab==='overview'" x-cloak>
          {!! nl2br(e($motor->deskripsi_iklan_motor ?? 'Belum ada deskripsi.')) !!}
        </div>

        <div class="pt-5 text-base text-slate-600 leading-relaxed" x-show="tab==='asuransi'" x-cloak>
          <div class="text-slate-500">Informasi asuransi motor (placeholder).</div>
        </div>
      </div>

      <div class="mt-10 rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6"
           x-data="{ tab:'Semua', open:false, reviews:@js($reviewsData), get filtered(){ if(this.tab==='Semua') return this.reviews; return this.reviews.filter(r=>r.cat===this.tab); } }">
        <div class="flex items-center justify-between gap-3">
          <div class="text-lg font-medium text-slate-900 md:text-2xl">Review Pengguna Sewa Motor</div>
          <button type="button" class="inline-flex items-center justify-center rounded-full bg-[#51A3EE] text-white hover:bg-sky-600 transition px-6 py-2 text-base font-medium" @click="open=true">
            Tulis Review
          </button>
        </div>

        <div class="mt-6 border-b border-slate-100">
          <div class="flex items-center gap-8 text-base">
            <button class="pb-3" :class="tab==='Semua' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : 'text-slate-500'" @click="tab='Semua'">Semua ({{ $counts['Semua'] }})</button>
            <button class="pb-3" :class="tab==='Fitur' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : 'text-slate-500'" @click="tab='Fitur'">Fitur ({{ $counts['Fitur'] }})</button>
            <button class="pb-3" :class="tab==='Specs' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : 'text-slate-500'" @click="tab='Specs'">Specs ({{ $counts['Specs'] }})</button>
            <button class="pb-3" :class="tab==='Performa' ? 'text-[#51A3EE] border-b-2 border-[#51A3EE] font-medium' : 'text-slate-500'" @click="tab='Performa'">Performa ({{ $counts['Performa'] }})</button>
          </div>
        </div>

        <div class="mt-5 space-y-4">
          <template x-for="(r, idx) in filtered" :key="idx">
            <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-sm p-5">
              <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                  <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(r.name)}&background=E2E8F0&color=0F172A`"
                       class="h-12 w-12 rounded-full object-cover bg-slate-100" alt="avatar">
                  <div>
                    <div class="text-base font-medium text-slate-900" x-text="r.name"></div>
                    <div class="mt-1 flex items-center gap-1 text-amber-400">
                      <template x-for="i in 5" :key="i">
                        <i class="bi" :class="i <= r.rating ? 'bi-star-fill' : 'bi-star'"></i>
                      </template>
                    </div>
                  </div>
                </div>
                <div class="text-sm text-slate-400 whitespace-nowrap" x-text="r.ago"></div>
              </div>
              <div class="mt-4 text-sm text-slate-500 leading-relaxed break-words" x-text="r.text"></div>
            </div>
          </template>
        </div>

        <div x-show="open" x-cloak class="fixed inset-0 z-50">
          <div class="absolute inset-0 bg-black/30" @click="open=false"></div>
          <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden">
              <div class="flex items-center justify-between px-6 py-4">
                <div class="text-lg font-medium text-slate-900">Tulis Review</div>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="open=false">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
              <div class="px-6 pb-6">
                <div class="text-sm text-slate-500">*UI saja (belum tersimpan ke database motor_sewa)</div>
                <div class="mt-4 grid gap-4">
                  <input class="w-full rounded-xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3" placeholder="Nama">
                  <textarea class="w-full min-h-[160px] rounded-xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3" placeholder="Tulis review kamu..."></textarea>
                  <button type="button" class="w-full rounded-2xl bg-[#51A3EE] py-3 text-white font-medium hover:bg-sky-600 transition" @click="open=false">
                    Kirim Review
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>



    </div>

    <aside class="lg:col-span-4 space-y-4">
      <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
        <div class="text-sm text-slate-500">Harga Sewa Motor</div>
        <div class="mt-1 text-2xl font-medium text-slate-900">
          {{ $fmtRupiah($motor->harga_motor) }}
        </div>

        <div class="mt-6 text-base font-medium text-slate-900">Customer Service</div>
        <div class="mt-3 flex items-center gap-3 rounded-2xl bg-slate-50 ring-1 ring-slate-100 p-3">
          <img src="{{ $sellerAvatar }}" class="h-10 w-10 rounded-full object-cover bg-white" alt="cs">
          <div class="min-w-0">
            <div class="font-medium text-slate-900 truncate">{{ $sellerName }}</div>
            <div class="text-sm text-slate-500 truncate">{{ $sellerCity }}</div>
          </div>
        </div>

        @auth
          <a href="{{ $seller ? route('chat.show', $seller->id) : route('chat.index') }}"
             class="mt-4 w-full inline-flex items-center justify-center rounded-full bg-[#51A3EE] px-6 py-3 text-white font-medium hover:bg-sky-600 transition">
            Pesan Motor
          </a>
        @else
          <button type="button"
                  class="mt-4 w-full inline-flex items-center justify-center rounded-full bg-[#51A3EE] px-6 py-3 text-white font-medium hover:bg-sky-600 transition"
                  @click="window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { tab: 'login' } }))">
            Pesan Motor
          </button>
        @endauth

        <a href="{{ $waLink ?: '#' }}" target="_blank"
           class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-full bg-sky-50 text-[#51A3EE] ring-1 ring-sky-200 px-6 py-3 font-medium hover:bg-sky-100 transition {{ $waLink ? '' : 'pointer-events-none opacity-60' }}">
          <i class="bi bi-whatsapp"></i> WhatsApp
        </a>

        <div class="mt-6">
          <div class="text-base font-medium text-slate-900">Location</div>
          <div class="mt-1 text-sm text-slate-500">Alamat:</div>

          <div class="mt-3 rounded-2xl overflow-hidden ring-1 ring-slate-100 bg-slate-50">
            <iframe src="{{ $mapEmbed }}" class="w-full h-[170px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </aside>
  </div>

        {{-- Rekomendasi (3 card) --}}
      <div class="mt-12 lg:col-span-12">
        <div class="flex items-center justify-between">
          <div class="text-lg font-medium text-slate-900 md:text-2xl">Direkomendasikan untuk anda</div>
          <a href="{{ route('sewa.index') }}" class="text-[#51A3EE] hover:underline">Lihat semua mobil</a>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach(collect($rekomendasi ?? [])->take(3) as $m)
            @php
              $img = $m->gambar_motor ? asset('storage/'.$m->gambar_motor) : 'https://placehold.co/900x520?text=Motor';
              $brandR = $m->merk_motor ?? 'Brand';
              $titleR = trim(($m->merk_motor ?? '').' '.($m->model_motor ?? ''));
              $subtitleR = ($m->kapasitas_mesin_motor ? $m->kapasitas_mesin_motor.'cc' : '150cc') . ' • ' . ($m->transmisi_motor ?? 'Automatic');
            @endphp

            <div class="rounded-2xl bg-white shadow-md overflow-hidden">
              <div class="flex items-start justify-between gap-3 px-5 pt-5">
                <div class="flex items-center gap-5">
                  <img src="{{ $brandLogo($brandR) }}" class="h-12 w-12 object-contain" alt="{{ $brandR }}">
                  <div class="min-w-0">
                    <div class="truncate text-base font-medium text-slate-900">{{ $titleR ?: $brandR }}</div>
                    <div class="truncate text-sm text-slate-500">{{ $subtitleR }}</div>
                  </div>
                </div>

                <a href="{{ route('sewa.motor.show', $m->id) }}"
                   class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-100 text-slate-700 shadow-sm">
                  <i class="bi bi-arrow-up-right"></i>
                </a>
              </div>

              <div class="px-5 pt-4">
                <div class="h-[170px] w-full bg-slate-100 rounded-xl">
                  <img src="{{ $img }}" class="h-full w-full object-contain" alt="motor">
                </div>
              </div>

              <div class="px-5 pb-5 pt-3">
                <div class="text-sm text-slate-500">Harga</div>
                <div class="mt-1 flex items-center justify-between gap-3">
                  <div class="truncate text-base font-medium text-slate-900">
                    {{ $fmtRupiah($m->harga_motor) }}
                    <span class="text-base font-normal text-slate-500">/hari</span>
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
      </div>
</div>
@endsection
