<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealPlansTable extends Migration
{
    public function up()
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('calorie_target')->nullable();
            $table->string('diet_type')->nullable();
            $table->boolean('is_ai_generated')->default(true);
            $table->timestamps();
        });

        Schema::create('meal_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->nullable()->constrained()->onDelete('set null');
            $table->json('ai_recipe')->nullable();
            $table->date('meal_date');
            $table->enum('meal_type', ['breakfast','lunch','dinner','snack']);
            $table->integer('servings')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_plan_items');
        Schema::dropIfExists('meal_plans');
    }
}
