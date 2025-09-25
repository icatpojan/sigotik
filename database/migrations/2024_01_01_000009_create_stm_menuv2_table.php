<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStmMenuv2Table extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stm_menuv2', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('id_parentmenu')->nullable();
            $table->integer('level')->nullable();
            $table->string('menu', 255)->nullable();
            $table->string('linka', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->integer('urutan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stm_menuv2');
    }
};
