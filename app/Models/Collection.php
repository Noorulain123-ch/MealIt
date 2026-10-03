<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $fillable = ['user_id','name','description','is_public','share_token'];
    protected $casts = ['is_public'=>'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($c){
            $c->share_token = Str::random(32);
        });
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function recipes() { return $this->belongsToMany(Recipe::class, 'collection_recipes')->withPivot('sort_order'); }
}
