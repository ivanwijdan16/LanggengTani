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
      // Mengubah kolom 'size' dari enum menjadi text
      $table->text('size')->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('stocks', function (Blueprint $table) {
      // Mengembalikan kolom 'size' ke enum (misalnya enum yang sebelumnya)
      $table->enum('size', ['Kecil', 'Sedang', 'Besar'])->change();
    });
  }
};
