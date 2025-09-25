<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMUptTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_upt', function (Blueprint $table) {
            $table->integer('m_upt_id')->primary();
            $table->string('nama', 255)->nullable();
            $table->string('code', 10)->nullable();
            $table->string('alamat1', 255)->nullable();
            $table->string('alamat2', 255)->nullable();
            $table->string('alamat3', 200)->nullable();
            $table->string('kota', 100)->nullable();
            $table->enum('zona_waktu_upt', ['WIB', 'WITA', 'WIT'])->default('WIB');
            $table->string('nama_petugas', 50)->nullable();
            $table->string('nip_petugas', 50)->nullable();
            $table->string('jabatan_petugas', 50)->nullable();
            $table->string('pangkat_petugas', 30)->nullable();
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
        Schema::dropIfExists('m_upt');
    }
}
