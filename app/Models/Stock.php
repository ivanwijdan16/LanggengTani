<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
  use HasFactory, SoftDeletes; // Tambahkan SoftDeletes

  protected $fillable = [
    'size',
    'purchase_price',
    'selling_price',
    'quantity',
    'expiration_date',
    'stock_id',
    'retail_price',
    'retail_quantity',
    'master_stock_id',
  ];

  protected $dates = ['expiration_date', 'deleted_at'];

  public function masterStock()
  {
    return $this->belongsTo(MasterStock::class, 'master_stock_id');
  }

  // Generate Stock ID
  public static function generateStockId($name, $size, $expirationDate, $sub_type = null)
  {
    $shortName = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
    $sizeCode = strtoupper(substr($size, 0, 1));
    $dateCode = date('Ymd', strtotime($expirationDate));
    return $sub_type ? "{$shortName}-{$sub_type}-{$sizeCode}-{$dateCode}" : "{$shortName}-{$sizeCode}-{$dateCode}";
  }

  public function transactionItems()
  {
    return $this->hasMany(TransactionItem::class);
  }

  // Method to create a notification if stock is low, out of stock, or expiring
  public function createNotification($message)
  {
    Notification::create([
      'stock_id' => $this->id,
      'message' => $message,
      'read' => false, // Newly created notification is unread
    ]);
  }

  // Method to check if stock is low
  public function isLowStock()
  {
    return $this->quantity <= 3 && $this->quantity > 0;
  }

  // Method to check if stock is out
  public function isOutOfStock()
  {
    return $this->quantity <= 0;
  }

  // Method to check if stock is expiring soon (within 3 days)
  public function isExpiringSoon()
  {
    return Carbon::parse($this->expiration_date)->diffInDays(Carbon::now()) <= 3;
  }

  // Method to check if stock is expired
  public function isExpired()
  {
    return Carbon::parse($this->expiration_date)->isPast();
  }

  // Method to check stock status and create notifications accordingly
  public function checkAndCreateNotifications()
  {
    if ($this->isLowStock()) {
      $this->createNotification("Stok barang '{$this->name}' hampir habis! Sisa {$this->quantity} unit. Segera restock!");
    }

    if ($this->isOutOfStock()) {
      $this->createNotification("Stok barang '{$this->name}' Habis. Segera restock!");
    }

    if ($this->isExpiringSoon()) {
      $this->createNotification("{$this->name} akan kadaluwarsa dalam " . Carbon::parse($this->expiration_date)->diffInDays(Carbon::now()) . " hari! (Exp: {$this->expiration_date})");
    }

    if ($this->isExpired()) {
      $this->createNotification("{$this->name} sudah kadaluwarsa (Exp: {$this->expiration_date})");
    }
  }
}
