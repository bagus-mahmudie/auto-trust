@extends('layouts.main')

@section('content')
@php
    $imgUrl = function ($path) {
        if (empty($path)) return 'https://placehold.co/1200x800?text=No+Image';
        if (str_starts_with($path, 'http')) return $path;

        // kalau sudah "storage/..." jangan double
        if (str_starts_with($path, 'storage/')) return asset(ltrim($path,'/'));

        // kalau memang path seharusnya dari storage
        return asset('storage/' . ltrim($path, '/'));
    };
    // =========================
    // DATA DARI DB (FALLBACK)
    // =========================
    $brand   = $motor->merk_motor ?? '';
    $model   = $motor->model_motor ?? '';
    $tahun   = $motor->tahun_motor ?? null;

    $judul   = trim(($brand ? $brand.' ' : '').($model ? $model.' ' : '').($tahun ?: ''));

    $fuel    = $motor->jenis_bahan_bakar_motor ?? '-';
    $lokasi  = $motor->lokasi_motor ?? '-';

    $bodi    = $motor->jenis_kendaraan_motor ?? '-';
    $km      = $motor->km_motor ?? 0;
    $trans   = $motor->transmisi_motor ?? '-';
    $cc      = $motor->kapasitas_mesin_motor ?? '-';
    $kondisi = $motor->kondisi_motor ?? 'Baru';

    $harga   = (int) ($motor->harga_motor ?? 0);

    // Penjual (fallback)
    $sellerName  = $motor->nama_penjual_motor ?? 'Penjual';
    $sellerEmail = $motor->email_penjual_motor ?? '';
    $sellerPhone = $motor->no_hp_penjual_motor ?? '';
    $sellerCity  = $motor->lokasi_motor ?? 'Indonesia';

    // Avatar simple
    $sellerAvatar = 'https://ui-avatars.com/api/?name='.urlencode($sellerName).'&background=E2E8F0&color=0F172A';

    // Images dari controller (cover + gallery)
    $imgs = collect($images ?? [])
        ->map(fn($p) => $imgUrl($p))
        ->values();

    if ($imgs->isEmpty()) {
        $imgs = collect([asset('assets/img/motor-terbaru/adv.jpg')]);
    }

    // Views (kalau belum ada)
    $viewCount = $views ?? 0;
    $isLiked = auth()->check() && in_array($motor->id, $likedMotorIds ?? []);
@endphp

