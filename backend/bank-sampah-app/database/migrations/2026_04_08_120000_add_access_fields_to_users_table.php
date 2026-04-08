<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('status')->default('Aktif')->after('role');
            $table->json('menu_access')->nullable()->after('status');
            $table->json('operational_access')->nullable()->after('menu_access');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['status', 'menu_access', 'operational_access']);
        });
    }
};
