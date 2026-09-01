<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_difficulty',
        'max_cooking_time',
        'preferred_categories',
        'preferred_meal_types',
        'additional_preferences',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}