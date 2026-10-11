@extends('layouts.main')

@section('content')
<div class="bg-[#f6f8fb] min-h-[calc(50vh-50px)]">
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">

        @php
            $user = auth()->user();
            $avatar = $user->foto_profile
                ? asset('storage/'.$user->foto_profile)
                : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E2E8F0&color=0F172A';
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6" x-data="{ tab: 'profile' }">
            {{-- LEFT SIDEBAR --}}
            <div class="md:col-span-3">
                <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-5">
                    {{-- TAB SWITCHER --}}
                    <div class="space-y-1">
                        <button type="button"
                            class="w-full flex items-center gap-2 rounded-2xl px-3 py-2 text-base transition"
                            :class="tab==='profile' ? 'text-slate-900' : 'text-slate-600 hover:bg-slate-50'"
                            @click="tab='profile'">
                            <span class="inline-block h-5 w-1 rounded-full"
                                :class="tab==='profile' ? 'bg-sky-500' : 'bg-transparent'"></span>
                            <span class="font-semibold">Edit Profile</span>
                        </button>

                        <button type="button"
                            class="w-full flex items-center gap-2 rounded-2xl px-3 py-2 text-base transition"
                            :class="tab==='photo' ? 'text-slate-900' : 'text-slate-600 hover:bg-slate-50'"
                            @click="tab='photo'">
                            <span class="inline-block h-5 w-1 rounded-full"
                                :class="tab==='photo' ? 'bg-sky-500' : 'bg-transparent'"></span>
                            <span class="font-semibold">Gambar Profile</span>
                        </button>
                        
                    </div>

                    {{-- BUTTON LIHAT PROFIL (SELALU TAMPIL) --}}
                    <a href="{{ route('seller.me') }}"
                       class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-[#51A3EE] px-4 py-2.5 text-base font-medium text-white hover:bg-sky-600 transition">
                        Lihat Profil
                    </a>
                </div>
            </div>

            {{-- RIGHT CONTENT --}}
            <div class="md:col-span-9">

                {{-- TAB: profile --}}
                <div x-show="tab==='profile'" x-cloak>
                    <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6 sm:p-8">

                        @if (session('success'))
                            <div class="mb-4 rounded-2xl bg-emerald-50 text-emerald-700 px-4 py-3 text-base">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.update') }}" class="space-y-0">
                            @csrf

                            {{-- Informasi Kontak --}}
                            <div>
                                <div class="text-lg font-semibold text-slate-900 mb-3">Informasi Kontak</div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-base text-slate-600">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                               class="mt-1 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-base focus:border-slate-300 focus:ring-slate-200">
                                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="text-base text-slate-600">Nomor</label>
                                        <div class="mt-1 flex rounded-2xl bg-slate-50 border border-slate-200 overflow-hidden">
                                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                                   placeholder="+62xxx-xxxx-xxxx"
                                                   class="w-full bg-transparent border-0 px-4 py-3 text-base">
                                        </div>
                                        @error('no_hp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Informasi Dasar --}}
                            <div>
                                <div class="text-lg font-semibold text-slate-900 mt-5 mb-3">Informasi Dasar</div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Nama --}}
                                    <div>
                                        <label class="text-base text-slate-600">Nama</label>
                                        <input type="text"
                                            name="name"
                                            value="{{ old('name', $user->name) }}"
                                            required
                                            class="mt-1 w-full rounded-2xl bg-slate-50 border border-slate-200
                                                    px-4 py-3 text-base focus:border-slate-300 focus:ring-slate-200">
                                        @error('name')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Lokasi --}}
                                    <div>
                                        <label class="text-base text-slate-600">Lokasi</label>
                                        <input type="text"
                                            name="lokasi"
                                            value="{{ old('lokasi', $user->lokasi) }}"
                                            placeholder="Masukkan Lokasi Anda"
                                            class="mt-1 w-full rounded-2xl bg-slate-50 border border-slate-200
                                                    px-4 py-3 text-base focus:border-slate-300 focus:ring-slate-200">
                                        @error('lokasi')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                                    <div>
                                        <label class="text-base text-slate-600">Deskripsi</label>
                                        <textarea name="deskripsi_user" rows="6"
                                                  class="mt-1 w-full rounded-2xl bg-slate-50 border border-slate-200 px-4 py-3 text-base focus:border-slate-300 focus:ring-slate-200"
                                                  placeholder="Tulis deskripsi singkat...">{{ old('deskripsi_user', $user->deskripsi_user) }}</textarea>
                                        @error('deskripsi_user') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Button --}}
                            <div class="pt-2 flex justify-center">
                                <button type="submit"
                                        class="w-full sm:w-[340px] rounded-full bg-[#51A3EE] hover:bg-sky-600 text-white py-3 text-base font-medium transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                {{-- TAB: photo --}}
                <div x-show="tab==='photo'" x-cloak>
                    <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6 sm:p-8"
                         x-data="{ preview: '{{ $avatar }}' }">

                        @if (session('success'))
                            <div class="mb-4 rounded-2xl bg-emerald-50 text-emerald-700 px-4 py-3 text-base">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" x-ref="form">
                            @csrf
                            <input type="hidden" name="mode" value="photo">

                            <input
                                x-ref="file"
                                type="file"
                                name="foto_profile"
                                accept="image/*"
                                class="hidden"
                                @change="
                                    const file = $event.target.files[0];
                                    if (!file) return;
                                    preview = URL.createObjectURL(file);
                                    $nextTick(() => $refs.form.submit());
                                "
                            >

                            {{-- responsive: mobile/tablet stack, desktop side-by-side --}}
                            <div class="flex flex-col lg:flex-row items-stretch gap-6 lg:gap-8">

                                {{-- KOTAK BESAR (responsive size) --}}
                                <button type="button" class="shrink-0 text-left w-full lg:w-auto" @click="$refs.file.click()">
                                    <div class="mx-auto lg:mx-0
                                                h-[220px] w-full max-w-[260px]
                                                sm:h-[240px] sm:max-w-[280px]
                                                lg:h-[220px] lg:w-[220px] lg:max-w-none
                                                rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50
                                                overflow-hidden relative flex items-center justify-center">
                                        <img :src="preview" class="absolute inset-0 h-full w-full object-cover" alt="foto profil">

                                        {{-- overlay --}}
                                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white/70"
                                             :class="preview ? 'opacity-0 hover:opacity-100 transition' : 'opacity-100'">
                                            <div class="h-14 w-14 rounded-full bg-white shadow flex items-center justify-center text-slate-600">
                                                <i class="bi bi-camera text-2xl"></i>
                                            </div>
                                            <div class="text-base text-slate-600">Foto Profil</div>
                                        </div>
                                    </div>
                                </button>

                                {{-- KANAN --}}
                                <div class="flex-1 flex flex-col pt-0 lg:pt-2">
                                    <div class="text-xl sm:text-2xl font-semibold text-slate-900">Gambar profil</div>
                                    <div class="mt-3 h-px w-full bg-slate-200"></div>

                                    <p class="mt-5 mb-6 text-sm sm:text-base text-slate-600 leading-relaxed max-w-[680px]">
                                        Foto yang jelas penting bagi pembeli dan penjual agar dapat saling mengenali.
                                        Pastikan tidak menyertakan informasi pribadi atau sensitif apa pun yang tidak ingin Anda
                                        perlihatkan.
                                    </p>

                                    <button type="button"
                                            class="mt-auto w-full sm:w-[340px] max-w-full
                                                rounded-full bg-[#51A3EE] hover:bg-sky-600 text-white
                                                py-3 text-base font-semibold transition"
                                            @click="$refs.file.click()">
                                        Unggah
                                    </button>

                                    @error('foto_profile')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
