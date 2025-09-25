<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmKapaltransTable extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_kapaltrans', function (Blueprint $table) {
            $table->integer('trans_id')->primary();
            $table->string('kapal_code', 30)->default('');
            $table->string('nomor_surat', 50)->default('');
            $table->date('tanggal_surat')->nullable();
            $table->char('jam_surat', 8)->default('');
            $table->enum('zona_waktu_surat', ['WIB', 'WITA', 'WIT'])->default('WIB');
            $table->string('lokasi_surat', 200);
            $table->decimal('volume_sisa', 10, 2)->default(0.00);
            $table->decimal('volume_sebelum', 10, 0)->default(0);
            $table->date('tanggal_sebelum')->nullable();
            $table->decimal('volume_pengisian', 10, 2)->default(0.00);
            $table->date('tanggal_pengisian')->nullable();
            $table->decimal('volume_pemakaian', 10, 2)->default(0.00);
            $table->string('nomor_nota', 30)->default('');
            $table->string('keterangan_jenis_bbm', 50)->default('');
            $table->tinyInteger('status_ba')->default(0);
            $table->enum('jenis_tranport', ['0', '1', '2', '3', ''])->default('0')->comment('0 = kosong, 1 = mobil, 2 = kapal');
            $table->tinyInteger('status_segel')->default(1)->comment('1 baik 2 rusak');
            $table->string('gambar_segel', 100)->nullable();
            $table->tinyInteger('status_flowmeter')->default(1)->comment('1 baik 2 rusak');
            $table->string('gambar_flowmeter', 100)->nullable();
            $table->string('nama_nahkoda', 50)->nullable();
            $table->string('nip_nahkoda', 50)->nullable();
            $table->string('jabatan_nahkoda', 50)->nullable();
            $table->string('pangkat_nahkoda', 50)->nullable();
            $table->string('golongan_nahkoda', 50)->nullable();
            $table->string('nama_kkm', 50)->nullable();
            $table->string('nip_kkm', 50)->nullable();
            $table->string('jabatan_kkm', 50)->nullable();
            $table->string('pangkat_kkm', 50)->nullable();
            $table->string('golongan_kkm', 50)->nullable();
            $table->string('nama_an', 50)->nullable();
            $table->string('nip_an', 50)->nullable();
            $table->string('jabatan_an', 50)->nullable();
            $table->string('pangkat_an', 50)->nullable();
            $table->string('golongan_an', 50)->nullable();
            $table->integer('an_nakhoda')->default(0);
            $table->integer('an_kkm')->default(0);
            $table->string('kapal_code_temp', 30)->nullable();
            $table->string('nama_nahkoda_temp', 50)->nullable();
            $table->string('nip_nahkoda_temp', 50)->nullable();
            $table->string('jabatan_nahkoda_temp', 50)->nullable();
            $table->string('pangkat_nahkoda_temp', 50)->nullable();
            $table->string('golongan_nahkoda_temp', 50)->nullable();
            $table->string('nama_kkm_temp', 50)->nullable();
            $table->string('nip_kkm_temp', 50)->nullable();
            $table->string('jabatan_kkm_temp', 50)->nullable();
            $table->string('pangkat_kkm_temp', 50)->nullable();
            $table->string('golongan_kkm_temp', 50)->nullable();
            $table->string('nama_an_temp', 50)->nullable();
            $table->string('nip_an_temp', 50)->nullable();
            $table->string('jabatan_an_temp', 50)->nullable();
            $table->string('pangkat_an_temp', 50)->nullable();
            $table->string('golongan_an_temp', 50)->nullable();
            $table->integer('an_nakhoda_temp')->default(0);
            $table->integer('an_kkm_temp')->default(0);
            $table->string('user_input', 10)->default('');
            $table->datetime('tanggal_input');
            $table->string('user_app', 10)->default('');
            $table->datetime('tanggal_app')->nullable();
            $table->tinyInteger('status_trans')->default(0)->comment('0 = input, 1 = approval, 2 = batal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_kapaltrans');
    }
};
