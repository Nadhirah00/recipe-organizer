<?php

namespace App\Models;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingList extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'recipe_id',
        'ingredient',
        'quantity',
        'is_checked',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'week_start' => 'date',
        'is_checked' => 'boolean',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}