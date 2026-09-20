<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';

    protected $fillable = [
        'transaksi_id',
        'menu_id',
        'nama_menu_snapshot',
        'harga_satuan_snapshot',
        'cost_per_cup_snapshot',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan_snapshot' => 'decimal:2',
        'cost_per_cup_snapshot' => 'decimal:2',
        'qty' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function getTotalHppAttribute(): float
    {
        $costPerCup = $this->menu && (float)$this->menu->cost_per_cup > 0 
            ? (float)$this->menu->cost_per_cup 
            : (float)$this->cost_per_cup_snapshot;
        return $costPerCup * (int) $this->qty;
    }

    public function getCostPerCupEffectiveAttribute(): float
    {
        return $this->menu && (float)$this->menu->cost_per_cup > 0 
            ? (float)$this->menu->cost_per_cup 
            : (float)$this->cost_per_cup_snapshot;
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->subtotal - $this->total_hpp;
    }

    public function getMarginPersenAttribute(): float
    {
        if ((float) $this->subtotal <= 0) {
            return 0;
        }
        return round(($this->profit / (float) $this->subtotal) * 100, 1);
    }
}
