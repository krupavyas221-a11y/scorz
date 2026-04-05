<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id', 20)->unique(); // e.g., Q2024-0001

            // ── Master data foreign keys ──────────────────────────────────────
            $table->foreignId('subject_id')
                  ->constrained('subjects')->restrictOnDelete();
            $table->foreignId('class_id')
                  ->nullable()
                  ->constrained('classes')->nullOnDelete();
            $table->foreignId('strand_id')
                  ->nullable()
                  ->constrained('strands')->nullOnDelete();
            $table->foreignId('skill_category_id')
                  ->nullable()
                  ->constrained('skill_categories')->nullOnDelete();
            $table->foreignId('test_type_id')
                  ->nullable()
                  ->constrained('test_types')->nullOnDelete();
            $table->foreignId('season_id')
                  ->nullable()
                  ->constrained('seasons')->nullOnDelete();
            $table->foreignId('test_level_id')      // difficulty
                  ->nullable()
                  ->constrained('test_levels')->nullOnDelete();

            // ── Test settings ─────────────────────────────────────────────────
            $table->unsignedSmallInteger('duration')->nullable();   // minutes
            $table->string('marking_scheme', 255)->nullable();

            // ── Question content ─────────────────────────────────────────────
            $table->enum('question_type', ['mcq'])->default('mcq');
            $table->text('question_text');
            $table->json('options');                // ["Opt A", "Opt B", ...]
            $table->unsignedTinyInteger('correct_answer'); // 0-based index

            // ── Scoring & status ─────────────────────────────────────────────
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes for common filters
            $table->index('subject_id',        'idx_q_subject');
            $table->index('class_id',          'idx_q_class');
            $table->index('strand_id',         'idx_q_strand');
            $table->index('is_active',         'idx_q_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
