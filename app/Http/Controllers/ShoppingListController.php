<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Shopping List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Selected Week
        |--------------------------------------------------------------------------
        */

        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->addDays(6);

        /*
        |--------------------------------------------------------------------------
        | Get meals for selected week
        |--------------------------------------------------------------------------
        */

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Get shopping list for selected week
        |--------------------------------------------------------------------------
        |
        | ShoppingList uses week_start, not date.
        |
        */

        $shoppingLists = ShoppingList::with('recipe')
            ->where('user_id', $user->id)
            ->whereDate(
                'week_start',
                $startOfWeek->toDateString()
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Group ingredients by recipe
        |--------------------------------------------------------------------------
        */

        $groupedLists = $shoppingLists->groupBy(
            'recipe_id'
        );

        /*
        |--------------------------------------------------------------------------
        | Shopping Progress
        |--------------------------------------------------------------------------
        */

        $shoppingTotal =
            $shoppingLists->count();

        $shoppingCompleted =
            $shoppingLists
                ->where('is_checked', true)
                ->count();

        $shoppingPercentage =
            $shoppingTotal > 0
                ? round(
                    ($shoppingCompleted / $shoppingTotal) * 100
                )
                : 0;

        return view(
            'shopping-lists.index',
            compact(
                'mealPlans',
                'shoppingLists',
                'groupedLists',
                'startOfWeek',
                'endOfWeek',
                'shoppingTotal',
                'shoppingCompleted',
                'shoppingPercentage'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Shopping List
    |--------------------------------------------------------------------------
    */

    public function generate(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Selected Week
        |--------------------------------------------------------------------------
        */

        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek =
            $startOfWeek->copy()->addDays(6);

        /*
        |--------------------------------------------------------------------------
        | Get meals for selected week
        |--------------------------------------------------------------------------
        */

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | No meals
        |--------------------------------------------------------------------------
        */

        if ($mealPlans->isEmpty()) {

            return redirect(
                '/shopping-list?week='.
                $startOfWeek->format('Y-m-d')
            )->with(
                'error',
                'No meals are planned for this week. Please plan some meals first.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove old shopping list for this week
        |--------------------------------------------------------------------------
        |
        | This prevents duplicate ingredients when the user
        | clicks Generate Shopping List again.
        |
        */

        ShoppingList::where(
            'user_id',
            $user->id
        )
            ->whereDate(
                'week_start',
                $startOfWeek->toDateString()
            )
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | Create new shopping list
        |--------------------------------------------------------------------------
        */

        $this->createShoppingList(
            $user->id,
            $startOfWeek,
            $mealPlans
        );

        return redirect(
            '/shopping-list?week='.
            $startOfWeek->format('Y-m-d')
        )->with(
            'success',
            'Shopping list generated for '.
            $startOfWeek->format('d M').
            ' - '.
            $endOfWeek->format('d M Y').
            '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Shopping List Records
    |--------------------------------------------------------------------------
    */

    private function createShoppingList(
        int $userId,
        Carbon $startOfWeek,
        $mealPlans
    ): void {

        $savedIngredients = [];

        foreach ($mealPlans as $mealPlan) {

            if (! $mealPlan->recipe) {
                continue;
            }

            $recipeId = $mealPlan->recipe->id;

            $recipeIngredients = preg_split(
                '/\r\n|\r|\n/',
                $mealPlan->recipe->ingredients
            );

            foreach ($recipeIngredients as $ingredient) {

                $ingredient = trim($ingredient);

                if ($ingredient === '') {
                    continue;
                }

                $parsed = $this->parseIngredient(
                    $ingredient
                );

                $ingredientName =
                    trim($parsed['ingredient']);

                $quantity =
                    $parsed['quantity'];

                if ($ingredientName === '') {
                    continue;
                }

                $key =
                    $recipeId.
                    '|'.
                    strtolower($ingredientName);

                if (isset($savedIngredients[$key])) {
                    continue;
                }

                $savedIngredients[$key] = true;

                ShoppingList::create([
                    'user_id' => $userId,
                    'week_start' => $startOfWeek->toDateString(),
                    'recipe_id' => $recipeId,
                    'ingredient' => ucwords($ingredientName),
                    'quantity' => $quantity,
                    'is_checked' => false,
                ]);
            }
        }
    }

    private function parseIngredient(string $ingredient): array
    {
        $ingredient = trim($ingredient);

        /*
        |--------------------------------------------------------------------------
        | Quantity + unit
        |--------------------------------------------------------------------------
        |
        | Supports:
        |
        | 3 cups cooked rice
        | 1 chicken breast
        | 2 cloves garlic
        | 1/2 teaspoon salt
        | 1/4 onion
        | 2 tablespoons soy sauce
        | 1 pinch salt
        | pinch salt
        |
        */

        $pattern = '~^
            (
                \d+/\d+
                |
                \d+(?:\.\d+)?
                |
                pinch
            )
            \s*
            (
                tablespoons?|tbsp|
                teaspoons?|tsp|
                cups?|
                grams?|g|
                kilograms?|kg|
                milliliters?|ml|
                liters?|l|
                cloves?|
                pieces?|pcs|
                slices?|
                pinch(?:es)?
            )?
            \s*
            (?:of\s+)?
        ~ix';

        if (preg_match(
            $pattern,
            $ingredient,
            $matches
        )) {

            $quantity = trim(
                $matches[1].
                (
                    ! empty($matches[2])
                        ? ' '.trim($matches[2])
                        : ''
                )
            );

            $name = trim(
                preg_replace(
                    $pattern,
                    '',
                    $ingredient,
                    1
                )
            );

            return [
                'ingredient' => $name,
                'quantity' => $quantity,
            ];
        }

        return [
            'ingredient' => $ingredient,
            'quantity' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Ingredient
    |--------------------------------------------------------------------------
    */

    public function toggle($id)
    {
        $item = ShoppingList::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $item->update([
            'is_checked' => ! $item->is_checked,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Recalculate progress for the affected week
        |--------------------------------------------------------------------------
        */

        $userId = Auth::id();

        $total = ShoppingList::where(
            'user_id',
            $userId
        )
            ->whereDate(
                'week_start',
                $item->week_start->toDateString()
            )
            ->count();

        $completed = ShoppingList::where(
            'user_id',
            $userId
        )
            ->whereDate(
                'week_start',
                $item->week_start->toDateString()
            )
            ->where('is_checked', true)
            ->count();

        $percentage = $total > 0
            ? round(($completed / $total) * 100)
            : 0;

        return response()->json([
            'success' => true,
            'is_checked' => $item->is_checked,
            'total' => $total,
            'completed' => $completed,
            'percentage' => $percentage,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Ingredient
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $item = ShoppingList::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);

        $item->delete();

        return back()->with(
            'success',
            'Ingredient removed from shopping list.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Selected Week
    |--------------------------------------------------------------------------
    */

    public function clear(Request $request)
    {
        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        ShoppingList::where(
            'user_id',
            Auth::id()
        )
            ->whereDate(
                'week_start',
                $startOfWeek->toDateString()
            )
            ->delete();

        return redirect(
            '/shopping-list?week='.
            $startOfWeek->format('Y-m-d')
        )->with(
            'success',
            'Shopping list for this week has been cleared.'
        );
    }
}
