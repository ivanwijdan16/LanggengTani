<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStock extends Model
{
  use HasFactory;

  protected $table = 'master_stocks';

  protected $fillable = [
    'name',
    'sku',
    'image',
    'description',
    'type',
    'sub_type',
  ];

  public function stocks()
  {
    return $this->hasMany(Stock::class, 'master_stock_id');
  }
}
