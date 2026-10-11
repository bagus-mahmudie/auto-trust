<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * =========================
         * MOBIL (jual)
         * =========================
         */
        Schema::create('mobil', function (Blueprint $table) {
            $table->id();

            $table->string('slug'); // DB: NOT NULL, TIDAK unique
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('gambar_mobil')->nullable();
            $table->longText('galery_mobil')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable();

            $table->string('lokasi')->nullable();
            $table->string('jenis_kendaraan')->nullable();
            $table->string('jenis_mobil')->nullable();
            $table->string('transmisi_mobil')->nullable();
            $table->string('jenis_bahan_bakar_mobil')->nullable();
            $table->string('merk_mobil')->nullable();
            $table->string('model_mobil')->nullable();
            $table->string('kondisi_mobil')->nullable();

            $table->string('status_pajak')->nullable();
            $table->string('kelengkapan_pajak')->nullable();
            $table->string('kelengkapan_surat')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('riwayat_servis')->nullable();

            $table->string('judul_iklan')->nullable();
            $table->text('deskripsi_iklan')->nullable();

            $table->string('nama_penjual')->nullable();
            $table->string('email_penjual')->nullable();
            $table->string('no_hp_penjual')->nullable();

            $table->unsignedInteger('likes_mobil')->default(0);

            $table->unsignedSmallInteger('tahun')->nullable(); // smallint unsigned
            $table->unsignedTinyInteger('tempat_duduk_mobil')->nullable(); // tinyint unsigned
            $table->unsignedInteger('kapasitas_mesin_mobil')->nullable(); // int unsigned
            $table->decimal('konsumsi_bbm_mobil', 5, 2)->nullable();
            $table->unsignedBigInteger('harga_mobil')->nullable(); // bigint unsigned
            $table->unsignedInteger('km_mobil')->nullable();

            $table->string('warna_mobil')->nullable();
            $table->string('tipe_penjual')->nullable();
            $table->string('nama_bursa_mobil')->nullable();

            $table->text('security_mobil')->nullable();
            $table->text('exterior_mobil')->nullable();
            $table->text('convenience_mobil')->nullable();
            $table->text('performance_mobil')->nullable();

            $table->timestamps();
        });

        // DB: CHECK (json_valid(galery_mobil))
        DB::statement("ALTER TABLE `mobil` ADD CONSTRAINT `mobil_galery_mobil_json_check` CHECK (json_valid(`galery_mobil`))");

        /**
         * =========================
         * MOTOR (jual)
         * =========================
         */
        Schema::create('motor', function (Blueprint $table) {
            $table->id();

            $table->string('slug'); // DB: NOT NULL, TIDAK unique
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('gambar_motor')->nullable();
            $table->longText('galery_motor')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable();

            $table->string('lokasi_motor')->nullable();
            $table->string('jenis_kendaraan_motor')->nullable();
            $table->string('jenis_motor')->nullable();
            $table->string('transmisi_motor')->nullable();
            $table->string('jenis_bahan_bakar_motor')->nullable();
            $table->string('merk_motor')->nullable();
            $table->string('model_motor')->nullable();
            $table->string('kondisi_motor')->nullable();

            $table->string('status_pajak_motor')->nullable();
            $table->string('kelengkapan_pajak_motor')->nullable();
            $table->string('kelengkapan_surat_motor')->nullable();
            $table->string('atas_nama_motor')->nullable();
            $table->string('riwayat_servis_motor')->nullable();

            $table->string('judul_iklan_motor')->nullable();
            $table->text('deskripsi_iklan_motor')->nullable();

            $table->string('nama_penjual_motor')->nullable();
            $table->string('email_penjual_motor')->nullable();
            $table->string('no_hp_penjual_motor')->nullable();

            $table->unsignedInteger('likes_motor')->default(0);

            $table->unsignedSmallInteger('tahun_motor')->nullable();
            $table->unsignedInteger('kapasitas_mesin_motor')->nullable();
            $table->unsignedBigInteger('harga_motor')->nullable();
            $table->unsignedInteger('km_motor')->nullable();

            $table->string('warna_motor')->nullable();
            $table->string('tipe_penjual_motor')->nullable();
            $table->string('nama_bursa_motor')->nullable();

            $table->text('security_motor')->nullable();
            $table->text('exterior_motor')->nullable();
            $table->text('convenience_motor')->nullable();
            $table->text('performance_motor')->nullable();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE `motor` ADD CONSTRAINT `motor_galery_motor_json_check` CHECK (json_valid(`galery_motor`))");

        /**
         * =========================
         * SEWA MOBIL
         * =========================
         */
        Schema::create('mobil_sewa', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique(); // DB: mobil_sewa_slug_unique
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('gambar_mobil')->nullable();
            $table->longText('galery_mobil')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable();

            $table->string('lokasi')->nullable();
            $table->string('jenis_kendaraan')->nullable();
            $table->string('jenis_mobil')->nullable();
            $table->string('transmisi_mobil')->nullable();
            $table->string('jenis_bahan_bakar_mobil')->nullable();
            $table->string('merk_mobil')->nullable();
            $table->string('model_mobil')->nullable();
            $table->string('kondisi_mobil')->nullable();

            $table->string('status_pajak')->nullable();
            $table->string('kelengkapan_pajak')->nullable();
            $table->string('kelengkapan_surat')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('riwayat_servis')->nullable();

            $table->string('judul_iklan')->nullable();
            $table->text('deskripsi_iklan')->nullable();

            $table->string('nama_penjual')->nullable();
            $table->string('email_penjual')->nullable();
            $table->string('no_hp_penjual')->nullable();

            $table->unsignedInteger('likes_mobil')->default(0);

            $table->unsignedSmallInteger('tahun')->nullable();
            $table->unsignedTinyInteger('tempat_duduk_mobil')->nullable();
            $table->unsignedInteger('kapasitas_mesin_mobil')->nullable();
            $table->decimal('konsumsi_bbm_mobil', 5, 2)->nullable();
            $table->unsignedBigInteger('harga_mobil')->nullable();
            $table->unsignedInteger('km_mobil')->nullable();

            $table->string('warna_mobil')->nullable();
            $table->string('tipe_penjual')->nullable();
            $table->string('nama_bursa_mobil')->nullable();

            $table->text('security_mobil')->nullable();
            $table->text('exterior_mobil')->nullable();
            $table->text('convenience_mobil')->nullable();
            $table->text('performance_mobil')->nullable();

            $table->timestamps();

            // Index tambahan sesuai DB
            $table->index('user_id', 'ms_user_idx');
            $table->index('kondisi_mobil', 'ms_kondisi_idx');
            $table->index('lokasi', 'ms_lokasi_idx');
            $table->index('jenis_mobil', 'ms_jenis_idx');
            $table->index('transmisi_mobil', 'ms_trans_idx');
        });

        DB::statement("ALTER TABLE `mobil_sewa` ADD CONSTRAINT `mobil_sewa_galery_mobil_json_check` CHECK (json_valid(`galery_mobil`))");

        /**
         * =========================
         * SEWA MOTOR
         * =========================
         */
        Schema::create('motor_sewa', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique(); // DB: motor_sewa_slug_unique
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('gambar_motor')->nullable();
            $table->longText('galery_motor')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable();

            $table->string('lokasi_motor')->nullable();
            $table->string('jenis_kendaraan_motor')->nullable();
            $table->string('jenis_motor')->nullable();
            $table->string('transmisi_motor')->nullable();
            $table->string('jenis_bahan_bakar_motor')->nullable();
            $table->string('merk_motor')->nullable();
            $table->string('model_motor')->nullable();
            $table->string('kondisi_motor')->nullable();

            $table->string('status_pajak_motor')->nullable();
            $table->string('kelengkapan_pajak_motor')->nullable();
            $table->string('kelengkapan_surat_motor')->nullable();
            $table->string('atas_nama_motor')->nullable();
            $table->string('riwayat_servis_motor')->nullable();

            $table->string('judul_iklan_motor')->nullable();
            $table->text('deskripsi_iklan_motor')->nullable();

            $table->string('nama_penjual_motor')->nullable();
            $table->string('email_penjual_motor')->nullable();
            $table->string('no_hp_penjual_motor')->nullable();

            $table->unsignedInteger('likes_motor')->default(0);

            $table->unsignedSmallInteger('tahun_motor')->nullable();
            $table->unsignedInteger('kapasitas_mesin_motor')->nullable();
            $table->unsignedBigInteger('harga_motor')->nullable();
            $table->unsignedInteger('km_motor')->nullable();

            $table->string('warna_motor')->nullable();
            $table->string('tipe_penjual_motor')->nullable();
            $table->string('nama_bursa_motor')->nullable();

            $table->text('security_motor')->nullable();
            $table->text('exterior_motor')->nullable();
            $table->text('convenience_motor')->nullable();
            $table->text('performance_motor')->nullable();

            $table->timestamps();

            // Index tambahan sesuai DB
            $table->index('user_id', 'mts_user_idx');
            $table->index('kondisi_motor', 'mts_kondisi_idx');
            $table->index('lokasi_motor', 'mts_lokasi_idx');
            $table->index('jenis_motor', 'mts_jenis_idx');
            $table->index('transmisi_motor', 'mts_trans_idx');
        });

        DB::statement("ALTER TABLE `motor_sewa` ADD CONSTRAINT `motor_sewa_galery_motor_json_check` CHECK (json_valid(`galery_motor`))");

        /**
         * =========================
         * REVIEWS MOBIL
         * =========================
         */
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mobil_id')->constrained('mobil')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // DB: ON DELETE SET NULL

            $table->string('name')->nullable();
            $table->unsignedTinyInteger('rating'); // tinyint unsigned NOT NULL
            $table->text('comment');

            $table->timestamps();

            $table->unique(['mobil_id', 'user_id'], 'reviews_mobil_user_unique');
        });

        /**
         * =========================
         * REVIEWS MOTOR
         * =========================
         */
        Schema::create('motor_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('motor_id')->constrained('motor')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // DB: set null

            $table->string('name')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('comment');

            $table->timestamps();

            $table->unique(['motor_id', 'user_id'], 'motor_reviews_motor_user_unique');
        });

        /**
         * =========================
         * LIKES MOBIL
         * =========================
         */
        Schema::create('mobil_likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobil')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'mobil_id'], 'mobil_likes_user_id_mobil_id_unique');
        });

        /**
         * =========================
         * LIKES MOTOR
         * =========================
         */
        Schema::create('motor_likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('motor_id')->constrained('motor')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'motor_id'], 'motor_likes_user_id_motor_id_unique');
        });

        /**
         * =========================
         * MESSAGES
         * =========================
         */
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();

            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'receiver_id'], 'messages_sender_id_receiver_id_index');
            $table->index(['receiver_id', 'read_at'], 'messages_receiver_id_read_at_index');
        });

        /**
         * =========================
         * KEBUTUHAN POSTINGS
         * =========================
         */
        Schema::create('kebutuhan_postings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('nama', 120);
            $table->string('kota', 120)->nullable();
            $table->string('judul', 160);
            $table->string('merek', 80)->nullable();
            $table->string('tipe_bodi', 80)->nullable();
            $table->string('transmisi', 80)->nullable();
            $table->string('range_harga', 80)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_postings');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('motor_likes');
        Schema::dropIfExists('mobil_likes');
        Schema::dropIfExists('motor_reviews');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('motor_sewa');
        Schema::dropIfExists('mobil_sewa');
        Schema::dropIfExists('motor');
        Schema::dropIfExists('mobil');
    }
};
