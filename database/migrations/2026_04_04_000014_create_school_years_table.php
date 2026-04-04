<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 20)->unique(); // e.g., 2023-2024
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active', 'idx_school_years_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
