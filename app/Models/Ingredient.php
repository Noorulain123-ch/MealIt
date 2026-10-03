<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name','category','unit','allergen','image_url'];

    public function recipes() { return $this->belongsToMany(Recipe::class, 'recipe_ingredients')->withPivot('quantity','is_optional'); }
}
