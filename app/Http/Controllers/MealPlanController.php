<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\ShoppingList;
use Carbon\Carbon;

class MealPlanController extends Controller
{
    public function index(Request $request)
    {
        $recipes = Recipe::where('user_id', Auth::id())
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Selected Week
        |--------------------------------------------------------------------------
        */

        if ($request->filled('week')) {
            $startOfWeek = \Carbon\Carbon::parse($request->week)
                ->startOfWeek();
        } else {
            $startOfWeek = now()->startOfWeek();
        }

        $endOfWeek = $startOfWeek->copy()->addDays(6);


        /*
        |--------------------------------------------------------------------------
        | Create 7 dates
        |--------------------------------------------------------------------------
        */

        $dates = collect();

        for ($i = 0; $i < 7; $i++) {

            $dates->push(
                $startOfWeek->copy()->addDays($i)
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Get meals for selected week
        |--------------------------------------------------------------------------
        */

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Easy lookup
        |--------------------------------------------------------------------------
        */

        $mealPlanMap = $mealPlans->keyBy(function ($meal) {

            return $meal->date->format('Y-m-d')
                . '_' .
                $meal->meal_type;

        });


        return view('meal-plans.index', compact(
            'recipes',
            'mealPlans',
            'mealPlanMap',
            'dates',
            'startOfWeek',
            'endOfWeek'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|integer|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner',
        ]);

        $recipe = Recipe::where('id', $validated['recipe_id'])
        ->where(function ($q) {

            $q->where(
                'user_id',
                Auth::id()
            )
            ->orWhere(
                'is_public',
                true
            );

        })
        ->firstOrFail();

        // Check whether this date + meal already has a recipe

        $existingMeal = MealPlan::where('user_id', Auth::id())
            ->where('date', $validated['date'])
            ->where('meal_type', $validated['meal_type'])
            ->first();


        if ($existingMeal) {

            $existingMeal->update([
                'recipe_id' => $recipe->id,
            ]);

            $this->syncShoppingList(
                Carbon::parse($validated['date'])->startOfWeek()
            );

            return redirect(
                '/meal-planner?week=' .
                \Carbon\Carbon::parse($validated['date'])
                    ->startOfWeek()
                    ->format('Y-m-d')
            )->with(
                'success',
                'Meal updated successfully!'
            );
        }


        MealPlan::create([
            'user_id' => Auth::id(),
            'recipe_id' => $recipe->id,
            'date' => $validated['date'],
            'meal_type' => $validated['meal_type'],
        ]);

        $this->syncShoppingList(
            Carbon::parse($validated['date'])->startOfWeek()
        );

        return redirect(
            '/meal-planner?week=' .
            \Carbon\Carbon::parse($validated['date'])
                ->startOfWeek()
                ->format('Y-m-d')
        )->with(
            'success',
            'Meal added to your plan!');
    }

    public function destroy($id)
    {
        $mealPlan = MealPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        $startOfWeek = Carbon::parse(
            $mealPlan->date
        )->startOfWeek();

        $mealPlan->delete();

        $this->syncShoppingList(
            $startOfWeek
        );

        return redirect('/meal-planner?week=' .
            $startOfWeek->format('Y-m-d')
        )->with('success', 'Meal removed from your plan.');
    }

    public function clear(Request $request)
    {
        $startOfWeek = $request->filled('week')
            ? \Carbon\Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->addDays(6);

        MealPlan::where('user_id', Auth::id())
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->delete();

        ShoppingList::where(
            'user_id',
            Auth::id()
        )->whereDate('week_start', $startOfWeek->toDateString()
        )->delete();

        return redirect(
            '/meal-planner?week=' . $startOfWeek->format('Y-m-d')
        )->with(
            'success',
            'All meals for the selected week have been cleared.'
        );
    }

    public function searchRecipes(Request $request)
    {
        $query = Recipe::where(function ($q) {
            $q->where('user_id', Auth::id())
            ->orWhere('is_public', true);
        });

        $mealType = $request->input('meal_type');
        $currentRecipeId = $request->input('current_recipe_id');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('ingredients', 'like', '%' . $search . '%');

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Difficulty filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('difficulty')) {

            $query->where(
                'difficulty',
                $request->difficulty
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Cooking time filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('max_time')) {

            $query->where(
                'cooking_time',
                '<=',
                $request->max_time
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Favorites
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('favorites')) {

            $favoriteRecipeIds = Favorite::where(
                'user_id',
                Auth::id()
            )->pluck('recipe_id');

            $query->whereIn(
                'id',
                $favoriteRecipeIds
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Exclude current recipe when changing
        |--------------------------------------------------------------------------
        */

        if ($currentRecipeId) {

            $query->where(
                'id',
                '!=',
                $currentRecipeId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Prioritize recipes matching the meal type
        |--------------------------------------------------------------------------
        */

        if ($mealType) {

            $query->orderByRaw(
                "CASE
                    WHEN category = ? THEN 0
                    ELSE 1
                END",
                [$mealType]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Only 5 recipes when opening modal
        |--------------------------------------------------------------------------
        */

        $hasFilters =
            $request->filled('search') ||
            $request->filled('category') ||
            $request->filled('difficulty') ||
            $request->filled('max_time') ||
            $request->boolean('favorites');


        if (!$hasFilters) {

            $recipes = $query
                ->latest()
                ->limit(5)
                ->get();

        } else {

            $recipes = $query
                ->orderBy('name')
                ->limit(12)
                ->get();
        }


        return response()->json([
            'data' => $recipes,
            'total' => $recipes->count(),
        ]);
    }

    private function syncShoppingList(Carbon $startOfWeek): void
    {
        $userId = Auth::id();

        $endOfWeek = $startOfWeek->copy()->addDays(6);


        /*
        |--------------------------------------------------------------------------
        | Get current meals for this week
        |--------------------------------------------------------------------------
        */

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', $userId)
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Get existing shopping list
        |--------------------------------------------------------------------------
        */

        $existingItems = ShoppingList::where(
            'user_id',
            $userId
        )
        ->whereDate(
            'week_start',
            $startOfWeek->toDateString()
        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Build checked-item lookup
        |--------------------------------------------------------------------------
        |
        | The key is recipe + ingredient.
        | Therefore, if the same recipe appears on another day,
        | its checked state can be preserved.
        |
        */

        $checkedItems = [];

        foreach ($existingItems as $item) {

            if ($item->is_checked) {

                $key =
                    $item->recipe_id .
                    '|' .
                    strtolower(trim($item->ingredient));

                $checkedItems[$key] = true;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Do nothing if a shopping list has never been generated
        |--------------------------------------------------------------------------
        */

        if ($existingItems->isEmpty()) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Rebuild the selected week's shopping list
        |--------------------------------------------------------------------------
        */

        ShoppingList::where(
            'user_id',
            $userId
        )
        ->whereDate(
            'week_start',
            $startOfWeek->toDateString()
        )
        ->delete();


        /*
        |--------------------------------------------------------------------------
        | Track ingredients already added
        |--------------------------------------------------------------------------
        */

        $savedIngredients = [];


        foreach ($mealPlans as $mealPlan) {

            if (!$mealPlan->recipe) {
                continue;
            }


            $recipeId = $mealPlan->recipe->id;


            $ingredients = preg_split(
                '/\r\n|\r|\n/',
                $mealPlan->recipe->ingredients
            );


            foreach ($ingredients as $ingredient) {

                $ingredient = trim($ingredient);

                if ($ingredient === '') {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Same recipe + same ingredient = one shopping item
                |--------------------------------------------------------------------------
                */

                $key =
                    $recipeId .
                    '|' .
                    strtolower($ingredient);


                if (isset($savedIngredients[$key])) {
                    continue;
                }


                $savedIngredients[$key] = true;


                /*
                |--------------------------------------------------------------------------
                | Preserve checked state
                |--------------------------------------------------------------------------
                */

                $wasChecked =
                    $checkedItems[$key] ?? false;


                ShoppingList::create([
                    'user_id' => $userId,
                    'week_start' => $startOfWeek->toDateString(),
                    'recipe_id' => $recipeId,
                    'ingredient' => ucwords($ingredient),
                    'quantity' => null,
                    'is_checked' => $wasChecked,
                ]);
            }
        }
    }

}