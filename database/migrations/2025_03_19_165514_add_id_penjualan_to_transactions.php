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
    Schema::table('transactions', function (Blueprint $table) {
      // Menambahkan kolom id_penjualan
      $table->string('id_penjualan')->unique()->nullable(); // Bisa kosong (nullable) dan unik
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('transactions', function (Blueprint $table) {
      // Menghapus kolom id_penjualan jika rollback
      $table->dropColumn('id_penjualan');
    });
  }
};
