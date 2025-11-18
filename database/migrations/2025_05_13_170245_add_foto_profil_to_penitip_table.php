<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoProfilToPenitipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penitip', function (Blueprint $table) {
            $table->string('FOTO_PROFIL')->nullable();  // Menambahkan kolom FOTO_PROFIL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penitip', function (Blueprint $table) {
            $table->dropColumn('FOTO_PROFIL');  // Menghapus kolom FOTO_PROFIL
        });
    }
}