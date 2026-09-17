<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('transaksi') && !Schema::hasColumn('transaksi', 'nama_customer')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->string('nama_customer')->nullable()->default('Pelanggan Umum')->after('kode_transaksi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksi') && Schema::hasColumn('transaksi', 'nama_customer')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('nama_customer');
            });
        }
    }
};
