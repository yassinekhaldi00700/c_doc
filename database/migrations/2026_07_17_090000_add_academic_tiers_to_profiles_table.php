<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('degree_track')->nullable()->after('user_id');

            $table->string('license_institution')->nullable();
            $table->unsignedSmallInteger('license_graduation_year')->nullable();
            $table->string('license_field_of_study')->nullable();
            $table->string('license_grade_mention')->nullable();

            $table->string('bac_institution')->nullable();
            $table->unsignedSmallInteger('bac_graduation_year')->nullable();
            $table->string('bac_field_of_study')->nullable();
            $table->string('bac_grade_mention')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'degree_track',
                'license_institution',
                'license_graduation_year',
                'license_field_of_study',
                'license_grade_mention',
                'bac_institution',
                'bac_graduation_year',
                'bac_field_of_study',
                'bac_grade_mention',
            ]);
        });
    }
};
