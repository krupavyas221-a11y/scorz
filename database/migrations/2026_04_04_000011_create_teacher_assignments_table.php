<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('school_year', 30)->comment('e.g. Year 7');
            $table->string('class_name', 30)->comment('e.g. 7A');
            $table->timestamps();

            $table->unique(['user_id', 'school_id', 'school_year', 'class_name'], 'uq_teacher_assignment');
            $table->index('school_id', 'idx_ta_school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
