<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = ['user_id','meal_plan_id','name'];

    public function user()    { return $this->belongsTo(User::class); }
    public function items()   { return $this->hasMany(ShoppingListItem::class); }
    public function mealPlan(){ return $this->belongsTo(MealPlan::class); }
}
