@extends('layouts.main')

@section('content')
<div class="bg-[#f6f8fb] min-h-[calc(100vh-112px)]">
    <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">

        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6 sm:p-8">
            <div class="text-base font-semibold text-slate-900 mb-6">
                Detail Posting
            </div>

            <form method="POST" action="{{ route('posting.kebutuhan.store') }}" class="space-y-5">
                @csrf

                {{-- Nama | Kota (readonly) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base text-slate-700 mb-2">Nama</label>
                        <input type="text"
                            value="{{ $user->name }}"
                            readonly
                            class="w-full rounded-xl bg-slate-200 border border-slate-200
                                    px-4 py-3 text-base text-slate-700
                                    pointer-events-none cursor-default
                                    focus:outline-none focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-base text-slate-700 mb-2">Kota</label>
                        <input type="text"
                            value="{{ $kotaUser }}"
                            readonly
                            class="w-full rounded-xl bg-slate-200 border border-slate-200
                                    px-4 py-3 text-base text-slate-700
                                    pointer-events-none cursor-default
                                    focus:outline-none focus:ring-0">
                    </div>
                </div>

                {{-- Judul Postingan --}}
                <div>
                    <label class="block text-base text-slate-700 mb-2">Judul Postingan</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Butuh Mobil/Motor" 
                           class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-base 
                                  focus:border-slate-300 focus:ring-slate-200">
                    @error('judul') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Merek | Tipe bodi --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base text-slate-700 mb-2">Merek</label>
                        <input
                            type="text"
                            name="merek"
                            value="{{ old('merek') }}"
                            placeholder="Contoh : Toyota / Honda / Suzuki"
                            class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-base
                            focus:border-slate-300 focus:ring-slate-200"
                        />
                    </div>

                    <div>
                        <label class="block text-base text-slate-700 mb-2">Tipe Body</label>
                        <input
                            type="text"
                            name="tipe_bodi"
                            value="{{ old('tipe_bodi') }}"
                            placeholder="Contoh : SUV / MPV / Sedan / Pickup"
                            class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-base
                            focus:border-slate-300 focus:ring-slate-200"
                        />
                    </div>
                </div>

                {{-- Transmisi | Range Harga --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-base text-slate-700 mb-2">Transmisi</label>
                        <input type="text" name="transmisi" value="{{ old('transmisi') }}" placeholder="Matic"
                               class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-base
                                      focus:border-slate-300 focus:ring-slate-200">
                    </div>

                    <div>
                        <label class="block text-base text-slate-700 mb-2">Range Harga</label>
                        <input type="text" name="range_harga" value="{{ old('range_harga') }}" placeholder="Rp150–200 juta"
                               class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-base
                                      focus:border-slate-300 focus:ring-slate-200">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full sm:w-[340px] rounded-full bg-[#51A3EE] hover:bg-sky-600 text-white
                                   py-3 text-base font-semibold transition">
                        Unggah
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
