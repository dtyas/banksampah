<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_transaksi', 'sampah_id')) {
                $table->unsignedBigInteger('sampah_id')->after('id');
                $table->foreign('sampah_id')->references('id')->on('sampah')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->dropForeign(['sampah_id']);
            $table->dropColumn('sampah_id');
        });
    }
};
