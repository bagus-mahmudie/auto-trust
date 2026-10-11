@extends('layouts.main')

@section('content')
@php
  $userName  = $user->name ?? '';
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

      {{-- LEFT STEPPER --}}
      <aside class="lg:col-span-3">
        <div class="sticky top-24 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100"
             x-data="{ activeStep: 1 }">
          @php
            $steps = [
              ['no'=>1,'label'=>'Unggah foto iklan','href'=>'#step-foto'],
              ['no'=>2,'label'=>'Detail item','href'=>'#step-detail'],
              ['no'=>3,'label'=>'Kondisi & Legalitas','href'=>'#step-legal'],
              ['no'=>4,'label'=>'Kontak & publikasi','href'=>'#step-kontak'],
            ];
          @endphp

          <div class="relative">
            <div class="pointer-events-none absolute left-[18px] top-[18px] bottom-[18px] w-px bg-slate-200"></div>

            <div class="space-y-6">
              @foreach($steps as $s)
                <a href="{{ $s['href'] }}"
                   @click="activeStep = {{ $s['no'] }}"
                   class="group flex items-center gap-4">
                  <div class="relative z-10 flex h-9 w-9 items-center justify-center rounded-full transition"
                       :class="activeStep === {{ $s['no'] }} ? 'bg-[#51A3EE] text-white' : 'bg-slate-100 text-sky-600'">
                    <span class="text-sm font-semibold">{{ $s['no'] }}</span>
                  </div>

                  <div class="text-base leading-snug transition"
                       :class="activeStep === {{ $s['no'] }} ? 'text-sky-500 font-medium' : 'text-slate-500'">
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
        <form action="{{ route('motor.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6"
              x-data="{
                max: 4,
                coverUrl: '',
                galleryUrls: [],
                galleryCount: 0,
                sellerType: '{{ old('tipe_penjual_motor', 'Pribadi') }}',
                pickCover(e){
                  const f = e.target.files?.[0];
                  this.coverUrl = f ? URL.createObjectURL(f) : '';
                },
                pickGallery(e){
                  const selected = Array.from(e.target.files || []);

                  this.galleryUrls.forEach(u => URL.revokeObjectURL(u));
                  this.galleryUrls = [];

                  const limited = selected.slice(0, this.max);
                  this.galleryUrls = limited.map(f => URL.createObjectURL(f));
                  this.galleryCount = limited.length;

                  const dt = new DataTransfer();
                  limited.forEach(f => dt.items.add(f));
                  e.target.files = dt.files;
                }
              }">
          @csrf

          <div class=" rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <x-switch-jual-kendaraan active="motor" />
          </div>

          {{-- STEP 1: FOTO --}}
          <div id="step-foto" class=" rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Unggah foto iklan</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 items-stretch">
            
            {{-- Foto Sampul --}}
            <label class="group cursor-pointer relative">
                <input type="file"
                    name="gambar_motor"
                    accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    @change="pickCover($event)"
                    required>
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
            <label class="cursor-pointer relative">
                <input type="file"
                    name="galery_motor[]"
                    accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    multiple
                    @change="pickGallery($event)">
                <div class="h-[92px] rounded-2xl border border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-center">
                <div class="text-center text-slate-600">
                    <div class="text-xl leading-none">+</div>
                    <div class="mt-1 text-sm" x-text="`Tambah Foto (${galleryCount}/${max})`"></div>
                </div>
                </div>
            </label>

            {{-- Preview gallery (sejajar di kolom 3) --}}
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

            {{-- Error messages (taruh di bawah grid, full width) --}}
            <div class="mt-2">
            @error('gambar_motor')
                <div class="text-xs text-red-600">{{ $message }}</div>
            @enderror
            @error('galery_motor.*')
                <div class="text-xs text-red-600">{{ $message }}</div>
            @enderror
            </div>

          </div>

          {{-- STEP 2: DETAIL ITEM --}}
          <div id="step-detail" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Detail item</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

              {{-- Merek --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Merek</div>
                <select name="merk_motor" required
                        class="{{ $inputBase }} {{ $errors->has('merk_motor') ? $inputErr : $inputOk }}">
                  @foreach($merks as $m)
                    <option value="{{ $m }}" @selected(old('merk_motor')===$m)>{{ $m }}</option>
                  @endforeach
                </select>
                @error('merk_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tahun --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Tahun</div>
                <input type="number" name="tahun_motor" required value="{{ old('tahun_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('tahun_motor') ? $inputErr : $inputOk }}"
                       placeholder="2018">
                @error('tahun_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Model --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Model</div>
                <input name="model_motor" required value="{{ old('model_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('model_motor') ? $inputErr : $inputOk }}"
                       placeholder="NMAX / Vario / CBR...">
                @error('model_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Tipe Motor --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Tipe Motor</div>
                <select name="jenis_kendaraan_motor" required
                        class="{{ $inputBase }} {{ $errors->has('jenis_kendaraan_motor') ? $inputErr : $inputOk }}">
                  @foreach($tipeKendaraan as $t)
                    <option value="{{ $t }}" @selected(old('jenis_kendaraan_motor')===$t)>{{ $t }}</option>
                  @endforeach
                </select>
                @error('jenis_kendaraan_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Transmisi --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Transmisi</div>
                <select name="transmisi_motor" required
                        class="{{ $inputBase }} {{ $errors->has('transmisi_motor') ? $inputErr : $inputOk }}">
                  @foreach($transmisiList as $t)
                    <option value="{{ $t }}" @selected(old('transmisi_motor')===$t)>{{ $t }}</option>
                  @endforeach
                </select>
                @error('transmisi_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Bahan Bakar --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Tipe Bahan Bakar</div>
                <select name="jenis_bahan_bakar_motor" required
                        class="{{ $inputBase }} {{ $errors->has('jenis_bahan_bakar_motor') ? $inputErr : $inputOk }}">
                  @foreach($bahanBakarList as $b)
                    <option value="{{ $b }}" @selected(old('jenis_bahan_bakar_motor')===$b)>{{ $b }}</option>
                  @endforeach
                </select>
                @error('jenis_bahan_bakar_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Kapasitas Mesin --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Kapasitas Mesin (cc)</div>
                <input type="number" name="kapasitas_mesin_motor" required value="{{ old('kapasitas_mesin_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('kapasitas_mesin_motor') ? $inputErr : $inputOk }}"
                       placeholder="155">
                @error('kapasitas_mesin_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Jarak Tempuh --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Jarak Tempuh (KM)</div>
                <input type="number" name="km_motor" required value="{{ old('km_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('km_motor') ? $inputErr : $inputOk }}"
                       placeholder="25000">
                @error('km_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Konsumsi BBM --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Konsumsi BBM (km/l)</div>
                <input type="number" step="0.01" name="konsumsi_bbm_motor" required value="{{ old('konsumsi_bbm_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('konsumsi_bbm_motor') ? $inputErr : $inputOk }}"
                       placeholder="35.00" min="0">
                @error('konsumsi_bbm_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              {{-- Warna --}}
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Warna</div>
                <input name="warna_motor" required value="{{ old('warna_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('warna_motor') ? $inputErr : $inputOk }}"
                       placeholder="Hitam">
                @error('warna_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

                {{-- Tipe Penjual --}}
                <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Tipe Penjual</div>
                <select name="tipe_penjual_motor"
                        x-model="sellerType"
                        class="{{ $inputBase }} {{ $errors->has('tipe_penjual_motor') ? $inputErr : $inputOk }}"
                        required>
                    <option value="Pribadi" @selected(old('tipe_penjual_motor')==='Pribadi')>Pribadi</option>
                    <option value="Bursa Motor" @selected(old('tipe_penjual_motor')==='Bursa Motor')>Bursa Motor</option>
                </select>
                @error('tipe_penjual_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>

                {{-- Nama Bursa Motor (muncul hanya jika Bursa Motor) --}}
                <div x-show="sellerType === 'Bursa Motor'"
                    x-transition
                    x-cloak>
                <div class="text-sm font-semibold text-slate-700 mb-2">Nama Bursa Motor</div>
                <input name="nama_bursa_motor"
                        value="{{ old('nama_bursa_motor') }}"
                        :required="sellerType === 'Bursa Motor'"
                        class="{{ $inputBase }} {{ $errors->has('nama_bursa_motor') ? $inputErr : $inputOk }}"
                        placeholder="Nama bursa motor...">
                @error('nama_bursa_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- FITUR --}}
            <div class="mt-5">
              @php
                $securityOptions     = ['ABS','Alarm','Kunci Ganda','Keyless','Traction Control'];
                $exteriorOptions     = ['Lampu LED','Windshield','Box','Hand Guard','Footstep'];
                $convenienceOptions  = ['USB Charger','Smart Key','Panel Digital','Handlebar Lock'];
                $performanceOptions  = ['Quick Shifter','Slipper Clutch','Mode Berkendara'];

                $selectedSecurity     = (array) old('security_motor', []);
                $selectedExterior     = (array) old('exterior_motor', []);
                $selectedConvenience  = (array) old('convenience_motor', []);
                $selectedPerformance  = (array) old('performance_motor', []);
              @endphp

              <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                  <div class="text-sm font-semibold text-slate-700 mb-3">Security</div>
                  <div class="space-y-2">
                    @foreach($securityOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="security_motor[]" value="{{ $opt }}"
                               class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                               @checked(in_array($opt, $selectedSecurity, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                  @error('security_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                  @error('security_motor.*') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>

                <div>
                  <div class="text-sm font-semibold text-slate-700 mb-3">Exterior</div>
                  <div class="space-y-2">
                    @foreach($exteriorOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="exterior_motor[]" value="{{ $opt }}"
                               class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                               @checked(in_array($opt, $selectedExterior, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                  @error('exterior_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                  @error('exterior_motor.*') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>

                <div>
                  <div class="text-sm font-semibold text-slate-700 mb-3">Convenience</div>
                  <div class="space-y-2">
                    @foreach($convenienceOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="convenience_motor[]" value="{{ $opt }}"
                               class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                               @checked(in_array($opt, $selectedConvenience, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                  @error('convenience_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                  @error('convenience_motor.*') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>

                <div>
                  <div class="text-sm font-semibold text-slate-700 mb-3">Performance</div>
                  <div class="space-y-2">
                    @foreach($performanceOptions as $opt)
                      <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="performance_motor[]" value="{{ $opt }}"
                               class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-0"
                               @checked(in_array($opt, $selectedPerformance, true))>
                        <span>{{ $opt }}</span>
                      </label>
                    @endforeach
                  </div>
                  @error('performance_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                  @error('performance_motor.*') <div class="{{ $errText }}">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>
          </div>

          {{-- STEP 3: KONDISI & LEGALITAS --}}
          <div id="step-legal" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Kondisi & Legalitas</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Kondisi Motor</div>
                <select name="kondisi_motor" required
                        class="{{ $inputBase }} {{ $errors->has('kondisi_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Bekas" @selected(old('kondisi_motor')==='Bekas')>Bekas</option>
                  <option value="Baru" @selected(old('kondisi_motor')==='Baru')>Baru</option>
                </select>
                @error('kondisi_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Status pajak</div>
                <select name="status_pajak_motor" required
                        class="{{ $inputBase }} {{ $errors->has('status_pajak_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Aktif" @selected(old('status_pajak_motor')==='Aktif')>Aktif</option>
                  <option value="Tidak Aktif" @selected(old('status_pajak_motor')==='Tidak Aktif')>Tidak Aktif</option>
                </select>
                @error('status_pajak_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Kelengkapan Pajak</div>
                <select name="kelengkapan_pajak_motor" required
                        class="{{ $inputBase }} {{ $errors->has('kelengkapan_pajak_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="STNK lengkap" @selected(old('kelengkapan_pajak_motor')==='STNK lengkap')>STNK lengkap</option>
                  <option value="Pajak tahunan lengkap" @selected(old('kelengkapan_pajak_motor')==='Pajak tahunan lengkap')>Pajak tahunan lengkap</option>
                  <option value="STNK lengkap, Pajak tahunan lengkap" @selected(old('kelengkapan_pajak_motor')==='STNK lengkap, Pajak tahunan lengkap')>STNK lengkap, Pajak tahunan lengkap</option>
                </select>
                @error('kelengkapan_pajak_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Kelengkapan Surat</div>
                <select name="kelengkapan_surat_motor" required
                        class="{{ $inputBase }} {{ $errors->has('kelengkapan_surat_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="STNK" @selected(old('kelengkapan_surat_motor')==='STNK')>STNK</option>
                  <option value="STNK, BPKB" @selected(old('kelengkapan_surat_motor')==='STNK, BPKB')>STNK, BPKB</option>
                  <option value="STNK, BPKB, Faktur" @selected(old('kelengkapan_surat_motor')==='STNK, BPKB, Faktur')>STNK, BPKB, Faktur</option>
                </select>
                @error('kelengkapan_surat_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Atas Nama</div>
                <select name="atas_nama_motor" required
                        class="{{ $inputBase }} {{ $errors->has('atas_nama_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Pribadi" @selected(old('atas_nama_motor')==='Pribadi')>Pribadi</option>
                  <option value="Perusahaan" @selected(old('atas_nama_motor')==='Perusahaan')>Perusahaan</option>
                </select>
                @error('atas_nama_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Riwayat Servis</div>
                <select name="riwayat_servis_motor" required
                        class="{{ $inputBase }} {{ $errors->has('riwayat_servis_motor') ? $inputErr : $inputOk }}">
                  <option value="">- Pilih Salah Satu -</option>
                  <option value="Servis rutin di bengkel resmi" @selected(old('riwayat_servis_motor')==='Servis rutin di bengkel resmi')>Servis rutin di bengkel resmi</option>
                  <option value="Servis rutin non resmi" @selected(old('riwayat_servis_motor')==='Servis rutin non resmi')>Servis rutin non resmi</option>
                  <option value="Tidak ada riwayat" @selected(old('riwayat_servis_motor')==='Tidak ada riwayat')>Tidak ada riwayat</option>
                </select>
                @error('riwayat_servis_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- JUDUL & DESKRIPSI --}}
          <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Judul dan Deskripsi Iklan</div>

            <div class="mt-4">
              <div class="text-sm font-semibold text-slate-700 mb-2">Judul iklan</div>
              <input name="judul_iklan_motor" required value="{{ old('judul_iklan_motor', 'Jual Motor') }}"
                     class="{{ $inputBase }} {{ $errors->has('judul_iklan_motor') ? $inputErr : $inputOk }}">
              @error('judul_iklan_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
              <div class="text-sm font-semibold text-slate-700 mb-2">Deskripsi</div>
              <textarea name="deskripsi_iklan_motor" required rows="6"
                        class="{{ $inputBase }} {{ $errors->has('deskripsi_iklan_motor') ? $inputErr : $inputOk }}"
                        placeholder="Ceritakan kondisi motor...">{{ old('deskripsi_iklan_motor') }}</textarea>
              @error('deskripsi_iklan_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4 max-w-sm">
              <div class="text-sm font-semibold text-slate-700 mb-2">Tentukan harga</div>
              <input type="number" name="harga_motor" required value="{{ old('harga_motor') }}"
                     class="{{ $inputBase }} {{ $errors->has('harga_motor') ? $inputErr : $inputOk }}"
                     placeholder="Rp. 10.000.000">
              @error('harga_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
            </div>
          </div>

          {{-- KONTAK & PUBLIKASI --}}
          <div id="step-kontak" class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-6">
            <div class="text-base font-semibold text-slate-900">Informasi kontak & publikasi</div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Nama penjual</div>
                <input value="{{ $userName }}" disabled class="{{ $inputBase }} border-slate-200 bg-slate-100 text-slate-500">
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Nomor</div>
                <input value="{{ $userPhone }}" disabled class="{{ $inputBase }} border-slate-200 bg-slate-100 text-slate-500">
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Email</div>
                <input value="{{ $userEmail }}" disabled class="{{ $inputBase }} border-slate-200 bg-slate-100 text-slate-500">
              </div>

              <div>
                <div class="text-sm font-semibold text-slate-700 mb-2">Lokasi</div>
                <input name="lokasi_motor" required value="{{ old('lokasi_motor') }}"
                       class="{{ $inputBase }} {{ $errors->has('lokasi_motor') ? $inputErr : $inputOk }}"
                       placeholder="Jakarta, Indonesia">
                @error('lokasi_motor') <div class="{{ $errText }}">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- hidden snapshot --}}
            <input type="hidden" name="nama_penjual_motor" value="{{ $userName }}">
            <input type="hidden" name="email_penjual_motor" value="{{ $userEmail }}">
            <input type="hidden" name="no_hp_penjual_motor" value="{{ $userPhone }}">
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
