<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','icon','image_url','description','type','is_active'];

    public function recipes() { return $this->hasMany(Recipe::class); }
}
