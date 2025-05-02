<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_master_stocks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterStocksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('master_stocks', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('sku');
      $table->string('image')->nullable();
      $table->text('description')->nullable();
      $table->enum('type', ['Obat', 'Pupuk', 'Bibit']);
      $table->string('sub_type')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('master_stocks');
  }
}
