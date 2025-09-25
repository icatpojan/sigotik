<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortNewsTable extends Migration
{
    public function up(): void
    {
        Schema::create('port_news', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('img', 255)->nullable();
            $table->string('news_title', 255)->nullable();
            $table->longText('news')->nullable();
            $table->enum('kategori_id', ['1', '2', '3', '4', '5', '6'])->default('1');
            $table->string('author', 255)->nullable();
            $table->datetime('date_create')->nullable();
            $table->enum('post', ['0', '1'])->default('1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('port_news');
    }
};
