<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $q = Mobil::query();

        // search
        if ($request->filled('q')) {
            $keyword = $request->q;
            $q->where(function ($s) use ($keyword) {
                $s->where('merk_mobil', 'like', "%{$keyword}%")
                  ->orWhere('model_mobil', 'like', "%{$keyword}%")
                  ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        $kondisi = $this->arr($request, 'kondisi');
        if ($kondisi) $q->whereIn('kondisi_mobil', $kondisi);

        $model = $this->arr($request, 'model');
        if ($model) $q->whereIn('model_mobil', $model);

        $lokasi = $this->arr($request, 'lokasi');
        if ($lokasi) $q->whereIn('lokasi', $lokasi);

        // filters (sesuaikan kolom)
        $jenisKendaraan = $this->arr($request, 'jenis_kendaraan');
        if ($jenisKendaraan) $q->whereIn('jenis_kendaraan', $jenisKendaraan);

        $jenisMobil = $this->arr($request, 'jenis_mobil');
        if ($jenisMobil) $q->whereIn('jenis_mobil', $jenisMobil);

        $transmisi = $this->arr($request, 'transmisi');
        if ($transmisi) $q->whereIn('transmisi_mobil', $transmisi);

        $bahanBakar = $this->arr($request, 'bahan_bakar');
        if ($bahanBakar) $q->whereIn('jenis_bahan_bakar_mobil', $bahanBakar);

        $merk = $this->arr($request, 'merk');
        if ($merk) $q->whereIn('merk_mobil', $merk);

        $tempatDuduk = $this->arr($request, 'tempat_duduk');
        if ($tempatDuduk) $q->whereIn('tempat_duduk_mobil', $tempatDuduk);

        $kapasitas = $this->arr($request, 'kapasitas');
        if ($kapasitas) {
            $q->where(function ($qq) use ($kapasitas) {
                foreach ($kapasitas as $r) {
                    if ($r === 'lt_1000') {
                        $qq->orWhere('kapasitas_mesin_mobil', '<', 1000);
                    } elseif ($r === '1000_2000') {
                        $qq->orWhereBetween('kapasitas_mesin_mobil', [1000, 2000]);
                    } elseif ($r === '2000_3000') {
                        $qq->orWhereBetween('kapasitas_mesin_mobil', [2001, 3000]);
                    } elseif ($r === 'gt_3000') {
                        $qq->orWhere('kapasitas_mesin_mobil', '>', 3000);
                    }
                }
            });
        }

        $konsumsi = $this->arr($request, 'konsumsi');
        if ($konsumsi) {

            $intPart = "CAST(SUBSTRING_INDEX(konsumsi_bbm_mobil, '.', 1) AS UNSIGNED)";
            $fracPart = "CAST(SUBSTRING_INDEX(konsumsi_bbm_mobil, '.', -1) AS UNSIGNED)";
            $iritScore = "($fracPart / NULLIF($intPart, 0))";

            $needRange = array_intersect($konsumsi, ['3_10', '10_15']);
            if (!empty($needRange)) {
                $q->where(function ($qq) use ($konsumsi, $intPart) {
                    if (in_array('3_10', $konsumsi, true)) {
                        $qq->orWhereRaw("$intPart BETWEEN ? AND ?", [3, 10]);
                    }
                    if (in_array('10_15', $konsumsi, true)) {
                        $qq->orWhereRaw("$intPart BETWEEN ? AND ?", [11, 15]);
                    }
                });
            }

            if (in_array('irit', $konsumsi, true)) {
                $q->orderByRaw("$iritScore DESC");
            }
        }

        $harga = $this->arr($request, 'harga');
        if ($harga) {
            $q->where(function ($qq) use ($harga) {
                foreach ($harga as $r) {
                    if ($r === 'lt_150') {
                        $qq->orWhere('harga_mobil', '<', 150_000_000);
                    } elseif ($r === 'lt_200') {
                        $qq->orWhere('harga_mobil', '<', 200_000_000);
                    } elseif ($r === '200_400') {
                        $qq->orWhereBetween('harga_mobil', [200_000_000, 400_000_000]);
                    } elseif ($r === '400_800') {
                        $qq->orWhereBetween('harga_mobil', [400_000_001, 800_000_000]);
                    } elseif ($r === '800_1200') {
                        $qq->orWhereBetween('harga_mobil', [800_000_001, 1_200_000_000]);
                    }
                }
            });
        }

        $jarak = $this->arr($request, 'jarak');
        if ($jarak) {
            $q->where(function ($qq) use ($jarak) {
                foreach ($jarak as $r) {
                    if ($r === 'lt_300') {
                        $qq->orWhere('km_mobil', '<', 300);
                    } elseif ($r === '300_500') {
                        $qq->orWhereBetween('km_mobil', [300, 500]);
                    } elseif ($r === 'gt_500') {
                        $qq->orWhere('km_mobil', '>', 500);
                    }
                }
            });
        }

        $security = $this->arr($request, 'security');
        if ($security) {
            $q->where(function ($qq) use ($security) {
                foreach ($security as $opt) {
                    $qq->orWhere('security_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $exterior = $this->arr($request, 'exterior');
        if ($exterior) {
            $q->where(function ($qq) use ($exterior) {
                foreach ($exterior as $opt) {
                    $qq->orWhere('exterior_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $convenience = $this->arr($request, 'convenience');
        if ($convenience) {
            $q->where(function ($qq) use ($convenience) {
                foreach ($convenience as $opt) {
                    $qq->orWhere('convenience_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $performance = $this->arr($request, 'performance');
        if ($performance) {
            $q->where(function ($qq) use ($performance) {
                foreach ($performance as $opt) {
                    $qq->orWhere('performance_mobil', 'like', "%{$opt}%");
                }
            });
        }

        // SORTING FINAL
        $harga = $this->arr($request, 'harga');
        if ($harga) {
            $q->orderBy('harga_mobil', 'asc');
        } else {
            $q->orderByDesc('id');
        }

        $mobils = $q->paginate(9)->withQueryString();

        return view('rekomendasi-mobil.index', [
            'title'   => 'Rekomendasi Mobil',
            'pageMode'=> 'rekomendasi',
            'mobils'  => $mobils,
            'filters' => $this->getFilterOptions(),
        ]);
    }

    public function popular(Request $request)
    {
        $q = Mobil::query();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $q->where(function ($s) use ($keyword) {
                $s->where('merk_mobil', 'like', "%{$keyword}%")
                  ->orWhere('model_mobil', 'like', "%{$keyword}%")
                  ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        $kondisi = $this->arr($request, 'kondisi');
        if ($kondisi) $q->whereIn('kondisi_mobil', $kondisi);

        $model = $this->arr($request, 'model');
        if ($model) $q->whereIn('model_mobil', $model);

        $lokasi = $this->arr($request, 'lokasi');
        if ($lokasi) $q->whereIn('lokasi', $lokasi);

        $jenisKendaraan = $this->arr($request, 'jenis_kendaraan');
        if ($jenisKendaraan) $q->whereIn('jenis_kendaraan', $jenisKendaraan);

        $jenisMobil = $this->arr($request, 'jenis_mobil');
        if ($jenisMobil) $q->whereIn('jenis_mobil', $jenisMobil);

        $transmisi = $this->arr($request, 'transmisi');
        if ($transmisi) $q->whereIn('transmisi_mobil', $transmisi);

        $bahanBakar = $this->arr($request, 'bahan_bakar');
        if ($bahanBakar) $q->whereIn('jenis_bahan_bakar_mobil', $bahanBakar);

        $merk = $this->arr($request, 'merk');
        if ($merk) $q->whereIn('merk_mobil', $merk);

        $tempatDuduk = $this->arr($request, 'tempat_duduk');
        if ($tempatDuduk) $q->whereIn('tempat_duduk_mobil', $tempatDuduk);

        $kapasitas = $this->arr($request, 'kapasitas');
        if ($kapasitas) {
            $q->where(function ($qq) use ($kapasitas) {
                foreach ($kapasitas as $r) {
                    if ($r === 'lt_1000') {
                        $qq->orWhere('kapasitas_mesin_mobil', '<', 1000);
                    } elseif ($r === '1000_2000') {
                        $qq->orWhereBetween('kapasitas_mesin_mobil', [1000, 2000]);
                    } elseif ($r === '2000_3000') {
                        $qq->orWhereBetween('kapasitas_mesin_mobil', [2001, 3000]);
                    } elseif ($r === 'gt_3000') {
                        $qq->orWhere('kapasitas_mesin_mobil', '>', 3000);
                    }
                }
            });
        }

        $konsumsi = $this->arr($request, 'konsumsi');
        if ($konsumsi) {
            $intPart  = "CAST(SUBSTRING_INDEX(konsumsi_bbm_mobil, '.', 1) AS UNSIGNED)";
            $fracPart = "CAST(SUBSTRING_INDEX(konsumsi_bbm_mobil, '.', -1) AS UNSIGNED)";
            $iritScore = "($fracPart / NULLIF($intPart, 0))";

            $needRange = array_intersect($konsumsi, ['3_10', '10_15']);
            if (!empty($needRange)) {
                $q->where(function ($qq) use ($konsumsi, $intPart) {
                    if (in_array('3_10', $konsumsi, true)) {
                        $qq->orWhereRaw("$intPart BETWEEN ? AND ?", [3, 10]);
                    }
                    if (in_array('10_15', $konsumsi, true)) {
                        $qq->orWhereRaw("$intPart BETWEEN ? AND ?", [11, 15]);
                    }
                });
            }

            if (in_array('irit', $konsumsi, true)) {
                $q->orderByRaw("$iritScore DESC");
            }
        }

        $harga = $this->arr($request, 'harga');
        if ($harga) {
            $q->where(function ($qq) use ($harga) {
                foreach ($harga as $r) {
                    if ($r === 'lt_150') {
                        $qq->orWhere('harga_mobil', '<', 150_000_000);
                    } elseif ($r === 'lt_200') {
                        $qq->orWhere('harga_mobil', '<', 200_000_000);
                    } elseif ($r === '200_400') {
                        $qq->orWhereBetween('harga_mobil', [200_000_000, 400_000_000]);
                    } elseif ($r === '400_800') {
                        $qq->orWhereBetween('harga_mobil', [400_000_001, 800_000_000]);
                    } elseif ($r === '800_1200') {
                        $qq->orWhereBetween('harga_mobil', [800_000_001, 1_200_000_000]);
                    }
                }
            });
        }

        $jarak = $this->arr($request, 'jarak');
        if ($jarak) {
            $q->where(function ($qq) use ($jarak) {
                foreach ($jarak as $r) {
                    if ($r === 'lt_300') {
                        $qq->orWhere('km_mobil', '<', 300);
                    } elseif ($r === '300_500') {
                        $qq->orWhereBetween('km_mobil', [300, 500]);
                    } elseif ($r === 'gt_500') {
                        $qq->orWhere('km_mobil', '>', 500);
                    }
                }
            });
        }

        $security = $this->arr($request, 'security');
        if ($security) {
            $q->where(function ($qq) use ($security) {
                foreach ($security as $opt) {
                    $qq->orWhere('security_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $exterior = $this->arr($request, 'exterior');
        if ($exterior) {
            $q->where(function ($qq) use ($exterior) {
                foreach ($exterior as $opt) {
                    $qq->orWhere('exterior_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $convenience = $this->arr($request, 'convenience');
        if ($convenience) {
            $q->where(function ($qq) use ($convenience) {
                foreach ($convenience as $opt) {
                    $qq->orWhere('convenience_mobil', 'like', "%{$opt}%");
                }
            });
        }

        $performance = $this->arr($request, 'performance');
        if ($performance) {
            $q->where(function ($qq) use ($performance) {
                foreach ($performance as $opt) {
                    $qq->orWhere('performance_mobil', 'like', "%{$opt}%");
                }
            });
        }

        // SORTING FINAL
        $harga = $this->arr($request, 'harga');
        if ($harga) {
            $q->orderBy('harga_mobil', 'asc');
        } else {
            $q->orderByDesc('likes_mobil')
              ->orderByDesc('id');
        }

        $mobils = $q->paginate(9)->withQueryString();

        return view('rekomendasi-mobil.index', [
            'title'    => 'Mobil Populer',
            'pageMode' => 'populer',
            'mobils'   => $mobils,
            'filters'  => $this->getFilterOptions(),
        ]);
    }

    /**
     * ============================
     * JUAL MOBIL (ADD)
     * ============================
     */

    public function create()
    {
        $user = Auth::user();

        // list select (statis)
        $merks = ['Toyota','Honda','Suzuki','Daihatsu','Hyundai','Nissan','Mitsubishi','Wuling','Mazda','BMW','Mercedes'];
        $transmisiList = ['Automatic','Manual','CVT','Dual Clutch'];
        $bahanBakarList = ['Bensin','Diesel','Elektrik','Hybrid','PHEV'];
        $tipeBodi = ['MPV','SUV','Crossover','Sedan','Hatchback','Coupe','Wagon','Van','Pickup Truck','Convertible','Sportback','Minivans'];

        // ✅ dropdown statis (samakan konsep dengan create motor)
        $jenisMobilOptions = [
            'Komersial',
            'City Car',
            'keluarga',
            'LCGC',
            'Mewah',
            'Off Road',
            'Sports Car',
        ];

        $tempatDudukOptions = [2, 4, 5, 6, 7, 8, 9];

        return view('mobil.create', compact(
            'user',
            'merks',
            'transmisiList',
            'bahanBakarList',
            'tipeBodi',
            'jenisMobilOptions',
            'tempatDudukOptions'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // ===== Validasi =====
        $validated = $request->validate([
            // Foto
            'gambar_mobil' => ['required','image','max:3072'],
            'galery_mobil' => ['nullable','array'],
            'galery_mobil.*' => ['nullable','image','max:3072'],

            // Detail item (sesuai tabel + tambahan)
            'merk_mobil' => ['required','string','max:255'],
            'model_mobil' => ['required','string','max:255'],
            'tahun' => ['required','integer','min:1980','max:'.(date('Y')+1)],
            'jenis_kendaraan' => ['required','string','max:255'], // tipe bodi
            'transmisi_mobil' => ['required','string','max:255'],
            'jenis_bahan_bakar_mobil' => ['required','string','max:255'],
            'kapasitas_mesin_mobil' => ['nullable','numeric','min:0'],
            'km_mobil' => ['nullable','integer','min:0'],
            'warna_mobil' => ['nullable','string','max:255'],
            'jenis_mobil' => ['required','string','max:255'],
            'tempat_duduk_mobil' => ['required','integer','min:1','max:20'],
            'kondisi_mobil' => ['nullable','string','max:255'],

            'tipe_penjual' => ['required','in:Pribadi,Bursa Mobil'],
            'nama_bursa_mobil' => ['nullable','string','max:255','required_if:tipe_penjual,Bursa Mobil'],

            'konsumsi_bbm_mobil' => ['nullable','numeric','min:0'],

            // Kondisi & Legalitas
            'status_pajak' => ['nullable','string','max:255'],
            'kelengkapan_pajak' => ['nullable','string','max:255'],
            'kelengkapan_surat' => ['nullable','string','max:255'],
            'atas_nama' => ['nullable','string','max:255'],
            'riwayat_servis' => ['nullable','string','max:255'],

            // Judul & Deskripsi
            'judul_iklan' => ['required','string','max:255'],
            'deskripsi_iklan' => ['nullable','string'],

            // Harga + lokasi
            'harga_mobil' => ['required','numeric','min:0'],
            'lokasi' => ['required','string','max:255'],

            // fitur (opsional)
            'security_mobil' => ['nullable','array'],
            'security_mobil.*' => ['string'],
            'exterior_mobil' => ['nullable','array'],
            'exterior_mobil.*' => ['string'],
            'convenience_mobil' => ['nullable','array'],
            'convenience_mobil.*' => ['string'],
            'performance_mobil' => ['nullable','array'],
            'performance_mobil.*' => ['string'],
        ]);

        // ===== Upload cover =====
        $coverPath = $request->file('gambar_mobil')->store('mobil', 'public');
        $validated['gambar_mobil'] = 'storage/' . $coverPath;

        // ===== Upload gallery (maks 4) =====
        $gallery = [];
        if ($request->hasFile('galery_mobil')) {
            $files = array_slice($request->file('galery_mobil'), 0, 4);
            foreach ($files as $f) {
                if (!$f) continue;
                $p = $f->store('mobil/gallery', 'public');
                $gallery[] = 'storage/' . $p;
            }
        }

        // galery_mobil di DB kamu longtext → simpan JSON array
        $validated['galery_mobil'] = $gallery;

        // ===== Fitur (text) → simpan JSON juga biar aman =====
        $validated['security_mobil'] = $validated['security_mobil'] ?? null;
        $validated['exterior_mobil'] = $validated['exterior_mobil'] ?? null;
        $validated['convenience_mobil'] = $validated['convenience_mobil'] ?? null;
        $validated['performance_mobil'] = $validated['performance_mobil'] ?? null;

        // ===== Auto user =====
        $validated['user_id'] = $user->id;
        $validated['nama_penjual'] = $user->name;
        $validated['email_penjual'] = $user->email;
        $validated['no_hp_penjual'] = $user->no_hp ?? null;

        // default likes
        $validated['likes_mobil'] = 0;

        // kalau bukan bursa mobil, kosongkan nama bursa
        if (($validated['tipe_penjual'] ?? null) !== 'Bursa Mobil') {
            $validated['nama_bursa_mobil'] = null;
        }

        $mobil = Mobil::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Iklan berhasil dibuat.');
    }

    private function getFilterOptions(): array
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER OPTIONS STATIS
        |--------------------------------------------------------------------------
        | Disamakan dengan form create mobil.
        | Tidak lagi mengambil distinct dari database.
        | Jadi option tidak berubah-ubah tergantung isi DB.
        |--------------------------------------------------------------------------
        */

        // === dari create() ===
        $merks = [
            'Toyota','Honda','Suzuki','Daihatsu','Hyundai','Nissan','Mitsubishi',
            'Wuling','Mazda','BMW','Mercedes'
        ];

        $transmisiList = ['Automatic','Manual','CVT','Dual Clutch'];

        $bahanBakarList = ['Bensin','Diesel','Elektrik','Hybrid','PHEV'];

        $tipeBodi = [
            'MPV','SUV','Crossover','Sedan','Hatchback','Coupe',
            'Wagon','Van','Pickup Truck','Convertible','Sportback','Minivans'
        ];

        $jenisMobilOptions = [
            'Komersial','City Car','Keluarga','LCGC','Mewah','Off Road',
            'Sports Car'
        ];

        $tempatDudukOptions = [2, 4, 5, 6, 7, 8, 9];

        // === dari create.blade.php (checkbox fitur) ===
        $securityOptions = [
            'ABS','Bantuan Rem','EBD','Kontrol Stabilitas Kendaraan',
            'Airbags','Monitor Tekanan Ban','Bantuan Hill Start'
        ];

        $exteriorOptions = [
            'Sunroof','Moonroof','Headlamp Otomatis',
            'Wiper Penginderaan Hujan','High Ground Clearance'
        ];

        $convenienceOptions = [
            'Cruise Control','Paddle Shifters','Pengaturan Setir',
            'Pembersih Udara','Kontrol Iklim Otomatis',
            'Tekan Tombol Mulai','Ventilasi Ac belakang','Entri Tanpa Kunci'
        ];

        $performanceOptions = ['Turbo Charger','AWD','4WD'];

        return [
            // dropdown sesuai create mobil
            'jenis_kendaraan' => $tipeBodi,
            'jenis_mobil'     => $jenisMobilOptions,
            'transmisi'       => $transmisiList,
            'bahan_bakar'     => $bahanBakarList,
            'merks'           => $merks,
            'seats'           => $tempatDudukOptions,

            // numeric input di create → kita kosongkan agar tidak tergantung DB
            'kapasitas'       => [],
            'konsumsi'        => [],
            'harga'           => [],
            'jarak'           => [],

            // fitur checkbox
            'security'        => $securityOptions,
            'exterior'        => $exteriorOptions,
            'convenience'     => $convenienceOptions,
            'performance'     => $performanceOptions,
        ];
    }


    private function arr($request, string $key): array
    {
        $v = $request->input($key);

        if (is_array($v)) return array_values(array_filter($v, fn($x) => $x !== null && $x !== ''));
        if (is_string($v) && $v !== '') return [$v];

        return [];
    }

    public function show(Mobil $mobil)
    {
        $mobil->load(['user', 'reviews.user']);

        // galery_mobil bisa string JSON atau array
        $gallery = $mobil->galery_mobil;

        if (is_string($gallery)) {
            $decoded = json_decode($gallery, true);
            $gallery = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($gallery)) $gallery = [];

        // cover
        $cover = $mobil->gambar_mobil ?: null;

        // images: cover + gallery
        $images = collect([$cover])
            ->merge($gallery)
            ->filter()
            ->values();

        // rekomendasi (samakan dengan homepage)
        $rekomendasis = Mobil::query()
            ->where('id', '!=', $mobil->id)
            ->orderByDesc('id')
            ->take(3) // ✅ saran: 9 biar bagus di mobile/tablet/desktop
            ->get();

        // views dummy
        $views = 22;

        // reviews
        $reviews = $mobil->reviews()
            ->with('user')
            ->latest()
            ->get();

        $hasReviewed = auth()->check()
            ? $mobil->reviews()->where('user_id', auth()->id())->exists()
            : false;

        return view('mobil.show', compact(
            'mobil',
            'images',
            'rekomendasis',
            'views',
            'reviews',
            'hasReviewed'
        ));
    }


}
