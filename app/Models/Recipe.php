<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MealPlan;
use App\Models\User;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'cooking_time',
        'difficulty',
        'servings',
        'ingredients',
        'instructions',
        'image',
        'tutorial_url',
        'is_public',
    ];

    protected $casts = ['is_public' => 'boolean',];

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }



    public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
