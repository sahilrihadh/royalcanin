<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('login_details', function (Blueprint $table) {
            $table->string('city')->nullable()->after('user_id');
            $table->index('city'); // Add index for faster queries
        });
    }

    public function down()
    {
        Schema::table('login_details', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};