<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('research_subjects')->cascadeOnDelete();
            $table->string('status')->default('pending');

            // Personal information
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('nationality');
            $table->string('gender', 10);
            $table->string('cin_or_passport_number');
            $table->string('address');
            $table->string('phone', 30);

            // Academic background
            $table->string('last_degree');
            $table->string('last_institution');
            $table->unsignedSmallInteger('graduation_year');
            $table->string('field_of_study');
            $table->string('grade_mention')->nullable();

            $table->text('motivation_summary')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_comment')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->unique(['candidate_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
