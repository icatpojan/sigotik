<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmAnggaranTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bbm_anggaran', function (Blueprint $table) {
            $table->integer('anggaran_id')->primary();
            $table->integer('periode')->default(0);
            $table->string('m_upt_code', 10)->default('');
            $table->decimal('anggaran', 20, 2)->default(0.00);
            $table->integer('perubahan_ke')->default(0);
            $table->string('keterangan', 255)->default('');
            $table->tinyInteger('statusanggaran')->default(0)->comment('0=belum app , 1 = approval');
            $table->string('user_input', 10)->default('');
            $table->datetime('tanggal_input');
            $table->string('user_app', 10)->default('');
            $table->datetime('tanggal_app')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbm_anggaran');
    }
};
