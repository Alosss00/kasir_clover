<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'resep';

    protected $fillable = [
        'menu_id',
        'bahan_baku_id',
        'jumlah_pemakaian',
    ];

    protected $casts = [
        'jumlah_pemakaian' => 'decimal:2',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    public function getSubtotalCostAttribute(): float
    {
        if ($this->bahanBaku) {
            return (float) ($this->jumlah_pemakaian * $this->bahanBaku->harga_per_satuan);
        }
        return 0;
    }
}
