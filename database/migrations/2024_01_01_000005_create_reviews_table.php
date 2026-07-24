<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->integer('likes')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'recipe_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
