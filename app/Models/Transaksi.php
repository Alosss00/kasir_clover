<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'nama_customer',
        'kasir_id',
        'total_harga',
        'metode_pembayaran',
        'jumlah_bayar',
        'kembalian',
        'tanggal_transaksi',
        'catatan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'tanggal_transaksi' => 'datetime',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function getTotalHppAttribute(): float
    {
        return (float) $this->details->sum(function ($d) {
            return (float) $d->cost_per_cup_snapshot * (int) $d->qty;
        });
    }

    public function getTotalProfitAttribute(): float
    {
        return (float) $this->total_harga - $this->total_hpp;
    }

    public function getMarginPersenAttribute(): float
    {
        if ((float)$this->total_harga <= 0) {
            return 0;
        }
        return round(($this->total_profit / (float)$this->total_harga) * 100, 1);
    }

    public static function generateKodeTransaksi(): string
    {
        $todayPrefix = 'TRX-' . date('Ymd') . '-';
        $lastTrx = self::where('kode_transaksi', 'LIKE', $todayPrefix . '%')
                       ->orderBy('id', 'desc')
                       ->first();

        if ($lastTrx) {
            $lastNumber = (int) substr($lastTrx->kode_transaksi, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $todayPrefix . $nextNumber;
    }
}
