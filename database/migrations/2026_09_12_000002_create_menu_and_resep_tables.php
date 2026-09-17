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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu');
            $table->string('kategori')->default('Minuman');
            $table->string('gambar')->nullable();
            $table->decimal('harga_jual', 12, 2);
            $table->decimal('biaya_lain', 12, 2)->default(0);
            $table->decimal('cost_per_cup', 12, 2)->default(0);
            $table->decimal('keuntungan_per_cup', 12, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('jumlah_pemakaian', 12, 2);
            $table->timestamps();

            $table->unique(['menu_id', 'bahan_baku_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
        Schema::dropIfExists('menu');
    }
};
