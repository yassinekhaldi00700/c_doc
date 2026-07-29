<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This data now lives on the `profiles` table (see the preceding backfill
     * migration), reused across every application a candidate submits.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'birth_date',
                'birth_place',
                'nationality',
                'gender',
                'cin_or_passport_number',
                'address',
                'phone',
                'last_degree',
                'last_institution',
                'graduation_year',
                'field_of_study',
                'grade_mention',
            ]);
        });
    }

    /**
     * Restores column shape only — the original data now lives on `profiles`
     * and is not moved back.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('cin_or_passport_number')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('last_degree')->nullable();
            $table->string('last_institution')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('field_of_study')->nullable();
            $table->string('grade_mention')->nullable();
        });
    }
};
