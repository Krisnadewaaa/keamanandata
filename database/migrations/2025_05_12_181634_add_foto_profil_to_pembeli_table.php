<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pembeli', function (Blueprint $table) {
            $table->string('FOTO_PROFIL')->nullable()->after('EMAIL_PEMBELI'); // atau sesuaikan posisinya
        });
    }

    public function down()
    {
        Schema::table('pembeli', function (Blueprint $table) {
            $table->dropColumn('FOTO_PROFIL');
        });
    }

};