<div class="bg-[#f6f8fb] min-h-[calc(100vh-112px)]">
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">

        {{-- TOP ACTION BAR --}}
        <div class="flex items-center justify-between mb-4"
            x-data="{
                shareOpen:false,
                copied:false,
                url: @js(url()->current()),
                title: @js($judul ?: 'Detail Motor'),
                copyLink(){
                    navigator.clipboard.writeText(this.url);
                    this.copied = true;
                    setTimeout(()=> this.copied=false, 1200);
                }
            }"
            @keydown.escape.window="shareOpen=false">
            <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-base text-slate-600 hover:text-slate-900">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>

            <div class="flex items-center gap-2">
                {{-- heart (biarkan sesuai versi kamu yang sudah sync) --}}
                <button
                    type="button"
                    class="h-10 w-10 rounded-xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 inline-flex items-center justify-center"
                    data-motor-like-btn="{{ $motor->slug ?? $motor->id }}"
                    data-motor-id="{{ $motor->slug ?? $motor->id }}"
                    aria-label="favorite"
                >
                    <i class="bi bi-heart-fill {{ $isLiked ? 'text-red-500' : 'text-slate-400' }} text-lg"></i>
                </button>

                <button type="button"
                        class="h-10 px-4 rounded-xl bg-white ring-1 ring-slate-200 text-slate-600 hover:bg-slate-50 inline-flex items-center gap-2"
                        @click="shareOpen=true">
                    <i class="bi bi-share"></i> Bagikan
                </button>
            </div>

            {{-- SHARE MODAL (sama persis seperti mobil) --}}
            <div x-show="shareOpen"
                x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center"
                aria-modal="true" role="dialog">
                <div class="absolute inset-0 bg-black/30" @click="shareOpen=false"></div>

                <div x-transition
                    class="relative w-[92vw] max-w-[420px] rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 p-5">
                    <div class="flex items-center justify-between">
                        <div class="text-base sm:text-lg font-medium text-slate-900">Share</div>
                        <button type="button"
                                class="h-8 w-8 rounded-full hover:bg-slate-100 inline-flex items-center justify-center text-slate-500"
                                @click="shareOpen=false">
                            <i class="bi bi-x text-2xl leading-none"></i>
                        </button>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-5 text-center">

                        {{-- WhatsApp --}}
                        <a :href="`https://wa.me/?text=${encodeURIComponent(title+' - '+url)}`"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/whatsapp.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="WhatsApp">
                            </div>
                            <div class="mt-2 text-sm text-slate-600">WhatsApp</div>
                        </a>

                        {{-- Instagram --}}
                        <a href="https://www.instagram.com/"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/instagram.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="Instagram">
                            </div>
                            <div class="mt-2 text-sm text-slate-600">Instagram</div>
                        </a>

                        {{-- Facebook --}}
                        <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/facebook.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="Facebook">
                            </div>
                            <div class="mt-2 text-sm text-slate-600">Facebook</div>
                        </a>

                        {{-- Tiktok --}}
                        <a href="https://www.tiktok.com/"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/tiktok.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="Tiktok">
                            </div>
                            <div class="mt-2 text-sm text-slate-600">Tiktok</div>
                        </a>

                        {{-- Telegram --}}
                        <a :href="`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/telegram.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="Telegram">
                            </div>
                            <div class="mt-2 text-sm text-slate-600">Telegram</div>
                        </a>

                        {{-- Discord --}}
                        <a href="https://discord.com/"
                        target="_blank"
                        class="group">
                            <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/img/icons/discord.png') }}"
                                    class="h-7 w-7 object-contain"
                                    alt="Discord">
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
                                @click="copyLink()"
                                title="Copy">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>

                    <div class="mt-2 text-xs text-slate-500" x-show="copied" x-transition>
                        Link disalin ✓
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================
             ROW 1: GALLERY
           ========================================= --}}
        <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-4 sm:p-6 mb-3"
             x-data="{ active: 0, imgs: @js($imgs) }">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- BIG IMAGE --}}
                <div class="md:col-span-8">
                    <div class="relative rounded-2xl overflow-hidden ring-1 ring-slate-100">

                        <!-- Background blur (cover) -->
                        <img :src="imgs[active]"
                            class="absolute inset-0 w-full h-full object-cover scale-[1.4] blur-[40px]"
                            alt="">

                        <div class="absolute inset-0 bg-black/5"></div>

                        <!-- Foreground (contain) + mask fade -->
                        <img :src="imgs[active]"
                            class="relative w-full h-[260px] sm:h-[340px] md:h-[380px] object-contain
                                    [mask-image:radial-gradient(ellipse_at_center,rgba(0,0,0,1)_60%,rgba(0,0,0,0)_88%)]
                                    [-webkit-mask-image:radial-gradient(ellipse_at_center,rgba(0,0,0,1)_60%,rgba(0,0,0,0)_88%)]"
                            alt="foto motor">

                    </div>
                </div>

                {{-- THUMBS --}}
                <div class="md:col-span-4 grid grid-cols-2 gap-3">
                    <template x-for="(src,i) in imgs.slice(0,4)" :key="i">
                        <button type="button"
                                class="relative rounded-2xl overflow-hidden transition"
                                :class="active===i 
                                    ? 'ring-2 ring-sky-500 shadow-lg shadow-sky-500/30' 
                                    : 'ring-1 ring-slate-200 hover:ring-slate-300'"
                                @click="active=i">

                            <!-- Background Blur -->
                            <img :src="src"
                                class="absolute inset-0 w-full h-full object-cover scale-[1.35] blur-[30px]"
                                alt="">

                            <!-- Foreground (fade edge) -->
                            <img :src="src"
                                class="relative w-full h-[120px] sm:h-[160px] md:h-[175px] object-contain
                                        [mask-image:radial-gradient(ellipse_at_center,rgba(0,0,0,1)_55%,rgba(0,0,0,0)_80%)]
                                        [-webkit-mask-image:radial-gradient(ellipse_at_center,rgba(0,0,0,1)_55%,rgba(0,0,0,0)_80%)]"
                                alt="thumb">

                        </button>


                    </template>
                </div>
            </div>
        </div>

        {{-- =========================================
             ROW 2: DETAIL (8) + SIDEBAR (4)
           ========================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT CONTENT --}}
            <section class="lg:col-span-8 space-y-3">

                {{-- TITLE + META + SPECS --}}
                <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-semibold text-slate-900">
                                {{ $judul ?: 'Detail Motor' }}
                            </h1>

                            <div class="mt-2 flex flex-wrap items-center text-base text-slate-500 divide-x divide-slate-300">

                                <div class="inline-flex items-center gap-2 px-3 first:pl-0">
                                    <i class="bi bi-award"></i>
                                    <span>{{ $brand }}</span>
                                </div>

                                <div class="inline-flex items-center gap-2 px-3">
                                    <i class="bi bi-fuel-pump"></i>
                                    <span>{{ $fuel }}</span>
                                </div>

                                <div class="inline-flex items-center gap-2 px-3">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $tahun }}</span>
                                </div>

                                <div class="inline-flex items-center gap-2 px-3">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>{{ $kondisi }}</span>
                                </div>

                            </div>

                            <div class="mt-2 flex items-center gap-2 text-base text-slate-500">
                                <i class="bi bi-geo-alt-fill text-sky-600"></i>
                                <span>{{ $lokasi }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- SPECS BOXES --}}
                    <div class="mt-5 grid grid-cols-2 sm:grid-cols-5 gap-3 text-base">
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3">
                            <div class="text-sm text-slate-500">Tipe</div>
                            <div class="font-medium text-slate-900">{{ $bodi }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3">
                            <div class="text-sm text-slate-500">Kilometer</div>
                            <div class="font-medium text-slate-900">{{ $km }} KM</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3">
                            <div class="text-sm text-slate-500">Transmisi</div>
                            <div class="font-medium text-slate-900">{{ $trans }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3">
                            <div class="text-sm text-slate-500">Kapasitas</div>
                            <div class="font-medium text-slate-900">{{ $cc }} cc</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3">
                            <div class="text-sm text-slate-500">Penjual</div>
                            <div class="font-medium line-clamp-1 text-slate-900">{{ $sellerName }}</div>
                        </div>
                    </div>
                </div>

                {{-- TABS --}}
                <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
                    <div class="mt-0" x-data="{ tab:'desc' }">
                        <div class="flex items-center gap-8 text-base border-b border-slate-100">
                            <button type="button"
                                    class="pb-3"
                                    :class="tab==='desc' ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'text-slate-500'"
                                    @click="tab='desc'">
                                Deskripsi
                            </button>

                            <button type="button"
                                    class="pb-3"
                                    :class="tab==='legal' ? 'text-sky-600 font-semibold border-b-2 border-sky-600' : 'text-slate-500'"
                                    @click="tab='legal'">
                                Kondisi &amp; Legalitas
                            </button>
                        </div>

                        <div class="pt-4 text-base text-slate-600 leading-relaxed" x-show="tab==='desc'" x-cloak>
                            {!! nl2br(e($motor->deskripsi_iklan_motor ?? 'Belum ada deskripsi.')) !!}
                        </div>

                        <div class="pt-4 text-base text-slate-600 leading-relaxed" x-show="tab==='legal'" x-cloak>
                            <ul class="space-y-2">
                                <li><span class="text-slate-500">Status Pajak:</span> {{ $motor->status_pajak_motor ?? '-' }}</li>
                                <li><span class="text-slate-500">Kelengkapan Pajak:</span> {{ $motor->kelengkapan_pajak_motor ?? '-' }}</li>
                                <li><span class="text-slate-500">Kelengkapan Surat:</span> {{ $motor->kelengkapan_surat_motor ?? '-' }}</li>
                                <li><span class="text-slate-500">Atas Nama:</span> {{ $motor->atas_nama_motor ?? '-' }}</li>
                                <li><span class="text-slate-500">Riwayat Servis:</span> {{ $motor->riwayat_servis_motor ?? '-' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- REVIEW SECTION --}}
                <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-semibold text-slate-900 md:text-2xl">
                            Review Pengguna {{ $judul ?: '' }}
                        </div>

                        @if(auth()->check() && ($hasReviewed ?? false))
                            <button type="button" disabled
                                    class="inline-flex h-10 w-10 sm:h-auto sm:w-auto
                                        items-center justify-center
                                        rounded-full bg-slate-200 text-slate-500 cursor-not-allowed
                                        sm:px-6 sm:py-2 text-base font-medium">
                                <i class="bi bi-plus sm:hidden text-xl"></i>
                                <span class="hidden sm:inline">Sudah Review</span>
                            </button>
                        @else

                        <button type="button"
                                @click="$store.reviewModal.open()"
                                class="inline-flex h-10 w-10 sm:h-auto sm:w-auto
                                    items-center justify-center
                                    rounded-full bg-[#51A3EE] text-white
                                    hover:bg-sky-600 transition
                                    sm:px-6 sm:py-2 text-base font-medium">

                            {{-- MOBILE: ICON PLUS --}}
                            <i class="bi bi-plus sm:hidden text-xl"></i>

                            {{-- TABLET & DESKTOP: TEXT --}}
                            <span class="hidden sm:inline">
                                Tulis Review
                            </span>
                        </button>
                        @endif
                    </div>

                    <div class="mt-5 space-y-4 max-h-[520px] overflow-y-auto pr-2 pb-4 scroll-smooth no-scrollbar">
                        @forelse(($reviews ?? collect()) as $r)
                            @php
                                $reviewName = $r->user->name ?? $r->name ?? 'Anonim';
                                $avatar = $r->user->foto_profile ?? null;

                                $avatarUrl = $avatar
                                    ? (str_starts_with($avatar, 'http') ? $avatar : asset('storage/' . ltrim($avatar, '/')))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($reviewName) . '&background=E2E8F0&color=0F172A';
                            @endphp

                            <div class="rounded-2xl bg-white ring-1 ring-slate-100 shadow-md p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <img src="{{ $avatarUrl }}"
                                            class="h-12 w-12 rounded-full object-cover bg-slate-100"
                                            alt="avatar">

                                        <div>
                                            <div class="text-base font-semibold text-slate-900">{{ $reviewName }}</div>
                                            <div class="mt-1 flex items-center gap-1 text-amber-400">
                                                @for($i=1;$i<=5;$i++)
                                                    <i class="bi {{ $i <= (int)$r->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-sm text-slate-400 whitespace-nowrap">
                                        {{ optional($r->created_at)->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="mt-4 text-sm text-slate-500 leading-relaxed break-all break-words whitespace-normal">
                                    {{ $r->comment }}
                                </div>
                            </div>
                        @empty
                            <div class="text-base text-slate-500">
                                Belum ada review. Jadilah yang pertama menulis review.
                            </div>
                        @endforelse
                    </div>

                    {{-- MODAL --}}
                    <div x-show="$store.reviewModal.isOpen" x-cloak class="fixed inset-0 z-50">
                        <div class="absolute inset-0 bg-black/30" @click="$store.reviewModal.close()"></div>

                        <div class="absolute inset-0 flex items-center justify-center p-4">
                            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden">
                                <div class="flex items-center justify-between px-6 py-4 pb-0">

                                    <div class="flex items-center gap-2 text-amber-400">
                                        <template x-for="i in 5" :key="i">
                                            <button type="button"
                                                    class="h-10 w-10 rounded-lg bg-slate-50 ring-1 ring-slate-200 hover:bg-slate-100 flex items-center justify-center"
                                                    @mouseenter="$store.reviewModal.setHover(i)"
                                                    @mouseleave="$store.reviewModal.setHover(0)"
                                                    @click="$store.reviewModal.setRating(i)">
                                                <i class="bi"
                                                   :class="($store.reviewModal.hover
                                                            ? i <= $store.reviewModal.hover
                                                            : i <= $store.reviewModal.rating)
                                                            ? 'bi-star-fill'
                                                            : 'bi-star'"></i>
                                            </button>
                                        </template>
                                    </div>

                                    <button type="button" class="text-slate-400 hover:text-slate-600"
                                            @click="$store.reviewModal.close()">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                {{-- NOTE:
                                     ini route review untuk MOTOR ya.
                                     pastikan kamu bikin route/controller-nya:
                                     route('motor.reviews.store', $motor)
                                --}}
                                <form method="POST" action="{{ route('motor.reviews.store', $motor) }}" class="px-6 py-5"
                                      @submit.prevent="$store.reviewModal.submit($event.target)">
                                    @csrf

                                    <input type="hidden" name="rating" :value="$store.reviewModal.rating">

                                    <div class="text-sm text-slate-500 mb-4">Click to rate</div>
                                    <div class="border-t border-slate-100 my-4"></div>

                                    <label class="block text-base font-medium text-slate-700">Name</label>
                                    <input type="text" name="name"
                                           value="{{ old('name', auth()->user()->name ?? '') }}"
                                           @if(auth()->check()) readonly @endif
                                           class="mt-2 w-full rounded-xl bg-slate-50 px-4 py-3 text-base focus:ring-sky-400"
                                           placeholder="Enter your name">

                                    <label class="block text-base font-medium text-slate-700 mt-6">Write a Review About This Motor</label>
                                    <textarea name="comment"
                                              class="mt-2 w-full min-h-[220px] rounded-xl bg-slate-50 px-4 py-3 text-base focus:ring-sky-400"
                                              placeholder="Start writing your review here">{{ old('comment') }}</textarea>

                                    <div class="mt-0 flex items-center gap-2 text-sm text-slate-600">
                                        <input id="agree" name="agree" type="checkbox" class="rounded border-slate-300">
                                        <label for="agree">I agree to the terms and conditions</label>
                                    </div>

                                    <button type="submit"
                                            class="mt-6 w-full text-base rounded-2xl bg-[#51A3EE] py-3 text-white font-medium hover:bg-sky-600 transition">
                                        Submit a review
                                    </button>

                                    <template x-if="$store.reviewModal.error">
                                        <div class="mt-3 text-sm text-red-600" x-text="$store.reviewModal.error"></div>
                                    </template>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            {{-- RIGHT SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-3">

                <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
                    <div class="text-sm text-slate-500">Harga Motor</div>
                    <div class="mt-1 text-2xl font-semibold text-slate-900">
                        Rp {{ number_format($harga, 0, ',', '.') }}
                    </div>

                    <div class="mt-5 text-base font-semibold text-slate-900">Harga Sudah Termasuk</div>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl bg-slate-50 p-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-patch-check text-lg"></i>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-slate-900">Motor bergaransi</div>
                                <div class="text-sm text-slate-500 leading-snug">
                                    Garansi mesin hingga 1 tahun
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-cash-coin text-lg"></i>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-slate-900">Jaminan uang kembali</div>
                                <div class="text-sm text-slate-500 leading-snug">
                                    Nikmati 7 hari jaminan uang kembali
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="bi bi-bicycle text-lg"></i>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-slate-900">Test ride dari rumah</div>
                                <div class="text-sm text-slate-500 leading-snug">
                                    Nikmati nyamannya test ride dari rumah
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <div class="text-base font-semibold text-slate-900">Penjual</div>

                        @php
                            $seller = $motor->user ?? null;

                            $sellerName2 = $seller->name ?? ($motor->nama_penjual_motor ?? 'Penjual');
                            $sellerCity2 = $seller->lokasi ?? ($motor->lokasi_motor ?? 'Indonesia');

                            $avatarUrl = ($seller && $seller->foto_profile)
                                ? asset('storage/' . ltrim($seller->foto_profile, '/'))
                                : 'https://ui-avatars.com/api/?name=' . urlencode($sellerName2) . '&background=E2E8F0&color=0F172A';

                            $profileUrl = $seller?->id ? route('seller.show', $seller->id) : '#';
                        @endphp

                        {{-- CARD PENJUAL (klik ke profile) --}}
                        @if($seller?->id)
                            <a href="{{ $profileUrl }}"
                            class="mt-3 block rounded-2xl bg-slate-50 p-4 hover:bg-slate-100 transition">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $avatarUrl }}"
                                        class="h-12 w-12 rounded-full object-cover bg-slate-200"
                                        alt="seller">

                                    <div class="min-w-0">
                                        <div class="text-base font-semibold text-slate-900 truncate">{{ $sellerName2 }}</div>
                                        <div class="text-sm text-slate-500 truncate">{{ $sellerCity2 }}</div>
                                    </div>
                                </div>
                            </a>
                        @else
                            <div class="mt-3 rounded-2xl bg-slate-50 p-4 flex items-center gap-3">
                                <img src="{{ $avatarUrl }}"
                                    class="h-12 w-12 rounded-full object-cover bg-slate-200"
                                    alt="seller">

                                <div class="min-w-0">
                                    <div class="text-base font-semibold text-slate-900 truncate">{{ $sellerName2 }}</div>
                                    <div class="text-sm text-slate-500 truncate">{{ $sellerCity2 }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- tombol chat internal full width --}}
                        @php
                            // ambil id penjual dari relasi (ini sudah ada di file kamu: $seller = $mobil->user)
                            $sellerId = $seller?->id;
                            $chatUrl = $sellerId ? route('chat.index', ['user' => $sellerId]) : '#';

                            // kalau login kamu bukan route('login'), ganti ke url login kamu
                            $loginUrl = url('/login'); // <-- SESUAIKAN kalau login route kamu beda
                        @endphp

                        @if($sellerId)
                            @auth
                                @if(auth()->id() !== $sellerId)
                                    <a href="{{ $chatUrl }}"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-semibold text-white hover:bg-sky-600 transition">
                                        <i class="bi bi-chat-dots mr-2"></i>
                                        Chat Dengan Penjual
                                    </a>
                                @else
                                    {{-- kalau yang lihat adalah penjualnya sendiri --}}
                                    <button type="button" disabled
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-slate-200 px-4 py-3 text-base font-semibold text-slate-500 cursor-not-allowed">
                                        <i class="bi bi-chat-dots mr-2"></i>
                                        Ini Iklan Kamu
                                    </button>
                                @endif
                            @else
                                {{-- belum login: arahkan login dulu lalu kembali ke chat --}}
                                <a href="{{ $loginUrl }}?intended={{ urlencode($chatUrl) }}"
                                class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-3 text-base font-semibold text-white hover:bg-sky-600 transition">
                                    <i class="bi bi-chat-dots mr-2"></i>
                                    Chat Dengan Penjual
                                </a>
                            @endauth
                        @else
                            <button type="button" disabled
                                class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-slate-200 px-4 py-3 text-base font-semibold text-slate-500 cursor-not-allowed">
                                Penjual tidak tersedia
                            </button>
                        @endif

                    </div>

                </div>

                {{-- LOCATION MAP --}}
                <div class="rounded-xl bg-white shadow-md ring-1 ring-slate-100 p-6">
                    <div class="text-base font-semibold text-slate-900">Location</div>

                    <div class="mt-2 text-base text-slate-600">
                        Alamat:
                        <div class="text-base text-slate-900">
                            {{ $lokasi }}
                        </div>
                    </div>

                    @php
                        $alamat = trim(($motor->alamat_motor ?? '') ?: ($motor->lokasi_motor ?? ''));
                        $alamatFinal = $alamat !== '' ? $alamat : 'Indonesia';

                        $gmapsQuery = urlencode($alamatFinal);
                        $gmapsSrc = "https://www.google.com/maps?q={$gmapsQuery}&output=embed";
                    @endphp

                    <div class="mt-4 overflow-hidden rounded-2xl bg-slate-50 ring-1 ring-slate-100">
                        <iframe
                            src="{{ $gmapsSrc }}"
                            class="h-[220px] w-full"
                            style="border:0;"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

            </aside>
        </div>
        

        
    </div>
    {{-- MOTOR POPULER DAN TERBARU --}}
    <section class="mx-auto max-w-[1200px] px-4 pb-16 pt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                <span class="block sm:inline">Motor Populer dan</span>
                <span class="block sm:inline">Terbaru</span>
            </h2>

            {{-- sesuaikan route kalau sudah ada --}}
            <a href="#"
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


