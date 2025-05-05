<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'purchase_price',
        'quantity',
        'purchase_date',
        'purchase_code',
        'master_pembelians_id'
    ];

    // Relasi dengan Stock - menggunakan withTrashed() untuk menampilkan data yang sudah dihapus
    public function stock()
    {
        return $this->belongsTo(Stock::class)->withTrashed();
    }

    // Relasi dengan MasterPembelian
    public function masterPembelian()
    {
        return $this->belongsTo(MasterPembelian::class, 'master_pembelians_id');
    }
}
