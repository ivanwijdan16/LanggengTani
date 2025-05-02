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
    Schema::table('pembelians', function (Blueprint $table) {
      $table->string('master_pembelians_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('pembelians', function (Blueprint $table) {
      $table->dropColumn('master_pembelians_id');
    });
  }
};
