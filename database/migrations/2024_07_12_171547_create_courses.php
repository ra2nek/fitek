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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('video_url');
            $table->longText('thumbnail_url');
            $table->string('type');
            $table->string('category');
            $table->string('tags')->nullable();
            $table->string('level');
            $table->integer('time')->default(15);
            $table->unsignedBigInteger('views')->default(0)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('likes')->default(0)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
