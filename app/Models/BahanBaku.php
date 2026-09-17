<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok_total',
        'total_harga_beli',
        'harga_per_satuan',
        'stok_minimum',
    ];

    protected $casts = [
        'stok_total' => 'decimal:2',
        'total_harga_beli' => 'decimal:2',
        'harga_per_satuan' => 'decimal:4',
        'stok_minimum' => 'decimal:2',
    ];

    public function histori()
    {
        return $this->hasMany(BahanBakuHistori::class, 'bahan_baku_id')->orderBy('created_at', 'desc');
    }

    public function resep()
    {
        return $this->hasMany(Resep::class, 'bahan_baku_id');
    }

    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'resep', 'bahan_baku_id', 'menu_id')
                    ->withPivot('jumlah_pemakaian')
                    ->withTimestamps();
    }

    public function isStokMenipis(): bool
    {
        return $this->stok_total <= $this->stok_minimum;
    }

    public function getTotalNilaiStokAttribute(): float
    {
        return (float) ($this->stok_total * $this->harga_per_satuan);
    }
}
