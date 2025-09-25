<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRememberTokenToConfUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conf_user', function (Blueprint $table) {
            $table->string('remember_token', 100)->nullable()->after('user_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conf_user', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
