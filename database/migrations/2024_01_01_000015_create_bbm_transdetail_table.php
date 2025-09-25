<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmTransdetailTable extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_transdetail', function (Blueprint $table) {
            $table->integer('bbm_transdetail_id')->primary();
            $table->string('nomor_surat', 50)->default('');
            $table->string('transportasi', 20)->default('');
            $table->string('no_so', 20)->default('');
            $table->string('no_do', 20)->default('');
            $table->decimal('volume_isi', 10, 2)->default(0.00);
            $table->string('keterangan', 30)->default('');
            $table->string('no_invoice', 50)->default('');
            $table->date('tgl_invoice')->default('1970-01-02');
            $table->decimal('harga_total', 20, 2)->default(0.00);
            $table->tinyInteger('status_bayar')->default(0);
            $table->string('no_tagihan', 50)->nullable();
            $table->datetime('tanggalinput');
            $table->string('userid', 10)->default('');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_transdetail');
    }
};
