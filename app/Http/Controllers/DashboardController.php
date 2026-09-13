<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
    $user = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    $today = now()->toDateString();

    $startOfWeek = now()->startOfWeek();

    $endOfWeek = $startOfWeek->copy()->addDays(6);


    /*
    |--------------------------------------------------------------------------
    | Recipe Count
    |--------------------------------------------------------------------------
    */

    $recipeCount = Recipe::where(
        'user_id',
        $user->id
    )->count();


    /*
    |--------------------------------------------------------------------------
    | Favorite Count
    |--------------------------------------------------------------------------
    */

    $favoriteCount = Favorite::where(
        'user_id',
        $user->id
    )->count();


    /*
    |--------------------------------------------------------------------------
    | Meal Plan Count - Current Week
    |--------------------------------------------------------------------------
    */

    $mealPlanCount = MealPlan::where(
        'user_id',
        $user->id
    )
    ->whereBetween('date', [
        $startOfWeek->toDateString(),
        $endOfWeek->toDateString(),
    ])
    ->count();


    /*
    |--------------------------------------------------------------------------
    | Today's Meals
    |--------------------------------------------------------------------------
    */

    $todayMeals = MealPlan::with('recipe')
        ->where('user_id', $user->id)
        ->whereDate('date', $today)
        ->orderByRaw("
            CASE meal_type
                WHEN 'Breakfast' THEN 1
                WHEN 'Lunch' THEN 2
                WHEN 'Dinner' THEN 3
                ELSE 4
            END
        ")
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Weekly Meal Progress
    |
    | 7 days × 3 meal types = 21 possible slots
    |--------------------------------------------------------------------------
    */

    $totalWeeklySlots = 21;

    $weeklyProgress = $totalWeeklySlots > 0
        ? round(
            ($mealPlanCount / $totalWeeklySlots) * 100
        )
        : 0;


    /*
    |--------------------------------------------------------------------------
    | Shopping List
    |--------------------------------------------------------------------------
    |
    | Keep your existing shopping-list calculation here if you
    | already have one in your controller.
    |
    */

    $startOfWeek = now()->startOfWeek()->toDateString();

    $shoppingItems = ShoppingList::where('user_id', auth()->id())
        ->whereDate('week_start', $startOfWeek)
        ->get();

    $shoppingTotal = $shoppingItems->count();

    $shoppingCompleted = $shoppingItems
        ->where('is_checked', true)
        ->count();


    /*
    |--------------------------------------------------------------------------
    | Recent Recipes
    |--------------------------------------------------------------------------
    */

    $recentRecipes = Recipe::where(
        'user_id',
        $user->id
    )
    ->latest()
    ->limit(3)
    ->get();


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    return view(
        'dashboard.index',
        compact(
            'recipeCount',
            'favoriteCount',
            'mealPlanCount',
            'todayMeals',
            'totalWeeklySlots',
            'weeklyProgress',
            'shoppingTotal',
            'shoppingCompleted',
            'recentRecipes'
        )
    );

    }

}