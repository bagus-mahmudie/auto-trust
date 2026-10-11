@extends('layouts.main')

@section('content')
@php
  $userName = $user->name ?? '';
  $userEmail = $user->email ?? '';
  $userPhone = $user->no_hp ?? '';

  $inputBase = 'w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-0';
  $inputOk   = 'border-slate-200 bg-slate-50 focus:border-slate-300';
  $inputErr  = 'border-red-500 bg-red-50 focus:border-red-500';
  $errText   = 'mt-1 text-xs text-red-600';
@endphp

<div class="bg-[#f6f8fb]">
  <div class="mx-auto max-w-[1200px] px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- LEFT STEPPER (clickable & active) --}}
    <aside class="lg:col-span-3">
    <div
        class="sticky top-24 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100"
        x-data="{ activeStep: 1 }"
    >
        @php
        $steps = [
            ['no'=>1,'label'=>'Unggah foto iklan','href'=>'#step-foto'],
            ['no'=>2,'label'=>'Detail item','href'=>'#step-detail'],
            ['no'=>3,'label'=>'Kondisi & Legalitas','href'=>'#step-legal'],
            ['no'=>6,'label'=>'Kontak & publikasi','href'=>'#step-kontak'],
        ];
        @endphp

        <div class="relative">
        {{-- garis vertikal --}}
        <div class="pointer-events-none absolute left-[18px] top-[18px] bottom-[18px] w-px bg-slate-200"></div>

        <div class="space-y-6">
            @foreach($steps as $s)
            <a
                href="{{ $s['href'] }}"
                @click="activeStep = {{ $s['no'] }}"
                class="group flex items-center gap-4"
            >
                {{-- circle --}}
                <div
                class="relative z-10 flex h-9 w-9 items-center justify-center rounded-full transition"
                :class="activeStep === {{ $s['no'] }}
                    ? 'bg-[#51A3EE] text-white'
                    : 'bg-slate-100 text-sky-600'"
                >
                <span class="text-sm font-semibold">{{ $s['no'] }}</span>
                </div>

                {{-- label --}}
                <div
                class="text-base leading-snug transition"
                :class="activeStep === {{ $s['no'] }}
                    ? 'text-sky-500 font-medium'
                    : 'text-slate-500'"
                >
                {{ $s['label'] }}
                </div>
            </a>
            @endforeach
        </div>
        </div>
    </div>
    </aside>


      {{-- MAIN FORM --}}
      <section class="lg:col-span-9">
        <form
          action="{{ route('mobil.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6"
          x-data="{
            max: 4,
            coverUrl: '',
            galleryUrls: [],
            galleryCount: 0,
            galleryFiles: [],
            sellerType: '{{ old('tipe_penjual', 'Pribadi') }}',

            pickCover(e){
                const f = e.target.files?.[0];
                this.coverUrl = f ? URL.createObjectURL(f) : '';
            },

            pickGallery(e){
                const selected = Array.from(e.target.files || []);

                // reset yang lama
                this.galleryUrls.forEach(u => URL.revokeObjectURL(u));
                this.galleryUrls = [];
                this.galleryFiles = [];

                // ambil max 4 file
                const limited = selected.slice(0, this.max);
                this.galleryFiles = limited;
                this.galleryUrls = limited.map(f => URL.createObjectURL(f));
                this.galleryCount = this.galleryFiles.length;

                // set ulang FileList
                const dt = new DataTransfer();
                this.galleryFiles.forEach(f => dt.items.add(f));
                e.target.files = dt.files;
            }
          }"
        >
          @csrf
          <div class=" rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <x-switch-jual-kendaraan active="mobil" />
          </div>

          {{-- STEP 1: FOTO --}}
          <div id="step-foto" class=" rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Unggah foto iklan</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">

              {{-- Foto Sampul --}}
              <label class="group cursor-pointer">
                <input type="file" name="gambar_mobil" accept="image/*" class="hidden" @change="pickCover($event)" required>
                <div class="h-[92px] rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-center overflow-hidden">
                  <template x-if="!coverUrl">
                    <div class="text-center text-slate-600">
                      <i class="bi bi-camera text-xl"></i>
                      <div class="mt-1 text-sm">Foto Sampul</div>
                    </div>
                  </template>
                  <template x-if="coverUrl">
                    <img :src="coverUrl" class="h-full w-full object-cover" alt="cover preview">
                  </template>
                </div>
              </label>

              {{-- Tambah Foto (Gallery) --}}
              <label class="cursor-pointer">
                <input
                    type="file"
                    name="galery_mobil[]"
                    accept="image/*"
                    class="hidden"
                    multiple
                    @change="pickGallery($event)"
                >
                <div class="h-[92px] rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-center">
                  <div class="text-center text-slate-600">
                    <div class="text-xl leading-none">+</div>
                    <div class="mt-1 text-sm" x-text="`Tambah Foto (${galleryCount}/${max})`"></div>
                  </div>
                </div>
              </label>

              {{-- Preview gallery --}}
              <div class="h-[92px] rounded-2xl border border-dashed border-slate-300 bg-white overflow-hidden">
                <template x-if="galleryUrls.length === 0">
                  <div class="h-full w-full flex items-center justify-center text-sm text-slate-400">
                    Preview gallery
                  </div>
                </template>

                <template x-if="galleryUrls.length > 0">
                  <div class="h-full px-3 flex items-center overflow-x-auto no-scrollbar">
                    <div class="flex items-center gap-2 flex-nowrap min-w-max">
                      <template x-for="(u,idx) in galleryUrls" :key="idx">
                        <img :src="u" class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200" alt="g">
                      </template>
                    </div>
                  </div>
                </template>
              </div>

            </div>

            @error('gambar_mobil') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
            @error('galery_mobil.*') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
          </div>


          {{-- STEP 2: DETAIL ITEM --}}
          <div id="step-detail" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Detail item</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

              {{-- Merek --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Merek</div>
                <select name="merk_mobil"
                  class="{{ $inputBase }} {{ $errors->has('merk_mobil') ? $inputErr : $inputOk }}"
                  required>
                  @foreach($merks as $m)
                    <option value="{{ $m }}" @selected(old('merk_mobil')===$m)>{{ $m }}</option>
                  @endforeach
                </select>
                @error('merk_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tahun --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Tahun</div>
                <input type="number" name="tahun" value="{{ old('tahun') }}"
                  class="{{ $inputBase }} {{ $errors->has('tahun') ? $inputErr : $inputOk }}"
                  placeholder="2018" required>
                @error('tahun') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Model --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Model</div>
                <input name="model_mobil" value="{{ old('model_mobil') }}"
                  class="{{ $inputBase }} {{ $errors->has('model_mobil') ? $inputErr : $inputOk }}"
                  placeholder="Avanza / Brio / Pajero..." required>
                @error('model_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Jenis Mobil (statis + required + error) --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Jenis Mobil</div>
                <select name="jenis_mobil"
                        class="{{ $inputBase }} {{ $errors->has('jenis_mobil') ? $inputErr : $inputOk }}"
                        required>
                  @foreach($jenisMobilOptions as $opt)
                    <option value="{{ $opt }}" @selected(old('jenis_mobil')===$opt)>{{ $opt }}</option>
                  @endforeach
                </select>
                @error('jenis_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tempat Duduk (statis + required + error) --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Tempat Duduk</div>
                <select name="tempat_duduk_mobil"
                        class="{{ $inputBase }} {{ $errors->has('tempat_duduk_mobil') ? $inputErr : $inputOk }}"
                        required>
                  @foreach($tempatDudukOptions as $seat)
                    <option value="{{ $seat }}" @selected((string)old('tempat_duduk_mobil')===(string)$seat)>{{ $seat }}</option>
                  @endforeach
                </select>
                @error('tempat_duduk_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Konsumsi BBM --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Konsumsi BBM (km/l)</div>
                <input type="number" step="0.01" name="konsumsi_bbm_mobil" value="{{ old('konsumsi_bbm_mobil') }}"
                    class="{{ $inputBase }} {{ $errors->has('konsumsi_bbm_mobil') ? $inputErr : $inputOk }}"
                    placeholder="12.50" min="0" required>
                @error('konsumsi_bbm_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tipe Bodi --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Tipe Bodi</div>
                <select name="jenis_kendaraan"
                  class="{{ $inputBase }} {{ $errors->has('jenis_kendaraan') ? $inputErr : $inputOk }}"
                  required>
                  @foreach($tipeBodi as $t)
                    <option value="{{ $t }}" @selected(old('jenis_kendaraan')===$t)>{{ $t }}</option>
                  @endforeach
                </select>
                @error('jenis_kendaraan') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Transmisi --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Transmisi</div>
                <select name="transmisi_mobil"
                  class="{{ $inputBase }} {{ $errors->has('transmisi_mobil') ? $inputErr : $inputOk }}"
                  required>
                  @foreach($transmisiList as $t)
                    <option value="{{ $t }}" @selected(old('transmisi_mobil')===$t)>{{ $t }}</option>
                  @endforeach
                </select>
                @error('transmisi_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tipe Bahan Bakar --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Tipe Bahan Bakar</div>
                <select name="jenis_bahan_bakar_mobil"
                  class="{{ $inputBase }} {{ $errors->has('jenis_bahan_bakar_mobil') ? $inputErr : $inputOk }}"
                  required>
                  @foreach($bahanBakarList as $b)
                    <option value="{{ $b }}" @selected(old('jenis_bahan_bakar_mobil')===$b)>{{ $b }}</option>
                  @endforeach
                </select>
                @error('jenis_bahan_bakar_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Kapasitas Mesin --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Kapasitas Mesin</div>
                <input name="kapasitas_mesin_mobil" value="{{ old('kapasitas_mesin_mobil') }}"
                  class="{{ $inputBase }} {{ $errors->has('kapasitas_mesin_mobil') ? $inputErr : $inputOk }}"
                  placeholder="1200 cc" required>
                @error('kapasitas_mesin_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Jarak Tempuh --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Jarak Tempuh</div>
                <input type="number" name="km_mobil" value="{{ old('km_mobil') }}"
                  class="{{ $inputBase }} {{ $errors->has('km_mobil') ? $inputErr : $inputOk }}"
                  placeholder="25000" required>
                @error('km_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Warna --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Warna</div>
                <input name="warna_mobil" value="{{ old('warna_mobil') }}"
                  class="{{ $inputBase }} {{ $errors->has('warna_mobil') ? $inputErr : $inputOk }}"
                  placeholder="Hitam" required>
                @error('warna_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tipe Penjual (samakan create motor) --}}
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Tipe Penjual</div>
                <select name="tipe_penjual"
                        x-model="sellerType"
                        class="{{ $inputBase }} {{ $errors->has('tipe_penjual') ? $inputErr : $inputOk }}"
                        required>
                  <option value="Pribadi" @selected(old('tipe_penjual')==='Pribadi')>Pribadi</option>
                  <option value="Bursa Mobil" @selected(old('tipe_penjual')==='Bursa Mobil')>Bursa Mobil</option>
                </select>
                @error('tipe_penjual') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Nama Bursa Mobil (muncul hanya jika Bursa Mobil) --}}
              <div x-show="sellerType === 'Bursa Mobil'" x-transition x-cloak>
                <div class="text-xs font-semibold text-slate-700 mb-2">Nama Bursa Mobil</div>
                <input name="nama_bursa_mobil"
                      value="{{ old('nama_bursa_mobil') }}"
                      :required="sellerType === 'Bursa Mobil'"
                      class="{{ $inputBase }} {{ $errors->has('nama_bursa_mobil') ? $inputErr : $inputOk }}"
                      placeholder="Nama bursa mobil...">
                @error('nama_bursa_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

            </div>

            {{-- FITUR --}}
            <div class="mt-5">
              @php
                $securityOptions = ['ABS','Bantuan Rem','EBD','Kontrol Stabilitas Kendaraan','Airbags','Monitor Tekanan Ban','Bantuan Hill Start'];
                $exteriorOptions = ['Sunroof','Moonroof','Headlamp Otomatis','Wiper Penginderaan Hujan','High Ground Clearance'];
                $convenienceOptions = ['Cruise Control','Paddle Shifters','Pengaturan Setir','Pembersih Udara','Kontrol Iklim Otomatis','Tekan Tombol Mulai','Ventilasi Ac belakang','Entri Tanpa Kunci'];
                $performanceOptions = ['Turbo Charger','AWD','4WD'];

                $selectedSecurity = (array) old('security_mobil', []);
                $selectedExterior = (array) old('exterior_mobil', []);
                $selectedConvenience = (array) old('convenience_mobil', []);
                $selectedPerformance = (array) old('performance_mobil', []);
              @endphp

              <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                  <div class="text-xs font-semibold text-slate-700 mb-3">Security</div>
                  <div class="space-y-2">
                    @foreach($securityOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="security_mobil[]" value="{{ $opt }}"
                          class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                          @checked(in_array($opt, $selectedSecurity, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>

                <div>
                  <div class="text-xs font-semibold text-slate-700 mb-3">Exterior</div>
                  <div class="space-y-2">
                    @foreach($exteriorOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="exterior_mobil[]" value="{{ $opt }}"
                          class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                          @checked(in_array($opt, $selectedExterior, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>

                <div>
                  <div class="text-xs font-semibold text-slate-700 mb-3">Convenience</div>
                  <div class="space-y-2">
                    @foreach($convenienceOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="convenience_mobil[]" value="{{ $opt }}"
                          class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                          @checked(in_array($opt, $selectedConvenience, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>

                <div>
                  <div class="text-xs font-semibold text-slate-700 mb-3">Performance</div>
                  <div class="space-y-2">
                    @foreach($performanceOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="performance_mobil[]" value="{{ $opt }}"
                          class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                          @checked(in_array($opt, $selectedPerformance, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                </div>
              </div>

              @error('security_mobil.*') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
              @error('exterior_mobil.*') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
              @error('convenience_mobil.*') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
              @error('performance_mobil.*') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            @if($errors->any())
              <div class="mt-4 text-sm text-red-600">Ada input yang belum valid. Silakan cek lagi.</div>
            @endif
          </div>

          {{-- STEP 3: KONDISI & LEGALITAS --}}
          <div id="step-legal" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Kondisi & Legalitas</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Kondisi Mobil</div>
                <select name="kondisi_mobil"
                  class="{{ $inputBase }} {{ $errors->has('kondisi_mobil') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Bekas" @selected(old('kondisi_mobil')==='Bekas')>Bekas</option>
                  <option value="Baru" @selected(old('kondisi_mobil')==='Baru')>Baru</option>
                </select>
                @error('kondisi_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Status pajak</div>
                <select name="status_pajak"
                  class="{{ $inputBase }} {{ $errors->has('status_pajak') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Aktif" @selected(old('status_pajak')==='Aktif')>Aktif</option>
                  <option value="Tidak Aktif" @selected(old('status_pajak')==='Tidak Aktif')>Tidak Aktif</option>
                </select>
                @error('status_pajak') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Kelengkapan Pajak</div>
                <select name="kelengkapan_pajak"
                  class="{{ $inputBase }} {{ $errors->has('kelengkapan_pajak') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="STNK lengkap" @selected(old('kelengkapan_pajak')==='STNK lengkap')>STNK lengkap</option>
                  <option value="Pajak tahunan lengkap" @selected(old('kelengkapan_pajak')==='Pajak tahunan lengkap')>Pajak tahunan lengkap</option>
                  <option value="STNK lengkap, Pajak tahunan lengkap" @selected(old('kelengkapan_pajak')==='STNK lengkap, Pajak tahunan lengkap')>STNK lengkap, Pajak tahunan lengkap</option>
                </select>
                @error('kelengkapan_pajak') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Kelengkapan Surat</div>
                <select name="kelengkapan_surat"
                  class="{{ $inputBase }} {{ $errors->has('kelengkapan_surat') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="STNK" @selected(old('kelengkapan_surat')==='STNK')>STNK</option>
                  <option value="STNK, BPKB" @selected(old('kelengkapan_surat')==='STNK, BPKB')>STNK, BPKB</option>
                  <option value="STNK, BPKB, Faktur" @selected(old('kelengkapan_surat')==='STNK, BPKB, Faktur')>STNK, BPKB, Faktur</option>
                </select>
                @error('kelengkapan_surat') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Atas Nama</div>
                <select name="atas_nama"
                  class="{{ $inputBase }} {{ $errors->has('atas_nama') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Pribadi" @selected(old('atas_nama')==='Pribadi')>Pribadi</option>
                  <option value="Perusahaan" @selected(old('atas_nama')==='Perusahaan')>Perusahaan</option>
                </select>
                @error('atas_nama') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Riwayat Servis</div>
                <select name="riwayat_servis"
                  class="{{ $inputBase }} {{ $errors->has('riwayat_servis') ? $inputErr : $inputOk }}"
                  required>
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Servis rutin di bengkel resmi" @selected(old('riwayat_servis')==='Servis rutin di bengkel resmi')>Servis rutin di bengkel resmi</option>
                  <option value="Servis rutin non resmi" @selected(old('riwayat_servis')==='Servis rutin non resmi')>Servis rutin non resmi</option>
                  <option value="Tidak ada riwayat" @selected(old('riwayat_servis')==='Tidak ada riwayat')>Tidak ada riwayat</option>
                </select>
                @error('riwayat_servis') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- JUDUL & DESKRIPSI --}}
          <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Judul dan Deskripsi Iklan</div>

            <div class="mt-4">
              <div class="text-xs font-semibold text-slate-700 mb-2">Judul iklan</div>
              <input name="judul_iklan" value="{{ old('judul_iklan', 'Jual Mobil') }}"
                class="{{ $inputBase }} {{ $errors->has('judul_iklan') ? $inputErr : $inputOk }}"
                required>
              @error('judul_iklan') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
              <div class="text-xs font-semibold text-slate-700 mb-2">Deskripsi</div>
              <textarea name="deskripsi_iklan" rows="6"
                class="{{ $inputBase }} {{ $errors->has('deskripsi_iklan') ? $inputErr : $inputOk }}"
                placeholder="Ceritakan kondisi mobil..." required>{{ old('deskripsi_iklan') }}</textarea>
              @error('deskripsi_iklan') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4 max-w-sm">
              <div class="text-xs font-semibold text-slate-700 mb-2">Tentukan harga</div>
              <input type="number" name="harga_mobil" value="{{ old('harga_mobil') }}"
                class="{{ $inputBase }} {{ $errors->has('harga_mobil') ? $inputErr : $inputOk }}"
                placeholder="Rp. 1.000.000.000" required>
              @error('harga_mobil') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>
          </div>

          {{-- STEP 6: KONTAK & PUBLIKASI --}}
          <div id="step-kontak" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Informasi kontak & publikasi</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Nama penjual</div>
                <input value="{{ $userName }}" disabled
                  class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-500">
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Nomor</div>
                <input value="{{ $userPhone }}" disabled
                  class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-500">
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Email</div>
                <input value="{{ $userEmail }}" disabled
                  class="w-full rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-500">
              </div>

              <div>
                <div class="text-xs font-semibold text-slate-700 mb-2">Lokasi</div>
                <input name="lokasi" value="{{ old('lokasi') }}"
                  class="{{ $inputBase }} {{ $errors->has('lokasi') ? $inputErr : $inputOk }}"
                  placeholder="Jakarta, Indonesia" required>
                @error('lokasi') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- hidden snapshot --}}
            <input type="hidden" name="nama_penjual" value="{{ $userName }}">
            <input type="hidden" name="email_penjual" value="{{ $userEmail }}">
            <input type="hidden" name="no_hp_penjual" value="{{ $userPhone }}">
          </div>

          <div class="flex justify-center">
            <button type="submit"
              class="w-full max-w-md rounded-full bg-[#51A3EE] py-3 text-white font-semibold hover:bg-sky-600 transition">
              Submit
            </button>
          </div>

        </form>
      </section>

    </div>
  </div>
</div>

@endsection
