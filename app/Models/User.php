<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'bio', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function recipes() { return $this->hasMany(Recipe::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function favoriteRecipes() { return $this->belongsToMany(Recipe::class, 'favorites'); }
    public function mealPlans() { return $this->hasMany(MealPlan::class); }
    public function shoppingLists() { return $this->hasMany(ShoppingList::class); }
    public function collections() { return $this->hasMany(Collection::class); }
    public function preferences() { return $this->hasOne(UserPreference::class); }
    public function searchHistory() { return $this->hasMany(SearchHistory::class); }
    public function aiGenerations() { return $this->hasMany(AiGeneration::class); }
    public function notifications() { return $this->hasMany(Notification::class); }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=E85D04&color=fff&size=128';
    }
}
