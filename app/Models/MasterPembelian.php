<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\IdGenerator;

class MasterPembelian extends Model
{
    use HasFactory;

    protected $table = 'master_pembelians';

    protected $fillable = [
        'purchase_code',
        'total',
        'date',
    ];

    // Menambahkan event untuk otomatis mengisi purchase_code saat pembuatan model baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($masterPembelian) {
            if (!$masterPembelian->purchase_code) {
                $masterPembelian->purchase_code = IdGenerator::generatePurchaseCode();
            }
        });
    }

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class, 'master_pembelians_id');
    }
}
