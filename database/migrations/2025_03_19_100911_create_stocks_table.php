<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('stocks', function (Blueprint $table) {
      $table->id();
      $table->string('master_stock_id');
      $table->enum('size', ['Kecil', 'Sedang', 'Besar']);
      $table->decimal('purchase_price', 10, 2);
      $table->decimal('selling_price', 10, 2);
      $table->integer('quantity');
      $table->date('expiration_date');
      $table->string('stock_id')->unique();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('stocks');
  }
};
