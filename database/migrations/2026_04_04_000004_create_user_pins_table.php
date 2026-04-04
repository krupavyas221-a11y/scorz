<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('pin')->comment('bcrypt hash of 5-digit PIN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pins');
    }
};
