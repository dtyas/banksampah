<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nasabah', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->timestamp('verified_at')->nullable()->after('tanggal');
            $table->foreignId('verified_by')
                ->nullable()
                ->after('verified_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn('verified_at');
        });

        Schema::table('nasabah', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
