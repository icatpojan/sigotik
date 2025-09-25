<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmAnggaranUptTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bbm_anggaran_upt', function (Blueprint $table) {
            $table->integer('anggaran_upt_id')->primary();
            $table->date('tanggal_trans');
            $table->string('m_upt_code', 10)->default('');
            $table->decimal('nominal', 20, 2)->default(0.00);
            $table->string('nomor_surat', 50)->default('');
            $table->string('keterangan', 255)->default('');
            $table->tinyInteger('statusperubahan')->default(0)->comment('0=belum app , 1 = approval , 2 = batal');
            $table->string('user_input', 10)->default('');
            $table->datetime('tanggal_input');
            $table->string('user_app', 10)->default('');
            $table->datetime('tanggal_app')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbm_anggaran_upt');
    }
};
