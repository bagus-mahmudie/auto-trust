@php
    $imgUrl = function ($path) {
        if (empty($path)) return '';
        if (str_starts_with($path, 'http')) return $path;

        // assets bawaan project (biar tetap jalan)
        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'build/') || str_starts_with($path, 'img/')) {
            return asset($path);
        }

        // selain itu anggap dari storage
        return asset('storage/' . ltrim($path, '/'));
    };
  // =========================
  // NORMALISASI DATA GAMBAR
  // =========================
  $images = [];

  if (!empty($motor?->gambar_motor)) {
    $images[] = $motor->gambar_motor;
  }

  // galery_motor bisa: array (cast), json string, longtext biasa
  $gallery = $motor?->galery_motor;

  if (is_string($gallery)) {
    $decoded = json_decode($gallery, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
      $gallery = $decoded;
    } else {
      $gallery = []; // kalau string bukan json, amanin
    }
  }

  if (is_array($gallery)) {
    foreach ($gallery as $g) {
      if (!empty($g)) $images[] = $g;
    }
  }

  if (count($images) === 0) {
    $images[] = 'assets/img/homepage/hero.png';
  }

  // =========================
  // NORMALISASI DATA LAIN
  // =========================
  // Sesuaikan lokasi kalau di motor kamu field-nya beda
  $lokasi = $motor?->lokasi_motor ?? $motor?->lokasi ?? 'Indonesia';

  $title  = trim(($motor?->merk_motor ?? '').' '.($motor?->model_motor ?? ''));
  $harga  = (int)($motor?->harga_motor ?? 0);

  // badges mirip homepage
  $badges = [];

  // Sesuaikan nama field kalau berbeda
  $jenisKendaraan = $motor?->jenis_kendaraan_motor ?? $motor?->jenis_kendaraan ?? $motor?->jenis_motor ?? null;

  if (!empty($jenisKendaraan)) {
    $badges[] = ['icon' => 'bi-bicycle', 'text' => $jenisKendaraan];
  }
  if (!empty($motor?->merk_motor)) {
    $badges[] = ['icon' => 'bi-award', 'text' => $motor->merk_motor];
  }
  if (!empty($motor?->transmisi_motor)) {
    $badges[] = ['icon' => 'bi-gear', 'text' => $motor->transmisi_motor];
  }

  // Like state (array likedMotorIds dikirim dari controller kalau ada)
  $isLiked = auth()->check() && in_array($motor->id, $likedMotorIds ?? []);
@endphp

<div class="rounded-2xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] ring-1 ring-slate-100 overflow-hidden">
  {{-- IMAGE SLIDER --}}
  <div class="relative" data-carousel>

    {{-- LIKE BUTTON (TANPA COUNT) --}}
    <button
      type="button"
      class="like-btn absolute top-3 right-3 z-20 flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 shadow hover:scale-105 transition"
      data-type="motor"
      data-motor-id="{{ $motor->slug ?? $motor->id }}"
      aria-label="Like"
    >
      <i
        data-heart-icon
        class="bi bi-heart-fill text-base {{ $isLiked ? 'text-red-500' : 'text-slate-400' }}"
      ></i>
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
              class="h-[150px] w-full object-cover select-none"
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
    <div class="flex items-start justify-between gap-3">
      <div class="min-w-0">
        <div class="text-base font-medium text-slate-900 truncate">
          {{ $title ?: 'Motor' }}
        </div>
      </div>

      <div class="flex shrink-0 items-center gap-2 text-base">
        <i class="bi bi-star-fill text-amber-400"></i>
        <span class="font-medium text-slate-800">4.9</span>
      </div>
    </div>

    <div class="mt-1 flex items-center gap-2 text-sm text-slate-500">
      <i class="bi bi-geo-alt"></i>
      <span class="truncate">{{ $lokasi }}</span>
    </div>

    <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
      <div class="flex items-center gap-2 flex-nowrap min-w-max">
        @foreach($badges as $badge)
          <span class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1 text-sm font-normal text-[#707070] ring-1 ring-slate-100 whitespace-nowrap">
            <i class="bi {{ $badge['icon'] }} text-slate-500"></i>
            <span>{{ $badge['text'] }}</span>
          </span>
        @endforeach

        <span class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1 text-sm font-normal text-[#707070] ring-1 ring-slate-100 whitespace-nowrap">
          <span>+3</span>
        </span>
      </div>
    </div>
  </div>

  <div class="h-px bg-slate-100"></div>

  {{-- FOOTER --}}
  <div class="flex items-center justify-between p-4">
    <div class="flex flex-col">
      <span class="text-sm text-slate-500">Harga</span>
      <span class="mt-1 text-base font-medium text-slate-900">
        {{ rupiahJuta($harga) }}
      </span>
    </div>

    <a href="{{ route('motor.show', $motor) }}"
      class="inline-flex items-center justify-center rounded-full bg-slate-50 px-5 py-2 text-sm font-medium text-sky-500 ring-1 ring-slate-100 hover:bg-sky-50 hover:text-sky-600 transition">
      Detail
    </a>
  </div>
</div>
