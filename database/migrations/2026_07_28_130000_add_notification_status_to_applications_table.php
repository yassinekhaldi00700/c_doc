<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Both null while the decision email is still queued/in flight.
            // notification_sent_at set on success; notification_error set
            // (and notification_sent_at left null) if it ultimately failed.
            $table->timestamp('notification_sent_at')->nullable()->after('reviewed_at');
            $table->text('notification_error')->nullable()->after('notification_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['notification_sent_at', 'notification_error']);
        });
    }
};
