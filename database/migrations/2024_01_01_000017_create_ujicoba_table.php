<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUjicobaTable extends Migration
{
    public function up(): void
    {
        Schema::create('ujicoba', function (Blueprint $table) {
            $table->decimal('no', 10, 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujicoba');
    }
};
