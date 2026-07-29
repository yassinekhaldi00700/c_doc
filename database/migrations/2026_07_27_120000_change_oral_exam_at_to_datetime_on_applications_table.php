<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Swapped from date to dateTime so the admin can also set the
        // exam's hour, not just the day — done as drop+re-add (rather than
        // ->change()) to avoid a doctrine/dbal dependency for a plain
        // column-type swap.
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('oral_exam_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dateTime('oral_exam_at')->nullable()->after('review_comment');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('oral_exam_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->date('oral_exam_at')->nullable()->after('review_comment');
        });
    }
};
