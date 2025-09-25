<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbmTagihanTable extends Migration
{
    public function up(): void
    {
        Schema::create('bbm_tagihan', function (Blueprint $table) {
            $table->integer('tagihan_id')->primary();
            $table->string('m_upt_code', 10)->default('');
            $table->string('no_tagihan', 50)->default('');
            $table->date('tanggal_invoice')->nullable();
            $table->string('no_invoice', 30)->default('');
            $table->string('penyedia', 50)->default('');
            $table->integer('quantity')->default(0);
            $table->decimal('harga', 12, 2)->default(0.00);
            $table->decimal('hargaperliter', 12, 2)->default(0.00);
            $table->decimal('ppn', 12, 2)->default(0.00);
            $table->decimal('total', 20, 2)->default(0.00);
            $table->tinyInteger('statustagihan')->default(0)->comment('0 = input, 1 = approval, 3= batal');
            $table->date('tanggal_sppd')->nullable();
            $table->string('file', 255)->nullable();
            $table->string('user_input', 10)->default('');
            $table->datetime('tanggal_input')->nullable();
            $table->string('user_app', 10)->default('');
            $table->datetime('tanggal_app')->nullable();
            $table->string('user_batal', 10)->default('');
            $table->datetime('tanggal_batal')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bbm_tagihan');
    }
};
