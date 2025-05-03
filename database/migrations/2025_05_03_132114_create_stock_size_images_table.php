<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_size_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_stock_id');
            $table->string('size');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('master_stock_id')
                  ->references('id')
                  ->on('master_stocks')
                  ->onDelete('cascade');

            // Each master_stock_id and size combination should be unique
            $table->unique(['master_stock_id', 'size']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_size_images');
    }
};
