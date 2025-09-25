<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysUserKapalTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sys_user_kapal', function (Blueprint $table) {
            $table->integer('sys_user_kapal_id')->primary();
            $table->integer('conf_user_id')->nullable();
            $table->integer('m_kapal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_user_kapal');
    }
};
