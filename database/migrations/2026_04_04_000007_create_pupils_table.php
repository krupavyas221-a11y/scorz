<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pupils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->string('pupil_id', 50)->comment('Human-readable ID e.g. 2024-001');
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('year_group', 20)->nullable();
            $table->string('pin')->comment('bcrypt hash of 5-digit PIN');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->unique(['pupil_id', 'school_id'], 'uq_pupils_pupil_id_school');
            $table->index('pupil_id', 'idx_pupils_pupil_id');
            $table->index('is_active', 'idx_pupils_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pupils');
    }
};
