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
        Schema::create('meals', function(Blueprint $table){
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('thumbnail')->nullable();
            $table->string('meal_time')->default('breakfast');
            $table->integer('calories')->default(0);
            $table->json('suitable_diets')->default('
    []
            ');
            $table->json('ingredients')->default('
    []
            ');
            $table->json('preparation')->default('
    []
            ');
            $table->unsignedInteger('likes');
            $table->unsignedInteger('views');
            $table->integer('time');
            $table->string('difficulty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
