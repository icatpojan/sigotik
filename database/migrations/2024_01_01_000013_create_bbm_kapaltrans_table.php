<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmKapaltransTable extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_kapaltrans', function (Blueprint $table) {

            // Identifier dan Meta Data Transaksi
            $table->integer('trans_id')->primary();
            $table->string('kapal_code', 20)->default(''); // DIKURANGI DARI 30
            $table->string('nomor_surat', 45)->default('');
            $table->date('tanggal_surat')->nullable();
            $table->char('jam_surat', 8)->default('');
            $table->enum('zona_waktu_surat', ['WIB', 'WITA', 'WIT'])->default('WIB');
            $table->text('lokasi_surat')->nullable(false); // TEXT

            // Detail BBM
            $table->decimal('volume_sisa', 10, 2)->default(0.00);
            $table->decimal('volume_sebelum', 10, 0)->default(0);
            $table->date('tanggal_sebelum')->nullable();
            $table->decimal('volume_pengisian', 10, 2)->default(0.00);
            $table->date('tanggal_pengisian')->nullable();
            $table->decimal('volume_pemakaian', 10, 2)->default(0.00);
            $table->string('nomor_nota', 30)->default('');
            $table->string('keterangan_jenis_bbm', 45)->default('');
            $table->string('penyedia', 30)->default('')->comment('nama pt penyedia'); // DIKURANGI DARI 45

            // Status dan Kondisi
            $table->tinyInteger('status_ba')->default(0);
            $table->enum('jenis_tranport', ['0', '1', '2', '3', ''])->default('0')->comment('0 = kosong, 1 = mobil, 2 = kapal');
            $table->tinyInteger('status_segel')->default(1)->comment('1 baik 2 rusak');
            $table->string('gambar_segel', 50)->nullable(); // DIKURANGI DARI 100
            $table->tinyInteger('status_flowmeter')->default(1)->comment('1 baik 2 rusak');
            $table->string('gambar_flowmeter', 50)->nullable(); // DIKURANGI DARI 100
            $table->tinyInteger('kesimpulan')->default(1)->comment('1. pengisian dilakukan 2 pengisian di tunda');
            $table->tinyInteger('status_modul')->default(0);
            $table->tinyInteger('status_trans')->default(0)->comment('0 = input, 1 = approval, 2 = batal');

            // Dokumen dan Upload
            $table->string('no_tagihan', 30)->default(''); // DIKURANGI DARI 50
            $table->tinyInteger('status_bayar')->default(0);
            $table->tinyInteger('status_upload')->default(0)->comment('0 belum upload 1 sudah upload');
            $table->string('file_upload', 50)->default(''); // DIKURANGI DARI 100
            $table->string('foto', 50)->nullable(); // DIKURANGI DARI 100
            $table->string('ttd', 50)->nullable(); // DIKURANGI DARI 100
            $table->string('link_modul_ba', 50)->nullable(); // DIKURANGI DARI 100
            $table->string('link_modul_ba_tamb', 50)->nullable(); // DIKURANGI DARI 100
            $table->string('link_modul_temp', 50)->nullable(); // DIKURANGI DARI 50
            $table->string('no_so', 15)->nullable(); // DIKURANGI DARI 20
            $table->string('foto_so', 50)->nullable()->comment('Foto Sales Order untuk BA');

            // Informasi Pejabat UPT/Staf Pangkalan (Saksi)
            $table->string('jabatan_staf_pangkalan', 40)->default(''); // DIKURANGI DARI 45
            $table->string('nama_staf_pangkalan', 30)->default(''); // DIKURANGI DARI 45
            $table->string('nip_staf', 20)->default(''); // DIKURANGI DARI 25
            $table->integer('an_staf')->default(0);

            // Informasi Nakhoda (Utama)
            $table->string('nama_nahkoda', 40)->default(''); // DIKURANGI DARI 45
            $table->string('nip_nahkoda', 20)->default(''); // DIKURANGI DARI 25
            $table->string('jabatan_nahkoda', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_nahkoda', 15)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_nahkoda', 15)->nullable(); // DIKURANGI DARI 45
            $table->integer('an_nakhoda')->default(0);

            // Informasi KKM (Utama)
            $table->string('nama_kkm', 30)->default(''); // DIKURANGI DARI 45
            $table->string('nama_staf_pagkalan', 30)->default(''); // DIKURANGI DARI 45
            $table->string('nip_kkm', 20)->default(''); // DIKURANGI DARI 25
            $table->string('jabatan_kkm', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_kkm', 15)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_kkm', 15)->nullable(); // DIKURANGI DARI 45
            $table->integer('an_kkm')->default(0);

            // Informasi Atas Nama (An.)
            $table->string('nama_an', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nip_an', 20)->nullable(); // DIKURANGI DARI 25
            $table->string('jabatan_an', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_an', 15)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_an', 30)->nullable(); // DIKURANGI DARI 45

            // Kolom Peruntukan dan Penitip
            $table->string('peruntukan', 45)->nullable(); // DIKURANGI DARI 70
            $table->decimal('penggunaan', 10, 2)->nullable();
            $table->string('nama_penitip', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('jabatan_penitip', 20)->nullable(); // DIKURANGI DARI 45
            $table->text('alamat_penitip')->nullable(); // TETAP TEXT
            $table->string('penyedia_penitip', 30)->nullable(); // DIKURANGI DARI 100
            $table->text('alamat_penyedia_penitip')->nullable(); // TETAP TEXT

            // Kolom Temporer (Temp) untuk Revisi/Tunda
            $table->string('kapal_code_temp', 20)->nullable(); // DIKURANGI DARI 30
            $table->string('nama_nahkoda_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nip_nahkoda_temp', 20)->nullable(); // DIKURANGI DARI 25
            $table->string('jabatan_nahkoda_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_nahkoda_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_nahkoda_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nama_kkm_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nip_kkm_temp', 20)->nullable(); // DIKURANGI DARI 25
            $table->string('jabatan_kkm_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_kkm_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_kkm_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nama_an_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('nip_an_temp', 20)->nullable(); // DIKURANGI DARI 25
            $table->string('jabatan_an_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('pangkat_an_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->string('golongan_an_temp', 30)->nullable(); // DIKURANGI DARI 45
            $table->integer('an_nakhoda_temp')->nullable();
            $table->integer('an_kkm_temp')->nullable();
            $table->integer('status_temp')->nullable();
            $table->text('sebab_temp')->nullable(); // TETAP TEXT
            $table->string('nama_penyedia', 45)->nullable(); // DIKURANGI DARI 100
            $table->string('instansi_temp', 45)->nullable(); // DIKURANGI DARI 100
            $table->string('alamat_instansi_temp', 45)->nullable(); // DIKURANGI DARI 100
            $table->string('nomer_persetujuan', 45)->nullable(); // DIKURANGI DARI 100
            $table->date('tgl_persetujuan')->nullable();
            $table->integer('m_persetujuan_id')->nullable();
            $table->string('lokasi_temp', 45)->nullable(); // DIKURANGI DARI 100

            // Tracking/Audit
            $table->string('user_input', 10)->default('');
            $table->timestamps();
            $table->string('user_app', 10)->default('');
            $table->datetime('tanggal_app')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_kapaltrans');
    }
}
