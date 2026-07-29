<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('research_subjects', function (Blueprint $table) {
            $table->text('responsibilities')->nullable()->after('description');
            $table->text('candidate_profile')->nullable()->after('responsibilities');
        });

        DB::table('research_subjects')->update([
            'responsibilities' => DB::raw('requirements'),
        ]);

        Schema::table('research_subjects', function (Blueprint $table) {
            $table->dropColumn('requirements');
        });
    }

    public function down(): void
    {
        Schema::table('research_subjects', function (Blueprint $table) {
            $table->text('requirements')->nullable()->after('description');
        });

        DB::table('research_subjects')->update([
            'requirements' => DB::raw('responsibilities'),
        ]);

        Schema::table('research_subjects', function (Blueprint $table) {
            $table->dropColumn(['responsibilities', 'candidate_profile']);
        });
    }
};
