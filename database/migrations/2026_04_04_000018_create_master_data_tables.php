<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subjects — English, Mathematics, Science, etc.
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Strands — Number, Algebra, Data, Comprehension, etc.
        Schema::create('strands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Skill Categories — Understanding, Problem-Solving, Computation, etc.
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Test Types — SIGMA-T, MICRA-T, NEW MICRA-T, etc.
        Schema::create('test_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seasons — Autumn, Spring, Summer, etc.
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Test Levels — Easy, Medium, Hard, etc.
        Schema::create('test_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_levels');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('test_types');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('strands');
        Schema::dropIfExists('subjects');
    }
};
