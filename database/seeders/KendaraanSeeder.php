<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada minimal 1 user (id=1) supaya FK aman
        $this->ensureUserExists();

        // Bersihkan dulu biar kalau seed ulang tidak numpuk (opsional)
        // Kalau kamu tidak mau truncate, komen saja bagian ini
        $this->truncateTables([
            'mobil_likes', 'motor_likes',
            'reviews', 'motor_reviews',
            'messages',
            'mobil_sewa', 'motor_sewa',
            'mobil', 'motor',
        ]);

        $this->seedMobilJual(20);
        $this->seedMotorJual(20);
        $this->seedMobilSewa(20);
        $this->seedMotorSewa(20);
    }

    private function ensureUserExists(): void
    {
        $exists = DB::table('users')->where('id', 1)->exists();

        if (!$exists) {
            DB::table('users')->insert([
                'id' => 1,
                'name' => 'Seeder User',
                'email' => 'user-96ff1b9b@example.test',
                'password' => \Illuminate\Support\Facades\Hash::make(bin2hex(random_bytes(24))),
                'lokasi' => 'Jakarta',
                'no_hp' => '081234567890',
                'deskripsi_user' => 'Akun untuk kebutuhan seeding.',
                'foto_profile' => null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function truncateTables(array $tables): void
    {
        // Matikan FK check agar truncate aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * =========================
     * Helpers gambar dari storage
     * =========================
     * Kita ambil dari folder:
     * storage/app/public/mobil  untuk mobil
     * storage/app/public/motor  untuk motor
     *
     * Yang disimpan ke DB adalah path relatif untuk dipanggil:
     * asset('storage/'.$path)
     */
    private function pickRandomStorageImage(string $folder, string $fallback): string
    {
        $base = storage_path("app/public/{$folder}");

        if (!is_dir($base)) {
            return $fallback;
        }

        $files = glob($base . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);

        if (!$files || count($files) === 0) {
            return $fallback;
        }

        $random = $files[array_rand($files)];

        // return relative path: "mobil/xxx.png"
        return $folder . '/' . basename($random);
    }

    private function pickRandomGallery(string $folder, string $fallback, int $count = 3): string
    {
        $gallery = [];
        for ($i = 0; $i < $count; $i++) {
            $gallery[] = $this->pickRandomStorageImage($folder, $fallback);
        }
        // longtext + check json_valid -> wajib valid JSON
        return json_encode($gallery, JSON_UNESCAPED_SLASHES);
    }

    private function rupiahRange(int $min, int $max): int
    {
        // random int range
        return random_int($min, $max);
    }

    private function randomFrom(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    /**
     * =========================
     * MOBIL DATA GENERATOR
     * =========================
     */
    private array $jenisKendaraanMobilList = [
        'MPV', 'SUV', 'Crossover',
        'Sedan', 'Hatchback', 'Coupe',
        'Wagon', 'Van', 'Pickup Truck',
        'Convertible', 'Sportback', 'Minivans',
    ];

    private array $jenisMobilList = [
        'Komersial',
        'City Car',
        'Keluarga',
        'LCGC',
        'Mewah',
        'Off Road',
        'Sports Car',
    ];

    private function makeMobilRow(int $userId, bool $isSewa = false): array
    {
        
        $merkModels = [
            'Toyota' => ['Avanza', 'Rush', 'Innova', 'Calya', 'Raize'],
            'Honda' => ['Brio', 'HR-V', 'BR-V', 'Mobilio', 'City'],
            'Suzuki' => ['Ertiga', 'XL7', 'Baleno', 'Ignis'],
            'Mitsubishi' => ['Xpander', 'Pajero Sport', 'Xforce'],
            'Hyundai' => ['Creta', 'Stargazer', 'Ioniq 5'],
            'Daihatsu' => ['Xenia', 'Sigra', 'Terios', 'Ayla'],
        ];

        $merk = $this->randomFrom(array_keys($merkModels));
        $model = $this->randomFrom($merkModels[$merk]);

        $jenisKendaraan = $this->randomFrom($this->jenisKendaraanMobilList);
        $jenisMobil = $this->randomFrom($this->jenisMobilList);
        $transmisi = $this->randomFrom(['Manual', 'Automatic']);
        $bahanBakar = $this->randomFrom(['Bensin', 'Diesel', 'Hybrid']);
        $kondisi = $this->randomFrom(['Baru', 'Bekas']);
        $warna = $this->randomFrom(['Putih', 'Hitam', 'Silver', 'Merah', 'Biru', 'Abu-abu']);
        $lokasi = $this->randomFrom(['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Bali', 'Medan']);
        $tahun = random_int(2016, 2024);

        $kursi = $this->randomFrom([4, 5, 6, 7, 8]);
        $cc = $this->randomFrom([1000, 1200, 1500, 1800, 2000, 2400]);
        $bbm = random_int(9, 18) + (random_int(0, 99) / 100); // 9.00 - 18.99
        $km = $kondisi === 'Baru' ? random_int(0, 500) : random_int(5000, 120000);

        // Harga sewa vs jual beda range
        $harga = $isSewa
            ? $this->rupiahRange(200000, 1200000)
            : $this->rupiahRange(80000000, 600000000);

        $judulIklan = "{$merk} {$model} {$tahun} - {$jenisMobil}";
        $desc = "Kondisi {$kondisi}, {$transmisi}, {$bahanBakar}. Lokasi {$lokasi}. Siap pakai, nyaman, dan terawat.";

        $fallback = 'mobil/default.png';
        $gambar = $this->pickRandomStorageImage('mobil', $fallback);
        $galery = $this->pickRandomGallery('mobil', $fallback, 3);

        // slug: di mobil jual TIDAK unique di DB, tapi tetap boleh unik nilainya biar aman
        $slug = Str::slug("{$merk}-{$model}-{$tahun}-" . Str::random(6));

        return [
            'slug' => $slug,
            'user_id' => $userId,

            'gambar_mobil' => $gambar,
            'galery_mobil' => $galery,

            'lokasi' => $lokasi,
            'jenis_kendaraan' => $jenisKendaraan,
            'jenis_mobil'     => $jenisMobil,
            'transmisi_mobil' => $transmisi,
            'jenis_bahan_bakar_mobil' => $bahanBakar,
            'merk_mobil' => $merk,
            'model_mobil' => $model,
            'kondisi_mobil' => $kondisi,

            'status_pajak' => $this->randomFrom(['Hidup', 'Mati', 'Tidak Ada']),
            'kelengkapan_pajak' => $this->randomFrom(['Lengkap', 'Tidak Lengkap']),
            'kelengkapan_surat' => $this->randomFrom(['Lengkap', 'Tidak Lengkap']),
            'atas_nama' => $this->randomFrom(['Pribadi', 'Perusahaan']),
            'riwayat_servis' => $this->randomFrom(['Rutin', 'Tidak Ada', 'Kadang']),

            'judul_iklan' => $judulIklan,
            'deskripsi_iklan' => $desc,

            'nama_penjual' => $this->randomFrom(['AutoTrust', 'Showroom Jaya', 'Bursa Mobil', 'Pemilik Langsung']),
            'email_penjual' => $this->randomFrom(['user-336a6767@example.test', 'user-50003d47@example.test', 'user-e1d37357@example.test']),
            'no_hp_penjual' => $this->randomFrom(['081234567890', '081298765432', '082112223333']),

            'likes_mobil' => random_int(0, 250),

            'tahun' => $tahun,
            'tempat_duduk_mobil' => $kursi,
            'kapasitas_mesin_mobil' => $cc,
            'konsumsi_bbm_mobil' => $bbm,
            'harga_mobil' => $harga,
            'km_mobil' => $km,

            'warna_mobil' => $warna,
            'tipe_penjual' => $this->randomFrom(['Dealer', 'Individu']),
            'nama_bursa_mobil' => $this->randomFrom(['AutoTrust Bursa', 'Bursa Mobil Online', 'Bursa Lokal']),

            'security_mobil' => $this->randomFrom(['Airbag, ABS', 'ABS, EBD', 'Airbag, Parking Sensor']),
            'exterior_mobil' => $this->randomFrom(['Velg Alloy', 'Body Kit', 'Fog Lamp']),
            'convenience_mobil' => $this->randomFrom(['AC, Power Steering', 'Head Unit, Bluetooth', 'Cruise Control']),
            'performance_mobil' => $this->randomFrom(['Mesin Halus', 'Tarikan Mantap', 'Irit BBM']),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * =========================
     * MOTOR DATA GENERATOR
     * =========================
     */
    private array $jenisKendaraanMotorList = [
        'Sport',
        'Off Road',
        'Cafe Racer',
        'Cruiser',
        'Street',
        'Moped',
        'Scooter',
    ];

    private array $merkMotorList = [
        'Honda',
        'Yamaha',
        'Suzuki',
        'Kawasaki',
        'Vespa',
        'KTM',
        'Benelli',
        'Royal Enfield',
        'BMW',
        'Harley-Davidson',
    ];

    private function makeMotorRow(int $userId, bool $isSewa = false): array
    {
        $merk = $this->randomFrom($this->merkMotorList);

        // Model disesuaikan dengan merk
        $modelByMerk = [
            'Honda' => ['Vario', 'Beat', 'PCX', 'CB150R'],
            'Yamaha' => ['Nmax', 'Aerox', 'R15', 'Mio'],
            'Suzuki' => ['Satria', 'GSX-R150', 'Nex'],
            'Kawasaki' => ['Ninja 250', 'KLX 150', 'W175'],
            'Vespa' => ['Sprint', 'Primavera', 'GTS'],
            'KTM' => ['RC 200', 'Duke 250'],
            'Benelli' => ['TRK 251', 'Leoncino'],
            'Royal Enfield' => ['Classic 350', 'Meteor 350'],
            'BMW' => ['G310R', 'G310GS'],
            'Harley-Davidson' => ['Street 500', 'Iron 883'],
        ];

        $model = $this->randomFrom($modelByMerk[$merk]);
        $jenisKendaraan = $this->randomFrom($this->jenisKendaraanMotorList);

        $jenisMotor = $this->randomFrom(['Matic', 'Sport', 'Bebek', 'Trail']);
        $transmisi = $jenisMotor === 'Matic' ? 'Automatic' : $this->randomFrom(['Manual', 'Automatic']);
        $bahanBakar = 'Bensin';
        $kondisi = $this->randomFrom(['Baru', 'Bekas']);
        $warna = $this->randomFrom(['Hitam', 'Putih', 'Merah', 'Biru', 'Silver']);
        $lokasi = $this->randomFrom(['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Bali', 'Medan']);
        $tahun = random_int(2016, 2024);

        $cc = $this->randomFrom([110, 125, 150, 155, 160, 250]);
        $km = $kondisi === 'Baru' ? random_int(0, 300) : random_int(2000, 70000);

        $harga = $isSewa
            ? $this->rupiahRange(50000, 250000)
            : $this->rupiahRange(8000000, 65000000);

        $judulIklan = "{$merk} {$model} {$tahun} - {$jenisMotor}";
        $desc = "Motor {$kondisi}, {$transmisi}, {$cc}cc. Lokasi {$lokasi}. Terawat dan siap pakai.";

        $fallback = 'motor/default.png';
        $gambar = $this->pickRandomStorageImage('motor', $fallback);
        $galery = $this->pickRandomGallery('motor', $fallback, 3);

        $slug = Str::slug("{$merk}-{$model}-{$tahun}-" . Str::random(6));

        return [
            'slug' => Str::slug("{$merk}-{$model}-" . Str::random(6)),
            'user_id' => $userId,

            'gambar_motor' => $gambar,
            'galery_motor' => $galery,

            'lokasi_motor' => $lokasi,
            'jenis_kendaraan_motor' => $jenisKendaraan,
            'jenis_motor' => $jenisMotor,
            'transmisi_motor' => $transmisi,
            'jenis_bahan_bakar_motor' => $bahanBakar,
            'merk_motor' => $merk,
            'model_motor' => $model,
            'kondisi_motor' => $kondisi,

            'status_pajak_motor' => $this->randomFrom(['Hidup', 'Mati', 'Tidak Ada']),
            'kelengkapan_pajak_motor' => $this->randomFrom(['Lengkap', 'Tidak Lengkap']),
            'kelengkapan_surat_motor' => $this->randomFrom(['Lengkap', 'Tidak Lengkap']),
            'atas_nama_motor' => $this->randomFrom(['Pribadi', 'Perusahaan']),
            'riwayat_servis_motor' => $this->randomFrom(['Rutin', 'Tidak Ada', 'Kadang']),

            'judul_iklan_motor' => $judulIklan,
            'deskripsi_iklan_motor' => $desc,

            'nama_penjual_motor' => $this->randomFrom(['AutoTrust', 'Showroom Jaya', 'Bursa Motor', 'Pemilik Langsung']),
            'email_penjual_motor' => $this->randomFrom(['user-336a6767@example.test', 'user-50003d47@example.test', 'user-e1d37357@example.test']),
            'no_hp_penjual_motor' => $this->randomFrom(['081234567890', '081298765432', '082112223333']),

            'likes_motor' => random_int(0, 250),

            'tahun_motor' => $tahun,
            'kapasitas_mesin_motor' => $cc,
            'harga_motor' => $harga,
            'km_motor' => $km,

            'warna_motor' => $warna,
            'tipe_penjual_motor' => $this->randomFrom(['Dealer', 'Individu']),
            'nama_bursa_motor' => $this->randomFrom(['AutoTrust Bursa', 'Bursa Motor Online', 'Bursa Lokal']),

            'security_motor' => $this->randomFrom(['Alarm', 'Immobilizer', 'Double Stand']),
            'exterior_motor' => $this->randomFrom(['Velg Racing', 'Lampu LED', 'Spion Custom']),
            'convenience_motor' => $this->randomFrom(['Electric Starter', 'Bagasi Luas', 'USB Charger']),
            'performance_motor' => $this->randomFrom(['Tarikan Responsif', 'Irit BBM', 'Stabil di Kecepatan Tinggi']),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * =========================
     * SEEDERS
     * =========================
     */
    private function seedMobilJual(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('mobil')->insert($this->makeMobilRow(1, false));
        }
    }

    private function seedMotorJual(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('motor')->insert($this->makeMotorRow(1, false));
        }
    }

    private function seedMobilSewa(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('mobil_sewa')->insert($this->makeMobilRow(1, true));
        }
    }

    private function seedMotorSewa(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('motor_sewa')->insert($this->makeMotorRow(1, true));
        }
    }
}
