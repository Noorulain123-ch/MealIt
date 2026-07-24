<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{
    protected $table = 'ai_generations';
    protected $fillable = ['user_id','type','input_data','output_data','model_used','success'];
    protected $casts = ['input_data'=>'array','output_data'=>'array','success'=>'boolean'];

    public function user() { return $this->belongsTo(User::class); }
}
