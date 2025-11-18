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
        Schema::table('penitip', function (Blueprint $table) {
            $table->string('NO_KTP', 20)->nullable()->after('PASSWORD_PENITIP');
            $table->string('FOTO_KTP')->nullable()->after('NO_KTP');
        });
    }

    public function down()
    {
        Schema::table('penitip', function (Blueprint $table) {
            $table->dropColumn('NO_KTP');
            $table->dropColumn('FOTO_KTP');
        });
    }
};
