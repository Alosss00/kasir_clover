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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
            $table->decimal('total_harga', 12, 2);
            $table->enum('metode_pembayaran', ['cash', 'debit_qris']);
            $table->decimal('jumlah_bayar', 12, 2)->nullable();
            $table->decimal('kembalian', 12, 2)->nullable();
            $table->dateTime('tanggal_transaksi')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->cascadeOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained('menu')->nullOnDelete();
            $table->string('nama_menu_snapshot');
            $table->decimal('harga_satuan_snapshot', 12, 2);
            $table->decimal('cost_per_cup_snapshot', 12, 2)->default(0);
            $table->integer('qty');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
    }
};
