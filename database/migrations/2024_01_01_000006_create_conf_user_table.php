<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conf_user', function (Blueprint $table) {
            $table->integer('conf_user_id')->primary();
            $table->string('username', 30);
            $table->string('password', 250)->nullable();
            $table->string('m_upt_code', 20)->nullable();
            $table->integer('conf_group_id')->nullable();
            $table->string('email', 50)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->string('nama_lengkap', 100)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('golongan', 30)->nullable();
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
        Schema::dropIfExists('conf_user');
    }
};
