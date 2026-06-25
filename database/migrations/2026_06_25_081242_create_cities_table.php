<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->string('state_name');
            $table->string('pincode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('city_name');
            $table->index('state_name');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
};