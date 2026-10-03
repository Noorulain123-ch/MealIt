<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions'); // JSON array of steps
            $table->string('cuisine_type');
            $table->enum('meal_type', ['breakfast','lunch','dinner','snack','dessert']);
            $table->enum('difficulty', ['beginner','easy','medium','advanced','professional'])->default('medium');
            $table->integer('cooking_time'); // minutes
            $table->integer('servings')->default(2);
            $table->integer('calories')->nullable();
            $table->decimal('protein', 8, 2)->nullable();
            $table->decimal('carbs', 8, 2)->nullable();
            $table->decimal('fats', 8, 2)->nullable();
            $table->decimal('fiber', 8, 2)->nullable();
            $table->decimal('sugar', 8, 2)->nullable();
            $table->decimal('sodium', 8, 2)->nullable();
            $table->enum('spice_level', ['mild','medium','spicy','extra_spicy'])->default('medium');
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_halal')->default(true);
            $table->boolean('is_gluten_free')->default(false);
            $table->boolean('is_keto')->default(false);
            $table->string('image_url', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->tinyInteger('health_score')->nullable();
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_ai_generated')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_seasonal')->default(false);
            $table->string('season_tag')->nullable();
            $table->string('source_api')->nullable();
            $table->string('external_id')->nullable();
            $table->json('allergens')->nullable();
            $table->decimal('estimated_cost', 8, 2)->nullable();
            $table->timestamps();

            $table->index('cuisine_type');
            $table->index('meal_type');
            $table->index('difficulty');
            $table->index('calories');
            $table->index('is_halal');
            $table->index('is_featured');
            $table->fullText(['title', 'description']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
