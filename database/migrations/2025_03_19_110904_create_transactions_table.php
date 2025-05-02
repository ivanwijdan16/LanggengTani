<?php

// database/migrations/2025_03_19_000001_create_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
  public function up()
  {
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi dengan tabel users
      $table->double('total_price');
      $table->double('total_paid');
      $table->double('change');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('transactions');
  }
}
