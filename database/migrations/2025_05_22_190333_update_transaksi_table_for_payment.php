<?php
// File: database/migrations/2025_05_24_000001_update_transaksi_table_for_payment.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransaksiTableForPayment extends Migration
{
    public function up()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('transaksi', 'NOMOR_TRANSAKSI')) {
                $table->string('NOMOR_TRANSAKSI', 20)->nullable()->after('ID_KERANJANG');
            }
            
            if (!Schema::hasColumn('transaksi', 'BATAS_WAKTU_PEMBAYARAN')) {
                $table->timestamp('BATAS_WAKTU_PEMBAYARAN')->nullable()->after('TOTAL_TRANSAKSI');
            }
            
            if (!Schema::hasColumn('transaksi', 'POIN_DITUKAR')) {
                $table->integer('POIN_DITUKAR')->default(0)->after('BATAS_WAKTU_PEMBAYARAN');
            }
            
            if (!Schema::hasColumn('transaksi', 'BUKTI_PEMBAYARAN')) {
                $table->string('BUKTI_PEMBAYARAN', 255)->nullable()->after('POIN_DITUKAR');
            }
            
            if (!Schema::hasColumn('transaksi', 'STATUS_VERIFIKASI')) {
                $table->enum('STATUS_VERIFIKASI', ['Pending', 'Valid', 'Invalid'])->default('Pending')->after('BUKTI_PEMBAYARAN');
            }
            
            if (!Schema::hasColumn('transaksi', 'TANGGAL_UPLOAD_BUKTI')) {
                $table->timestamp('TANGGAL_UPLOAD_BUKTI')->nullable()->after('STATUS_VERIFIKASI');
            }
            
            if (!Schema::hasColumn('transaksi', 'TANGGAL_VERIFIKASI')) {
                $table->timestamp('TANGGAL_VERIFIKASI')->nullable()->after('TANGGAL_UPLOAD_BUKTI');
            }
            
            if (!Schema::hasColumn('transaksi', 'VERIFIED_BY')) {
                $table->integer('VERIFIED_BY')->nullable()->after('TANGGAL_VERIFIKASI');
                $table->foreign('VERIFIED_BY')->references('ID_PEGAWAI')->on('pegawai')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('transaksi', 'CATATAN_VERIFIKASI')) {
                $table->text('CATATAN_VERIFIKASI')->nullable()->after('VERIFIED_BY');
            }
        });
    }

    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn([
                'NOMOR_TRANSAKSI',
                'BATAS_WAKTU_PEMBAYARAN', 
                'POIN_DITUKAR',
                'BUKTI_PEMBAYARAN',
                'STATUS_VERIFIKASI',
                'TANGGAL_UPLOAD_BUKTI',
                'TANGGAL_VERIFIKASI',
                'VERIFIED_BY',
                'CATATAN_VERIFIKASI'
            ]);
        });
    }
}