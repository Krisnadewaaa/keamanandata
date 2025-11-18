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
        Schema::table('donasi', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('donasi', function (Blueprint $table) {
            $table->string('status')->nullable(false)->default('0')->change();
        });
    }
};
