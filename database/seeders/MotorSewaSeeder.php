<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MotorSewa;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MotorSewaSeeder extends Seeder
{
    public function run(): void
    {
        $userId = \App\Models\User::inRandomOrder()->value('id');

        $imagePath = storage_path('app/public/motor');
        $images = File::files($imagePath);

        if (empty($images)) {
            $this->command->warn('Folder motor kosong!');
            return;
        }

        $data = [
            ['Honda','Vario 160','Automatic','Jakarta',120000],
            ['Yamaha','NMAX','Automatic','Bandung',150000],
            ['Suzuki','GSX R150','Manual','Surabaya',130000],
            ['Kawasaki','KLX 150','Manual','Yogyakarta',140000],
            ['Honda','CBR 150R','Manual','Bali',160000],
            ['Yamaha','Aerox','Automatic','Semarang',125000],
            ['Honda','Beat','Automatic','Medan',90000],
            ['Yamaha','R15','Manual','Makassar',170000],
            ['Suzuki','Satria FU','Manual','Jakarta',110000],
            ['Kawasaki','Ninja 250','Manual','Bandung',200000],
        ];

        foreach ($data as $d) {

            $randomImage = $images[array_rand($images)];
            $fileName = 'motor/'.$randomImage->getFilename();

            \App\Models\MotorSewa::create([
                'slug' => \Str::slug($d[0].'-'.$d[1].'-sewa'),
                'user_id' => $userId,
                'gambar_motor' => $fileName,
                'merk_motor' => $d[0],
                'model_motor' => $d[1],
                'transmisi_motor' => $d[2],
                'lokasi_motor' => $d[3],
                'kondisi_motor' => 'Bekas',
                'tahun_motor' => rand(2020,2024),
                'kapasitas_mesin_motor' => rand(110,250),
                'harga_motor' => $d[4],
                'judul_iklan_motor' => $d[0].' '.$d[1].' Siap Disewa',
                'deskripsi_iklan_motor' => 'Motor siap pakai dan irit.',
            ]);
        }
    }

}
