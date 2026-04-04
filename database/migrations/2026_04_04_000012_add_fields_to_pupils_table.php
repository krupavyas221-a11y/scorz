<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pupils', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('school_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('class_name', 30)->nullable()->after('year_group');
            $table->foreignId('teacher_id')->nullable()->after('class_name')
                  ->constrained('users')->nullOnDelete();
            $table->boolean('include_in_averages')->default(true)->after('teacher_id');
            $table->enum('sen', ['none', 'sen_support', 'ehc_plan'])->default('none')->after('include_in_averages')
                  ->comment('Special Educational Needs: none, sen_support, ehc_plan');
        });
    }

    public function down(): void
    {
        Schema::table('pupils', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['first_name', 'last_name', 'class_name', 'teacher_id', 'include_in_averages', 'sen']);
        });
    }
};
