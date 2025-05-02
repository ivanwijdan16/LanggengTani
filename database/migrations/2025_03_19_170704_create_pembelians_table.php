<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('pembelians', function (Blueprint $table) {
      $table->id();
      $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
      $table->decimal('purchase_price', 10, 2);
      $table->integer('quantity');
      $table->date('purchase_date');
      $table->string('purchase_code')->unique(); // ID Pembelian, misalnya PB-YYYYMMDD-XXX
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pembelians');
  }
};
