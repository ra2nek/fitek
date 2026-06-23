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
        Schema::create('diet_plans', function(Blueprint $table){
            $table->id();
            $table->date(column: 'date')->default(date('Y-m-d'));
            $table->string('diet', 100)->default("podstawowa");
            $table->integer('breakfast_id');
            $table->integer('brunch_id');
            $table->integer('lunch_id');
            $table->integer('snack_id');
            $table->integer('dinner_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
