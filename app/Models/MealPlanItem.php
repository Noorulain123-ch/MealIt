<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlanItem extends Model
{
    protected $fillable = ['meal_plan_id','recipe_id','ai_recipe','meal_date','meal_type','servings'];
    protected $casts = ['ai_recipe'=>'array','meal_date'=>'date'];

    public function mealPlan() { return $this->belongsTo(MealPlan::class); }
    public function recipe()   { return $this->belongsTo(Recipe::class); }
}
