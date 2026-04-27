<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('transaksi_id');
            $table->foreign('nasabah_id')->references('id')->on('nasabah')->nullOnDelete();
        });

        // Back-fill nasabah_id dari relasi transaksi → nasabah
        DB::statement('
            UPDATE pembayaran p
            JOIN transaksi t ON t.id = p.transaksi_id
            SET p.nasabah_id = t.nasabah_id
            WHERE p.nasabah_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['nasabah_id']);
            $table->dropColumn('nasabah_id');
        });
    }
};
