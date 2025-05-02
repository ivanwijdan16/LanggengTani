<?php

// database/migrations/2025_03_19_000000_create_carts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
  public function up()
  {
    Schema::create('carts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi dengan tabel users
      $table->integer('product_id'); // Relasi dengan tabel products
      $table->integer('quantity');
      $table->double('subtotal');
      $table->string('type')->default('normal');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('carts');
  }
}
