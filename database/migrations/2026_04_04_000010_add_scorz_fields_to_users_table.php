<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('scorz_admin')->default(false)->after('is_active')
                  ->comment('Whether the user has Scorz admin privileges');
            $table->boolean('scorz_access')->default(true)->after('scorz_admin')
                  ->comment('Whether the user has access to the Scorz platform');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['scorz_admin', 'scorz_access']);
        });
    }
};
