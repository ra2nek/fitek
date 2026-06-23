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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('role')->default('user');

            //! REST OF DATA
            $table->JSON('courses')->default('
{   
    "RecentlyWatched": [],
    "Liked": [],
    "ToWatch": []
}
            ');

            $table->JSON('meals')->default('
{   
    "RecentlyWatched": [],
    "Liked": [],
    "ToWatch": []
}
                        ');

            $table->JSON('data')->default('
{
    "age": null,
    "gender": null,
    "height": null,
    "weight": null,
    "activity_level": null,
    "work_type": null,
    "goal": null,
    "diet": null
}  
            ')->nullable();

            $table->JSON('diet')->default('
{
    "PPM": null,
    "PAL": null,
    "BMI": null,
    "calorie_intake": null           
}
            ')->nullable();
            
            $table->boolean('diet_form_filled')->default(0);

            $table->rememberToken();
            $table->timestamps();

            

            //? UNUSED SUBSCRIPTIONS COLUMNS
            // $table->string('subscription_type', 45)->nullable();
            // $table->timestamp('subscription_end_at')->nullable();
            // $table->string('subscription_next', 45)->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
