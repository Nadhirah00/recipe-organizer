<?php

namespace App\Support;

use App\Models\MealPlan;
use App\Models\ShoppingList;
use Carbon\Carbon;

/**
 * Rebuild a week's shopping list from the meals planned for that week.
 *
 * Anything the user has never generated a shopping list for is left alone,
 * so this is safe to call after any planner change.
 */
class SyncShoppingList
{
    public function __invoke(int $userId, Carbon $startOfWeek): void
    {
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

                $parsed = IngredientParser::parse(
                    $item->ingredient
                );

                $key =
                    $item->recipe_id.
                    '|'.
                    strtolower(trim($parsed['ingredient']));

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

            if (! $mealPlan->recipe) {
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
                | Split the quantity out so it can be shown
                | in its own right-aligned column
                |--------------------------------------------------------------------------
                */

                $parsed = IngredientParser::parse(
                    $ingredient
                );

                $ingredientName =
                    trim($parsed['ingredient']);

                $quantity =
                    $parsed['quantity'];

                if ($ingredientName === '') {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Same recipe + same ingredient = one shopping item
                |--------------------------------------------------------------------------
                |
                | Keyed on the parsed name so the same ingredient
                | collapses regardless of the raw wording or
                | quantity, matching createShoppingList().
                |
                */

                $key =
                    $recipeId.
                    '|'.
                    strtolower($ingredientName);

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
                    'ingredient' => ucwords($ingredientName),
                    'quantity' => $quantity,
                    'is_checked' => $wasChecked,
                ]);
            }
        }
    }
}
