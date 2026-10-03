<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = ['shopping_list_id','name','quantity','category','is_checked'];
    protected $casts = ['is_checked'=>'boolean'];

    public function shoppingList() { return $this->belongsTo(ShoppingList::class); }
}
