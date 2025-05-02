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
    Schema::table('stocks', function (Blueprint $table) {
      // Menambahkan kolom retail_quantity untuk jumlah eceran per unit
      $table->integer('retail_quantity')->nullable()->after('selling_price');

      // Menambahkan kolom retail_price untuk harga jual per eceran
      $table->decimal('retail_price', 10, 2)->nullable()->after('retail_quantity');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('stocks', function (Blueprint $table) {
      $table->dropColumn(['retail_quantity', 'retail_price']);
    });
  }
};
