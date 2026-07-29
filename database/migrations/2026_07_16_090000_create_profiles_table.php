<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Personal information
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('cin_or_passport_number')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();

            // Academic background
            $table->string('last_degree')->nullable();
            $table->string('last_institution')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('field_of_study')->nullable();
            $table->string('grade_mention')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
