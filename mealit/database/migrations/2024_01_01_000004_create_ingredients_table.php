<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientsTable extends Migration
{
    public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable(); // Protein/Vegetable/Grain/Dairy/Spice
            $table->string('unit')->nullable();
            $table->string('allergen')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->timestamps();
            $table->index('name');
            $table->index('category');
        });

        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->string('quantity');
            $table->boolean('is_optional')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('ingredients');
    }
}
