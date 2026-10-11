<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MotorController extends Controller
{
    /**
     * PAGE: Lihat semua motor (default: paling populer)
     * View: resources/views/rekomendasi-motor/index.blade.php
     */
    public function index(Request $request)
    {
        $q = Motor::query();

        // ===== SEARCH (samakan pola mobil) =====
        // gunakan query param "q"
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $q->where(function ($s) use ($keyword) {
                $s->where('judul_iklan_motor', 'like', "%{$keyword}%")
                    ->orWhere('merk_motor', 'like', "%{$keyword}%")
                    ->orWhere('model_motor', 'like', "%{$keyword}%")
                    ->orWhere('lokasi_motor', 'like', "%{$keyword}%");
            });
        }

        // ===== FILTER MULTI SELECT =====
        $lokasi = $this->arr($request, 'lokasi');
        if ($lokasi) $q->whereIn('lokasi_motor', $lokasi);

        $jenisKendaraan = $this->arr($request, 'jenis_kendaraan');
        if ($jenisKendaraan) $q->whereIn('jenis_kendaraan_motor', $jenisKendaraan);

        $transmisi = $this->arr($request, 'transmisi');
        if ($transmisi) $q->whereIn('transmisi_motor', $transmisi);

        $bahanBakar = $this->arr($request, 'bahan_bakar');
        if ($bahanBakar) $q->whereIn('jenis_bahan_bakar_motor', $bahanBakar);

        $merk = $this->arr($request, 'merk');
        if ($merk) $q->whereIn('merk_motor', $merk);

        $kondisi = $this->arr($request, 'kondisi');
        if ($kondisi) $q->whereIn('kondisi_motor', $kondisi);

        $warna = $this->arr($request, 'warna');
        if ($warna) $q->whereIn('warna_motor', $warna);

        // ===== RANGE / BUCKET FILTER (samakan gaya mobil) =====

        // Kapasitas mesin (opsional — hanya berlaku kalau UI kamu kirim key "kapasitas")
        $kapasitas = $this->arr($request, 'kapasitas');
        if ($kapasitas) {
            $q->where(function ($qq) use ($kapasitas) {
                foreach ($kapasitas as $r) {
                    if ($r === 'lt_100') {
                        $qq->orWhere('kapasitas_mesin_motor', '<', 100);
                    } elseif ($r === '100_150') {
                        $qq->orWhereBetween('kapasitas_mesin_motor', [100, 150]);
                    } elseif ($r === '150_250') {
                        $qq->orWhereBetween('kapasitas_mesin_motor', [151, 250]);
                    } elseif ($r === 'gt_250') {
                        $qq->orWhere('kapasitas_mesin_motor', '>', 250);
                    }
                }
            });
        }

        // Harga (bucket)
        $harga = $this->arr($request, 'harga');
        if ($harga) {
            $q->where(function ($qq) use ($harga) {
                foreach ($harga as $r) {
                    if ($r === 'lt_10') {
                        $qq->orWhere('harga_motor', '<', 10_000_000);
                    } elseif ($r === '10_20') {
                        $qq->orWhereBetween('harga_motor', [10_000_000, 20_000_000]);
                    } elseif ($r === '20_40') {
                        $qq->orWhereBetween('harga_motor', [20_000_001, 40_000_000]);
                    } elseif ($r === '40_80') {
                        $qq->orWhereBetween('harga_motor', [40_000_001, 80_000_000]);
                    } elseif ($r === 'gt_80') {
                        $qq->orWhere('harga_motor', '>', 80_000_000);
                    }
                }
            });

            // mirip mobil: kalau filter harga dipakai, urutkan harga asc
            $q->orderBy('harga_motor', 'asc');
        }

        // Jarak / KM (bucket)
        $jarak = $this->arr($request, 'jarak');
        if ($jarak) {
            $q->where(function ($qq) use ($jarak) {
                foreach ($jarak as $r) {
                    if ($r === 'lt_5k') {
                        $qq->orWhere('km_motor', '<', 5_000);
                    } elseif ($r === '5k_15k') {
                        $qq->orWhereBetween('km_motor', [5_000, 15_000]);
                    } elseif ($r === '15k_30k') {
                        $qq->orWhereBetween('km_motor', [15_001, 30_000]);
                    } elseif ($r === 'gt_30k') {
                        $qq->orWhere('km_motor', '>', 30_000);
                    }
                }
            });
        }

        // Tahun (bucket)
        $tahun = $this->arr($request, 'tahun');
        if ($tahun) {
            $q->where(function ($qq) use ($tahun) {
                foreach ($tahun as $r) {
                    if ($r === '2024_2026') {
                        $qq->orWhereBetween('tahun_motor', [2024, 2026]);
                    } elseif ($r === '2020_2023') {
                        $qq->orWhereBetween('tahun_motor', [2020, 2023]);
                    } elseif ($r === '2016_2019') {
                        $qq->orWhereBetween('tahun_motor', [2016, 2019]);
                    } elseif ($r === 'lt_2016') {
                        $qq->orWhere('tahun_motor', '<', 2016);
                    }
                }
            });
        }

        // ===== FITUR (LIKE contains) =====
        // Catatan: kolom fitur motor kamu string/text (bukan array JSON),
        // jadi kita cocokkan dengan LIKE "%opt%".
        $security = $this->arr($request, 'security');
        if ($security) {
            $q->where(function ($qq) use ($security) {
                foreach ($security as $opt) $qq->orWhere('security_motor', 'like', "%{$opt}%");
            });
        }

        $exterior = $this->arr($request, 'exterior');
        if ($exterior) {
            $q->where(function ($qq) use ($exterior) {
                foreach ($exterior as $opt) $qq->orWhere('exterior_motor', 'like', "%{$opt}%");
            });
        }

        $convenience = $this->arr($request, 'convenience');
        if ($convenience) {
            $q->where(function ($qq) use ($convenience) {
                foreach ($convenience as $opt) $qq->orWhere('convenience_motor', 'like', "%{$opt}%");
            });
        }

        $performance = $this->arr($request, 'performance');
        if ($performance) {
            $q->where(function ($qq) use ($performance) {
                foreach ($performance as $opt) $qq->orWhere('performance_motor', 'like', "%{$opt}%");
            });
        }

        // ===== DEFAULT SORTING =====
        // kalau belum ada orderBy dari harga filter, default motor = populer
        if (!$this->hasOrderBy($q)) {
            $q->orderByDesc('likes_motor')
              ->orderByDesc('created_at');
        }

        $motors = $q->paginate(9)->withQueryString();

        return view('rekomendasi-motor.index', [
            'title'    => 'Lihat Semua Motor',
            'pageMode' => 'motor',
            'motors'   => $motors,
            'filters'  => $this->getFilterOptions(),
        ]);
    }

    /**
     * PAGE: Detail motor
     * View: resources/views/motor/show.blade.php
     */
    public function show(Motor $motor)
    {
        // ✅ penting untuk nama reviewer login (r->user->name)
        $motor->load(['user', 'reviews.user']);

        // images: gambar utama + galery (json array)
        $images = [];
        if (!empty($motor->gambar_motor)) $images[] = $motor->gambar_motor;

        $gallery = $motor->galery_motor;
        if (is_string($gallery)) {
            $decoded = json_decode($gallery, true);
            if (json_last_error() === JSON_ERROR_NONE) $gallery = $decoded;
        }
        if (is_array($gallery)) {
            foreach ($gallery as $g) if (!empty($g)) $images[] = $g;
        }

        $reviews = $motor->reviews()->with('user')->latest()->get();

        $hasReviewed = false;
        if (Auth::check()) {
            $hasReviewed = $motor->reviews()
                ->where('user_id', Auth::id())
                ->exists();
        }

        // untuk section "Motor Populer dan Terbaru" di show
        $populerMotors = Motor::query()
            ->where('id', '!=', $motor->id)
            ->orderByDesc('likes_motor')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('motor.show', compact(
            'motor',
            'images',
            'reviews',
            'hasReviewed',
            'populerMotors'
        ));
    }

    /**
     * PAGE: Jual motor (form)
     */
    public function create()
    {
        $user = Auth::user();

        // sesuaikan list sesuai kebutuhanmu
        $merks = ['Honda','Yamaha','Suzuki','Kawasaki','Vespa','KTM','Benelli','Royal Enfield','BMW','Harley-Davidson'];
        $transmisiList = ['Automatic','Manual'];
        $bahanBakarList = ['Bensin','Elektrik'];

        // sesuai kolom DB: jenis_kendaraan_motor (pilihan kategori)
        $tipeKendaraan = ['Sport','Off Road','Cafe Racer','Cruicer','Street','Moped','Scooter'];

        return view('motor.create', compact('user','merks','transmisiList','bahanBakarList','tipeKendaraan'));
    }

    /**
     * ACTION: Store motor baru sesuai kolom DB kamu
     */
    public function store(Request $request)
    {
        // kalau route kamu pakai middleware auth, ini aman
        // kalau user belum login => akan redirect login (itu yang kamu lihat modal login)
        $user = Auth::user();

        $validated = $request->validate([
            // FOTO (sesuaikan dengan form)
            'gambar_motor' => ['required','image','max:3072'],
            'galery_motor' => ['nullable','array'],
            'galery_motor.*' => ['nullable','image','max:3072'],

            // DETAIL ITEM (ini yang kamu kirim pada STEP 2)
            'merk_motor' => ['required','string','max:255'],
            'tahun_motor' => ['nullable','integer','min:1950','max:'.(date('Y')+1)],
            'model_motor' => ['required','string','max:255'],

            'jenis_kendaraan_motor' => ['required','string','max:255'],
            'transmisi_motor' => ['required','string','max:255'],
            'jenis_bahan_bakar_motor' => ['required','string','max:255'],

            'kapasitas_mesin_motor' => ['nullable','integer','min:0'],
            'km_motor' => ['nullable','integer','min:0'],
            'konsumsi_bbm_motor' => ['nullable','numeric','min:0'],
            'warna_motor' => ['nullable','string','max:255'],

            'tipe_penjual_motor' => ['required','string','max:255'],
            'nama_bursa_motor' => ['nullable','string','max:255','required_if:tipe_penjual_motor,Bursa Motor'],


            // KONDISI & LEGALITAS (sesuaikan jika memang ada di form kamu)
            'kondisi_motor' => ['nullable','string','max:255'],
            'status_pajak_motor' => ['nullable','string','max:255'],
            'kelengkapan_pajak_motor' => ['nullable','string','max:255'],
            'kelengkapan_surat_motor' => ['nullable','string','max:255'],
            'atas_nama_motor' => ['nullable','string','max:255'],
            'riwayat_servis_motor' => ['nullable','string','max:255'],

            // JUDUL/DESKRIPSI/HARGA (sesuaikan dengan form motor kamu)
            'judul_iklan_motor' => ['required','string','max:255'],
            'deskripsi_iklan_motor' => ['nullable','string'],
            'harga_motor' => ['required','numeric','min:0'],

            // KONTAK & PUBLIKASI (sesuai form motor)
            'lokasi_motor' => ['required','string','max:255'],
            'nama_penjual_motor' => ['nullable','string','max:255'],
            'email_penjual_motor' => ['nullable','string','max:255'],
            'no_hp_penjual_motor' => ['nullable','string','max:255'],

            // FITUR (checkbox array -> simpan JSON string di kolom text)
            'security_motor' => ['nullable','array'],
            'security_motor.*' => ['string','max:255'],
            'exterior_motor' => ['nullable','array'],
            'exterior_motor.*' => ['string','max:255'],
            'convenience_motor' => ['nullable','array'],
            'convenience_motor.*' => ['string','max:255'],
            'performance_motor' => ['nullable','array'],
            'performance_motor.*' => ['string','max:255'],
        ]);

        // kalau tipe penjual bukan Bursa Motor, kosongkan nama bursa
        if (($validated['tipe_penjual_motor'] ?? '') !== 'Bursa Motor') {
            $validated['nama_bursa_motor'] = null;
        }

        // Upload cover
        $coverPath = $request->file('gambar_motor')->store('motor', 'public');
        $validated['gambar_motor'] = 'storage/' . $coverPath;

        // Upload gallery max 4 -> simpan JSON string sesuai kolom DB (longtext)
        $gallery = [];
        if ($request->hasFile('galery_motor')) {
            $files = array_slice($request->file('galery_motor'), 0, 4);
            foreach ($files as $f) {
                if (!$f) continue;
                $p = $f->store('motor/gallery', 'public');
                $gallery[] = 'storage/' . $p;
            }
        }
        $validated['galery_motor'] = json_encode($gallery);

        // fitur -> JSON string (kolom text)
        $validated['security_motor'] = !empty($validated['security_motor']) ? json_encode($validated['security_motor']) : null;
        $validated['exterior_motor'] = !empty($validated['exterior_motor']) ? json_encode($validated['exterior_motor']) : null;
        $validated['convenience_motor'] = !empty($validated['convenience_motor']) ? json_encode($validated['convenience_motor']) : null;
        $validated['performance_motor'] = !empty($validated['performance_motor']) ? json_encode($validated['performance_motor']) : null;

        // set user_id + default likes
        $validated['user_id'] = $user->id;
        $validated['likes_motor'] = 0;

        // snapshot kontak kalau belum diisi (biasanya hidden input sudah isi)
        $validated['nama_penjual_motor'] = $validated['nama_penjual_motor'] ?? ($user->name ?? null);
        $validated['email_penjual_motor'] = $validated['email_penjual_motor'] ?? ($user->email ?? null);
        $validated['no_hp_penjual_motor'] = $validated['no_hp_penjual_motor'] ?? ($user->no_hp ?? null);

        Motor::create($validated);

        return redirect()->route('motor.index')->with('success', 'Iklan motor berhasil dibuat.');
    }


    // ======================
    // Helpers (samakan gaya mobil)
    // ======================

    private function arr(Request $request, string $key): array
    {
        $v = $request->input($key);

        if (is_array($v)) {
            return array_values(array_filter($v, fn($x) => $x !== null && $x !== ''));
        }

        if (is_string($v) && $v !== '') {
            return [$v];
        }

        return [];
    }

    private function getFilterOptions(): array
    {
        // Filter options statis (tidak tergantung DB) dan disamakan dengan form create motor.
        $merks = [
            'Honda','Yamaha','Suzuki','Kawasaki','Vespa','KTM','Benelli',
            'Royal Enfield','BMW','Harley-Davidson'
        ];

        // sesuai form create(): input = jenis_kendaraan_motor (kategori)
        $jenisKendaraan = ['Sport','Off Road','Cafe Racer','Cruiser','Street','Moped','Scooter'];

        $transmisiList  = ['Automatic','Manual'];
        $bahanBakarList = ['Bensin','Elektrik'];

        // fitur: samakan dengan create motor
        $securityOptions    = ['ABS','Alarm','Kunci Ganda','Keyless','Traction Control'];
        $exteriorOptions    = ['Lampu LED','Windshield','Box','Hand Guard','Footstep'];
        $convenienceOptions = ['USB Charger','Smart Key','Panel Digital','Handlebar Lock'];
        $performanceOptions = ['Quick Shifter','Slipper Clutch','Mode Berkendara'];

        return [
            // saat ini UI motor tidak pakai lokasi/warna, tetap disediakan agar kompatibel bila dibutuhkan
            'lokasi'          => [],
            'kondisi'         => ['Bekas','Baru'],
            'warna'           => [],

            // ini yang dipakai di filter motor
            'jenis_kendaraan' => $jenisKendaraan,
            'transmisi'       => $transmisiList,
            'bahan_bakar'     => $bahanBakarList,
            'merk'            => $merks,

            // fitur
            'security'        => $securityOptions,
            'exterior'        => $exteriorOptions,
            'convenience'     => $convenienceOptions,
            'performance'     => $performanceOptions,
        ];
    }


    /**
     * Deteksi apakah query builder sudah punya orderBy.
     * Ini dipakai supaya default sorting (populer) tidak menimpa sorting harga.
     */
    private function hasOrderBy($builder): bool
    {
        try {
            $orders = $builder->getQuery()->orders ?? null;
            return is_array($orders) && count($orders) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
