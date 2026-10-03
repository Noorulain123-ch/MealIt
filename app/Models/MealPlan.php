<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    protected $fillable = ['user_id','name','start_date','end_date','calorie_target','diet_type','is_ai_generated'];

    protected $casts = ['start_date'=>'date','end_date'=>'date','is_ai_generated'=>'boolean'];

    public function user()  { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(MealPlanItem::class); }
}
