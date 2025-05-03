<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MariaDB, kita perlu menggunakan pendekatan berbeda:
        // 1. Tambahkan kolom baru dengan tipe data yang benar
        Schema::table('pembelians', function (Blueprint $table) {
            $table->unsignedBigInteger('temp_master_pembelians_id')->nullable()->after('master_pembelians_id');
        });

        // 2. Salin data dari kolom lama ke kolom baru
        DB::statement('UPDATE pembelians SET temp_master_pembelians_id = CAST(master_pembelians_id AS UNSIGNED)');

        // 3. Hapus kolom lama
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('master_pembelians_id');
        });

        // 4. Tambahkan kolom baru dengan nama yang benar
        Schema::table('pembelians', function (Blueprint $table) {
            $table->unsignedBigInteger('master_pembelians_id')->nullable()->after('temp_master_pembelians_id');
        });

        // 5. Salin data dari kolom temp ke kolom yang baru
        DB::statement('UPDATE pembelians SET master_pembelians_id = temp_master_pembelians_id');

        // 6. Hapus kolom temporary
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('temp_master_pembelians_id');
        });

        // 7. Tambahkan foreign key constraint jika perlu
        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreign('master_pembelians_id')
                  ->references('id')
                  ->on('master_pembelians')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus foreign key constraint terlebih dahulu
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['master_pembelians_id']);
        });

        // Tambah kolom temporary dengan tipe string
        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('temp_master_pembelians_id')->nullable()->after('master_pembelians_id');
        });

        // Salin data
        DB::statement('UPDATE pembelians SET temp_master_pembelians_id = CAST(master_pembelians_id AS CHAR)');

        // Hapus kolom lama
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('master_pembelians_id');
        });

        // Buat kolom baru dengan nama yang sama tapi tipe string
        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('master_pembelians_id')->nullable()->after('temp_master_pembelians_id');
        });

        // Salin data dari temporary
        DB::statement('UPDATE pembelians SET master_pembelians_id = temp_master_pembelians_id');

        // Hapus kolom temporary
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropColumn('temp_master_pembelians_id');
        });
    }
};
