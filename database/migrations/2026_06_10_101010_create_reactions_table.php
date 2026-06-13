<?php
// database/migrations/xxxx_create_reactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reaction_type'); // love, like, applause
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index('reaction_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reactions');
    }
};