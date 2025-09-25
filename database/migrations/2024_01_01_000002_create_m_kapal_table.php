<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMKapalTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_kapal', function (Blueprint $table) {
            $table->integer('m_kapal_id')->primary();
            $table->char('nama_kapal', 150);
            $table->string('code_kapal', 30)->nullable();
            $table->string('m_upt_code', 10)->nullable();
            $table->decimal('bobot', 11, 2)->nullable();
            $table->decimal('panjang', 11, 2)->nullable();
            $table->decimal('tinggi', 11, 2)->nullable();
            $table->decimal('lebar', 11, 2)->nullable();
            $table->string('main_engine', 100)->default('0');
            $table->integer('jml_main_engine')->default(0);
            $table->string('pk_main_engine', 100)->nullable();
            $table->string('aux_engine_utama', 100)->default('0');
            $table->integer('jml_aux_engine_utama')->default(0);
            $table->string('pk_aux_engine_utama', 100)->nullable();
            $table->string('gerak_engine', 100)->nullable();
            $table->string('aux_engine_emergency', 100)->nullable();
            $table->string('galangan_pembuat', 100)->nullable();
            $table->string('kapasitas_tangki', 100)->nullable();
            $table->integer('jml_tangki')->nullable();
            $table->integer('tahun_buat')->nullable();
            $table->integer('jml_abk')->nullable();
            $table->string('nama_nakoda', 100)->nullable();
            $table->string('nip_nakoda', 50)->nullable();
            $table->string('jabatan_nakoda', 50)->nullable();
            $table->string('pangkat_nakoda', 50)->nullable();
            $table->string('golongan_nakoda', 50)->nullable();
            $table->string('gambar_kapal', 100)->nullable();
            $table->string('lampiran_kapal', 100)->nullable();
            $table->string('nama_kkm', 100)->nullable();
            $table->string('nip_kkm', 50)->nullable();
            $table->string('jabatan_kkm', 50)->nullable();
            $table->string('pangkat_kkm', 50)->nullable();
            $table->string('golongan_kkm', 50)->nullable();
            $table->datetime('date_insert')->nullable();
            $table->string('user_insert', 30)->nullable();
            $table->datetime('date_update')->nullable();
            $table->string('user_update', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kapal');
    }
};
