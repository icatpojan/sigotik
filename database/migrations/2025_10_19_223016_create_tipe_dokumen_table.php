<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipeDokumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipe_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('kode_dokumen', 20)->unique(); // Kode unik untuk tipe dokumen
            $table->string('nama_dokumen'); // Nama tipe dokumen
            $table->text('deskripsi')->nullable(); // Deskripsi tipe dokumen
            $table->json('allowed_extensions')->nullable(); // Ekstensi file yang diizinkan
            $table->bigInteger('max_size_kb')->default(5120); // Maksimal ukuran file dalam KB
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipe_dokumen');
    }
}
