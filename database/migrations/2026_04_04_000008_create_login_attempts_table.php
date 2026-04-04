<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->enum('guard', ['superadmin', 'web', 'pupil']);
            $table->string('identifier')->comment('email or pupil_id');
            $table->string('ip_address', 45);
            $table->boolean('was_successful')->default(false);
            $table->tinyInteger('step')->default(1)->comment('1=password, 2=PIN');
            $table->timestamp('attempted_at')->useCurrent();

            $table->index(['identifier', 'guard'], 'idx_la_identifier_guard');
            $table->index(['ip_address', 'attempted_at'], 'idx_la_ip_attempted');
            $table->index('attempted_at', 'idx_la_attempted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
