<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBakuHistori extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku_histori';

    protected $fillable = [
        'bahan_baku_id',
        'jumlah_ditambahkan',
        'harga_beli',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_ditambahkan' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}
