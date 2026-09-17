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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan')->index();
            $table->enum('satuan', ['gr', 'ml']);
            $table->decimal('stok_total', 12, 2)->default(0);
            $table->decimal('total_harga_beli', 12, 2)->default(0);
            $table->decimal('harga_per_satuan', 12, 4)->default(0);
            $table->decimal('stok_minimum', 12, 2)->default(100.00);
            $table->timestamps();
        });

        Schema::create('bahan_baku_histori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('jumlah_ditambahkan', 12, 2);
            $table->decimal('harga_beli', 12, 2);
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku_histori');
        Schema::dropIfExists('bahan_baku');
    }
};
