@extends('layouts.main')

@section('content')
<div class="bg-[#f6f8fb] min-h-[calc(100vh-112px)]">
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-800 md:text-2xl">
                Lihat Siapa yang Lagi Butuh Mobil
            </h2>
        </div>

        {{-- Grid --}}
        @if($postings->count() === 0)
            <div class="mt-10 rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-10 text-center">
                <div class="text-base font-semibold text-slate-900">Belum ada postingan kebutuhan</div>
                <div class="mt-2 text-base text-slate-500">Nanti akan tampil di sini jika sudah ada yang membuat postingan.</div>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($postings as $p)
                    @php
                        $u = $p->user;
                        $avatar = ($u && $u->foto_profile)
                            ? asset('storage/'.$u->foto_profile)
                            : 'https://ui-avatars.com/api/?name='.urlencode($p->nama).'&background=E2E8F0&color=0F172A';

                        $tags = array_values(array_filter([
                            $p->tipe_bodi,
                            $p->merek,
                            $p->transmisi,
                        ]));
                    @endphp

                    {{-- CARD (SAMA PERSIS HOMEPAGE) --}}
                    <div class="rounded-2xl bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] ring-1 ring-slate-100 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $avatar }}" alt="{{ $p->nama }}"
                                     class="h-12 w-12 rounded-full object-cover ring-2 ring-white bg-slate-100" />
                                <div class="leading-tight min-w-0">
                                    <div class="text-lg font-medium text-slate-900 truncate">{{ $p->nama }}</div>
                                    <div class="text-sm text-slate-500 truncate">{{ $p->kota ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="my-4 h-px bg-slate-100"></div>

                            <div class="text-base text-slate-500 truncate">{{ $p->judul }}</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">{{ $p->range_harga ?: '—' }}</div>

                            {{-- BADGE: 1 baris + bisa digeser (drag/scroll) --}}
                            <div class="mt-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none" data-drag-scroll>
                                <div class="flex items-center gap-2 flex-nowrap min-w-max">
                                    @foreach($tags as $t)
                                        <span class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1 text-sm text-slate-600 whitespace-nowrap">
                                            {{ $t }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- CTA: langsung chat pembuat postingan; kalau belum login -> login dulu --}}
                            @php
                                $chatUrl = $u ? route('chat.show', $u) : url()->current();
                            @endphp

                            @auth
                                @if($u && auth()->id() !== $u->id)
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
                                            body: JSON.stringify({ url: '{{ $u ? route('chat.show', $u) : url()->current() }}' })
                                        }).finally(() => $store.authModal.open('login'))
                                    ">
                                    Tawarkan Mobil Saya
                                </button>
                            @endguest
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $postings->links() }}
            </div>
        @endif

    </div>
</div>
@endsection