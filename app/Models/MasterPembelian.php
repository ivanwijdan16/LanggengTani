<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPembelian extends Model
{
  use HasFactory;

  protected $table = 'master_pembelians';

  protected $fillable = [
    'total',
    'date',
  ];

  public function pembelians()
  {
    return $this->hasMany(Pembelian::class, 'master_pembelians_id'); // Adjust 'master_pembelian_id' to match your foreign key
  }
}
