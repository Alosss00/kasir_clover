<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'nama_menu',
        'kategori',
        'gambar',
        'harga_jual',
        'biaya_lain',
        'cost_per_cup',
        'keuntungan_per_cup',
        'is_active',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'biaya_lain' => 'decimal:2',
        'cost_per_cup' => 'decimal:2',
        'keuntungan_per_cup' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function resep()
    {
        return $this->hasMany(Resep::class, 'menu_id')->with('bahanBaku');
    }

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'resep', 'menu_id', 'bahan_baku_id')
                    ->withPivot('id', 'jumlah_pemakaian')
                    ->withTimestamps();
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'menu_id');
    }

    public function calculateCost(): void
    {
        $this->loadMissing('resep.bahanBaku');

        $totalCostBahan = 0;
        foreach ($this->resep as $itemResep) {
            if ($itemResep->bahanBaku) {
                $costBahanIni = $itemResep->jumlah_pemakaian * $itemResep->bahanBaku->harga_per_satuan;
                $totalCostBahan += $costBahanIni;
            }
        }

        $costPerCup = $totalCostBahan + (float)$this->biaya_lain;
        $keuntunganPerCup = (float)$this->harga_jual - $costPerCup;

        $this->updateQuietly([
            'cost_per_cup' => round($costPerCup, 2),
            'keuntungan_per_cup' => round($keuntunganPerCup, 2),
        ]);
    }

    public function getMarginPersenAttribute(): float
    {
        if ($this->harga_jual <= 0) {
            return 0;
        }
        return round(($this->keuntungan_per_cup / $this->harga_jual) * 100, 1);
    }

    /**
     * Hitung estimasi sisa porsi/cup yang dapat dijual berdasarkan stok bahan baku terendah (bottleneck)
     */
    public function getAvailableStockAttribute(): ?int
    {
        // Pastikan relasi resep dan bahan baku sudah dimuat
        if (!$this->relationLoaded('resep')) {
            $this->load('resep.bahanBaku');
        }

        if ($this->resep->isEmpty()) {
            return null; // Menu tanpa takaran resep (unlimited)
        }

        $minCups = null;

        foreach ($this->resep as $item) {
            $bahan = $item->bahanBaku;
            if (!$bahan || (float)$item->jumlah_pemakaian <= 0) {
                continue;
            }

            $stokTersedia = (float)$bahan->stok_total;
            $butuhPerCup = (float)$item->jumlah_pemakaian;

            $cupsBahanIni = (int) floor($stokTersedia / $butuhPerCup);

            if ($minCups === null || $cupsBahanIni < $minCups) {
                $minCups = $cupsBahanIni;
            }
        }

        return $minCups !== null ? max(0, $minCups) : null;
    }
}
