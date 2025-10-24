<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipeBaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipe_ba', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ba', 10)->unique(); // Kode unik untuk tipe BA
            $table->string('nama_ba'); // Nama tipe BA
            $table->text('deskripsi')->nullable(); // Deskripsi tipe BA
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
        Schema::dropIfExists('tipe_ba');
    }
}
