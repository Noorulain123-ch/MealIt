<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id','preferred_cuisines','dietary_flags','spice_level','skill_level',
        'calorie_target','health_goal','allergies','dark_mode','height','weight','age','activity_level'
    ];
    protected $casts = [
        'preferred_cuisines'=>'array','dietary_flags'=>'array',
        'allergies'=>'array','dark_mode'=>'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
