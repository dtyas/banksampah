<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nasabah', function (Blueprint $table): void {
            $table->string('payout_channel', 50)->nullable()->after('no_hp');
            $table->string('account_number', 100)->nullable()->after('payout_channel');
            $table->string('account_holder_name', 100)->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table): void {
            $table->dropColumn(['payout_channel', 'account_number', 'account_holder_name']);
        });
    }
};
