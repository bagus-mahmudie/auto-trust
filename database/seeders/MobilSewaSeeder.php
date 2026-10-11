<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MobilSewa;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MobilSewaSeeder extends Seeder
{
    public function run(): void
        {
            $userId = \App\Models\User::inRandomOrder()->value('id');

            $imagePath = storage_path('app/public/mobil');
            $images = File::files($imagePath);

            if (empty($images)) {
                $this->command->warn('Folder mobil kosong!');
                return;
            }

            $data = [
                ['Toyota','Avanza','MPV','Automatic','Jakarta',450000,7],
                ['Honda','Brio','City Car','Automatic','Bandung',350000,5],
                ['Mitsubishi','Xpander','MPV','Manual','Surabaya',500000,7],
                ['Suzuki','Ertiga','MPV','Automatic','Yogyakarta',400000,7],
                ['Daihatsu','Sigra','City Car','Manual','Bali',300000,5],
                ['Toyota','Fortuner','SUV','Automatic','Jakarta',900000,7],
                ['Honda','CRV','SUV','Automatic','Bandung',850000,5],
                ['Hyundai','Stargazer','MPV','Automatic','Semarang',480000,7],
                ['Wuling','Almaz','SUV','Automatic','Medan',700000,5],
                ['Isuzu','Elf','Mini Bus','Manual','Surabaya',1200000,15],
            ];

            foreach ($data as $i => $d) {

                $randomImage = $images[array_rand($images)];
                $fileName = 'mobil/'.$randomImage->getFilename();

                \App\Models\MobilSewa::create([
                    'slug' => \Str::slug($d[0].'-'.$d[1].'-sewa'),
                    'user_id' => $userId,
                    'gambar_mobil' => $fileName,
                    'merk_mobil' => $d[0],
                    'model_mobil' => $d[1],
                    'jenis_mobil' => $d[2],
                    'transmisi_mobil' => $d[3],
                    'lokasi' => $d[4],
                    'kondisi_mobil' => 'Bekas',
                    'tahun' => 2020 + ($i % 4),
                    'tempat_duduk_mobil' => $d[6],
                    'kapasitas_mesin_mobil' => rand(1200,2500),
                    'harga_mobil' => $d[5],
                    'judul_iklan' => $d[0].' '.$d[1].' Siap Disewa',
                    'deskripsi_iklan' => 'Unit terawat dan siap jalan.',
                ]);
            }
        }

}
