<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->json('preferred_cuisines')->nullable();
            $table->json('dietary_flags')->nullable();
            $table->enum('spice_level', ['mild','medium','spicy','extra_spicy'])->default('medium');
            $table->enum('skill_level', ['beginner','easy','medium','advanced','professional'])->default('easy');
            $table->integer('calorie_target')->nullable();
            $table->enum('health_goal', ['weight_loss','muscle_gain','maintenance'])->default('maintenance');
            $table->json('allergies')->nullable();
            $table->boolean('dark_mode')->default(false);
            $table->integer('height')->nullable(); // cm
            $table->integer('weight')->nullable(); // kg
            $table->integer('age')->nullable();
            $table->string('activity_level')->nullable();
            $table->timestamps();
        });

        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('query');
            $table->string('type')->default('keyword'); // keyword | ingredient | ai
            $table->timestamps();
        });

        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // recipe | meal_plan | chat | substitute
            $table->json('input_data');
            $table->json('output_data')->nullable();
            $table->string('model_used')->nullable();
            $table->boolean('success')->default(true);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('link', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('ai_generations');
        Schema::dropIfExists('search_history');
        Schema::dropIfExists('user_preferences');
    }
}
