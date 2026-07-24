<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','category_id','title','slug','description','instructions',
        'cuisine_type','meal_type','difficulty','cooking_time','servings',
        'calories','protein','carbs','fats','fiber','sugar','sodium',
        'spice_level','is_vegan','is_vegetarian','is_halal','is_gluten_free','is_keto',
        'image_url','video_url','health_score','avg_rating','review_count','view_count',
        'is_ai_generated','is_featured','is_seasonal','season_tag',
        'source_api','external_id','allergens','estimated_cost',
    ];

    protected $casts = [
        'instructions'   => 'array',
        'allergens'      => 'array',
        'is_vegan'       => 'boolean',
        'is_vegetarian'  => 'boolean',
        'is_halal'       => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_keto'        => 'boolean',
        'is_ai_generated'=> 'boolean',
        'is_featured'    => 'boolean',
        'is_seasonal'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = Str::slug($recipe->title) . '-' . Str::random(5);
            }
        });
    }

    public function category()    { return $this->belongsTo(Category::class); }
    public function user()        { return $this->belongsTo(User::class); }
    public function reviews()     { return $this->hasMany(Review::class); }
    public function favorites()   { return $this->hasMany(Favorite::class); }
    public function ingredients() { return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')->withPivot('quantity','is_optional'); }
    public function mealPlanItems(){ return $this->hasMany(MealPlanItem::class); }
    public function collections() { return $this->belongsToMany(Collection::class, 'collection_recipes')->withPivot('sort_order'); }

    public function getImageAttribute(): string
    {
        return $this->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80';
    }

    public function getDifficultyBadgeAttribute(): string
    {
        return match($this->difficulty) {
            'beginner'     => '<span class="badge badge-beginner">Beginner</span>',
            'easy'         => '<span class="badge badge-easy">Easy</span>',
            'medium'       => '<span class="badge badge-medium">Medium</span>',
            'advanced'     => '<span class="badge badge-advanced">Advanced</span>',
            'professional' => '<span class="badge badge-pro">Professional</span>',
            default        => '',
        };
    }

    public function scopeFeatured($q)   { return $q->where('is_featured', true); }
    public function scopeSeasonal($q)   { return $q->where('is_seasonal', true); }
    public function scopeHalal($q)      { return $q->where('is_halal', true); }
    public function scopeAiGenerated($q){ return $q->where('is_ai_generated', true); }
    public function scopePopular($q)    { return $q->orderBy('view_count', 'desc'); }
    public function scopeTopRated($q)   { return $q->orderBy('avg_rating', 'desc'); }
}
