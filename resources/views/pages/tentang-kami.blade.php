@extends('layouts.main') {{-- PASTIIN ini layout utama kamu --}}
@section('title', 'Tentang Kami')

@section('content')
<div class="bg-white">
    {{-- HERO --}}
    <section class="mx-auto max-w-[1200px] px-4 pt-10 md:pt-14">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-12 md:items-start">
            <div class="md:col-span-7">
                <h1 class="text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">
                    Lorem ipsum dolor sit<br class="hidden md:block" />
                    amet consectetur.
                </h1>
            </div>

            <div class="md:col-span-5">
                <p class="text-sm leading-6 text-slate-500 md:text-base">
                    Lorem ipsum dolor sit amet consectetur. Nisl viverra ut neque fusce dolor.
                    Amet aliquet convallis donec ultrices aliquam venenatis magna tortor faucibus.
                </p>
            </div>
        </div>

        {{-- HERO IMAGE --}}
        <div class="mt-8 overflow-hidden rounded-2xl bg-slate-100">
            <img src="{{ asset('assets/img/about/hero-car.png') }}"
                 alt="Hero"
                 class="h-[240px] w-full object-cover md:h-[320px] lg:h-[360px]" />
        </div>
    </section>

    {{-- SECTION: TITLE CENTER + CARD TEXT --}}
    <section class="mx-auto max-w-[1200px] px-4 py-12 md:py-14">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-slate-900 md:text-3xl">
                Lorem ipsum dolor sit
            </h2>
            <p class="mx-auto mt-2 max-w-[680px] text-sm text-slate-500 md:text-base">
                Kami berdedikasi mewujudkan hunian ideal Anda, menjadi mitra terpercaya yang mengutamakan kualitas, inovasi, dan integritas.
            </p>
        </div>

        @php
            $slides = [
                [
                    'title' => 'Visi Kami',
                    'text'  => 'Menjadi penyedia terdepan layanan arsitektur dan desain interior, serta solusi struktural, yang dikenal karena reputasi dan keunggulan kami. Kami bertujuan mencapainya melalui pengembangan sumber daya manusia yang berlandaskan kompetensi spiritual, intelektual, dan sosial. Kami bercita-cita menjadi perusahaan jasa konstruksi utama, yang berdedikasi menghadirkan solusi hunian nyaman, aman, dan berkelanjutan sesuai dengan kebutuhan klien yang terus berkembang.',
                ],
                        [
                    'title' => 'Misi Kami',
                    'text'  => 'Menjadi penyedia terdepan layanan arsitektur dan desain interior, serta solusi struktural, yang dikenal karena reputasi dan keunggulan kami. Kami bertujuan mencapainya melalui pengembangan sumber daya manusia yang berlandaskan kompetensi spiritual, intelektual, dan sosial. Kami bercita-cita menjadi perusahaan jasa konstruksi utama, yang berdedikasi menghadirkan solusi hunian nyaman, aman, dan berkelanjutan sesuai dengan kebutuhan klien yang terus berkembang.',
                ],
            ];
        @endphp

        <div class="mt-8"
            x-data="{
                i: 0,
                anim: true,
                total: {{ count($slides) }},
                maxH: 0,

                next() { if (this.i < this.total) this.i++ },   // bisa sampai clone (total)
                prev() { if (this.i > 0) this.i-- },

                onEnd() {
                    // jika sudah sampai CLONE (index == total), reset ke 0 TANPA animasi
                    if (this.i === this.total) {
                        this.anim = false
                        this.i = 0
                        // re-enable anim di next tick biar geser berikutnya halus
                        this.$nextTick(() => { this.anim = true })
                    }
                },

                setMaxHeight() {
                    this.$nextTick(() => {
                        let cards = this.$refs.cards.querySelectorAll('[data-card]')
                        cards.forEach(el => {
                            this.maxH = Math.max(this.maxH, el.offsetHeight)
                        })
                    })
                }
            }"
            x-init="setMaxHeight()">
            <div class="overflow-hidden">
                <div class="flex"
                    @transitionend="onEnd()"
                    :class="anim ? 'transition-transform duration-500 ease-out' : ''"
                    :style="`transform: translateX(-${i * 100}%);`">

                    <div x-ref="cards" class="flex min-w-full">
                        {{-- CARDS ASLI --}}
                        @foreach($slides as $s)
                            <div class="min-w-full">
                                <div data-card
                                    :style="maxH ? `min-height:${maxH}px` : ''"
                                    class="relative rounded-3xl bg-white ring-1 ring-slate-100
                                            shadow-md my-2
                                            px-8 py-10 md:px-10 md:py-12">

                                    {{-- accent bar kiri --}}
                                    <img
                                        src="{{ asset('assets/img/about/side.png') }}"
                                        alt="accent"
                                        class="absolute left-0 top-1/2 -translate-y-1/2 h-[70%] w-3"
                                    />

                                    {{-- isi --}}
                                    <div class="pr-2 md:pr-24">
                                        <p class="text-[15px] leading-7 text-slate-500">
                                            {{ $s['text'] }}
                                        </p>
                                    </div>

                                    <div class="mt-10 text-2xl font-semibold text-slate-900">
                                        {{ $s['title'] }}
                                    </div>

                                    {{-- arrows posisi persis kanan bawah --}}
                                    <div class="absolute right-8 bottom-8 flex items-center gap-3">
                                        <button type="button"
                                                @click="prev"
                                                :disabled="i===0"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-full
                                                    bg-slate-100 text-slate-500 hover:bg-slate-200
                                                    disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>

                                        <button type="button"
                                                @click="next"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-full
                                                    bg-[#51A3EE] text-white hover:bg-sky-600">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- CLONE slide pertama --}}
                        <div class="min-w-full">
                            <div data-card
                                :style="maxH ? `min-height:${maxH}px` : ''"
                                class="relative rounded-3xl bg-white ring-1 ring-slate-100
                                        shadow-md my-2
                                        px-8 py-10 md:px-10 md:py-12">

                                <img
                                        src="{{ asset('assets/img/about/side.png') }}"
                                        alt="accent"
                                        class="absolute left-0 top-1/2 -translate-y-1/2 h-[70%] w-3"
                                    />

                                <div class="pr-2 md:pr-24">
                                    <p class="text-[15px] leading-7 text-slate-500">
                                        {{ $slides[0]['text'] }}
                                    </p>
                                </div>

                                <div class="mt-10 text-2xl font-semibold text-slate-900">
                                    {{ $slides[0]['title'] }}
                                </div>

                                {{-- arrows tetap ada agar layout konsisten --}}
                                <div class="absolute right-8 bottom-8 flex items-center gap-3">
                                    <button type="button"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 opacity-50 cursor-not-allowed">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#51A3EE] text-white">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </section>

{{-- SECTION: IMAGE LEFT + FEATURES RIGHT --}}
<section class="mx-auto max-w-[1200px] px-4 pb-16 md:pb-20">
    <div class="grid grid-cols-1 gap-10 md:grid-cols-12 md:items-start">
        <div class="md:col-span-7">
            <h2 class="text-3xl font-semibold leading-tight text-slate-900 md:text-4xl">
                Lorem ipsum dolor sit<br class="hidden md:block" />
                amet consectetur
            </h2>
        </div>

        <div class="md:col-span-5">
            <p class="text-sm leading-6 text-slate-500 md:text-base">
                Lorem ipsum dolor sit amet consectetur. Nisl viverra ut neque fusce dolor.
                Amet aliquet convallis donec ultrices aliquam venenatis magna tortor faucibus.
            </p>
        </div>
    </div>

    {{-- ROW KUNCI: stretch supaya kiri & kanan sama tinggi --}}
    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-12 md:items-stretch">
        {{-- image --}}
        <div class="md:col-span-6">
            <div class="h-full overflow-hidden rounded-2xl bg-slate-100">
                <img src="{{ asset('assets/img/about/red-car.png') }}"
                     alt="About"
                     class="h-full w-full object-cover" />
            </div>
        </div>

        {{-- features --}}
        <div class="md:col-span-6">
            @php
                $features = [
                    ['no' => '01', 'title' => 'Tim Profesional', 'desc' => 'Kami didukung oleh tim ahli berpengalaman yang berdedikasi menciptakan karya berkualitas tinggi.'],
                    ['no' => '02', 'title' => 'Solusi Terintegrasi', 'desc' => 'Kami menyediakan layanan terpadu dari desain hingga konstruksi, demi kemudahan dan efisiensi proyek.'],
                    ['no' => '03', 'title' => 'Kualitas Terbaik', 'desc' => 'Kami menggunakan material premium & standar pengerjaan tertinggi untuk hasil maksimal.'],
                    ['no' => '04', 'title' => 'Design Kreatif', 'desc' => 'Kami mengedepankan inovasi dan estetika untuk menciptakan ruang yang fungsional dan indah.'],
                ];
            @endphp

            {{-- grid kanan dibuat h-full supaya sejajar dengan gambar --}}
            <div class="grid h-full grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach($features as $f)
                    <div class="h-full rounded-2xl bg-white p-6 shadow-[0_10px_30px_rgba(2,6,23,0.06)] ring-1 ring-slate-100">
                        
                        {{-- nomor di atas --}}
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#51A3EE] text-base font-semibold text-white">
                            {{ $f['no'] }}
                        </div>

                        {{-- title --}}
                        <div class="mt-4 text-lg font-semibold text-slate-900">
                            {{ $f['title'] }}
                        </div>

                        {{-- description --}}
                        <div class="mt-2 text-sm leading-6 text-slate-500">
                            {{ $f['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>


        </div>
    </div>
</section>

</div>
@endsection
