<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every research subject is now a single opening — there is no longer a
     * configurable number of slots per subject.
     */
    public function up(): void
    {
        Schema::table('research_subjects', function (Blueprint $table) {
            $table->dropColumn('slots');
        });
    }

    public function down(): void
    {
        Schema::table('research_subjects', function (Blueprint $table) {
            $table->unsignedSmallInteger('slots')->default(1);
        });
    }
};
