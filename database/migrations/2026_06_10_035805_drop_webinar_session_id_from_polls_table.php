<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            if (Schema::hasColumn('polls', 'webinar_session_id')) {
                $table->dropForeign(['webinar_session_id']); // Drop foreign key if exists
                $table->dropColumn('webinar_session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->unsignedBigInteger('webinar_session_id')->nullable()->after('id');
            $table->foreign('webinar_session_id')->references('id')->on('webinar_sessions')->onDelete('cascade');
        });
    }
};