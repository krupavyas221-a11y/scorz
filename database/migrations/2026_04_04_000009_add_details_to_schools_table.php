<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('school_type', 50)->nullable()->after('name')
                  ->comment('e.g. primary, secondary, sixth_form');
            $table->string('region', 100)->nullable()->after('address');
            $table->enum('gender', ['girls', 'boys', 'mixed'])->nullable()->after('region');
            $table->string('phone', 30)->nullable()->after('gender');
            $table->string('fax', 30)->nullable()->after('phone');
            $table->string('principal_name')->nullable()->after('fax');
            $table->string('email', 191)->nullable()->after('principal_name');
            $table->string('website')->nullable()->after('email');
            $table->string('teacher_council_number', 50)->nullable()->after('website');
            $table->json('school_years')->nullable()->after('teacher_council_number')
                  ->comment('e.g. ["Year 7","Year 8","Year 9"]');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'school_type', 'region', 'gender', 'phone', 'fax',
                'principal_name', 'email', 'website',
                'teacher_council_number', 'school_years',
            ]);
        });
    }
};
