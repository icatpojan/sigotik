<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFotoUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foto_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable(); // Nomor surat BA
            $table->integer('trans_id')->nullable(); // Foreign key ke bbm_kapaltrans
            $table->integer('tipe_ba_id'); // Foreign key ke tipe_ba
            $table->integer('tipe_dokumen_id'); // Foreign key ke tipe_dokumen
            $table->string('nama_file'); // Nama file asli
            $table->string('nama_file_stored'); // Nama file yang disimpan
            $table->string('path_file'); // Path relatif dari public
            $table->string('mime_type'); // Tipe MIME file
            $table->bigInteger('ukuran_file'); // Ukuran file dalam bytes
            $table->text('keterangan')->nullable(); // Keterangan foto
            $table->integer('user_upload_id'); // ID user yang upload
            $table->timestamps();

            // Indexes
            $table->index(['nomor_surat', 'tipe_ba_id']);
            $table->index('trans_id');
            $table->index('tipe_dokumen_id');
            $table->index('user_upload_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foto_uploads');
    }
}
