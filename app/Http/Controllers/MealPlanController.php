<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Support\SyncShoppingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            $startOfWeek = Carbon::parse($request->week)
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
                $startOfWeek->startOfDay(),
                $endOfWeek->endOfDay(),
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Easy lookup
        |--------------------------------------------------------------------------
        */

        $mealPlanMap = $mealPlans->keyBy(function ($meal) {

            return $meal->date->format('Y-m-d')
                .'_'.
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
            'replace' => 'sometimes|boolean',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Normalise the selection
        |--------------------------------------------------------------------------
        |
        | The week picker posts a `slots[]` array while older modals still post
        | a single `date` + `meal_type`. Both collapse into the same shape.
        |
        */

        $slots = $this->normalizeSlots($request);

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

        $replace = $request->boolean('replace');

        /*
        |--------------------------------------------------------------------------
        | Look up the meals already planned in the selected slots
        |--------------------------------------------------------------------------
        */

        $slotsCollection = collect($slots);

        $existingMeals = MealPlan::with('recipe')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [
                Carbon::parse($slotsCollection->min('date'))->startOfDay(),
                Carbon::parse($slotsCollection->max('date'))->endOfDay(),
            ])
            ->whereIn('meal_type', $slotsCollection->pluck('meal_type')->unique()->all())
            ->get()
            ->keyBy(fn (MealPlan $meal) => $meal->date->format('Y-m-d').'|'.$meal->meal_type);

        /*
        |--------------------------------------------------------------------------
        | Conflicts
        |--------------------------------------------------------------------------
        |
        | A slot already holding another recipe needs confirmation before it is
        | overwritten. A slot already holding this very recipe is a no-op.
        |
        */

        $conflicts = collect($slots)
            ->map(fn (array $slot) => $existingMeals->get($slot['date'].'|'.$slot['meal_type']))
            ->filter(fn (?MealPlan $meal) => $meal && $meal->recipe_id !== $recipe->id)
            ->values();

        if ($conflicts->isNotEmpty() && ! $replace) {

            $message = $this->conflictMessage($conflicts, $slots);

            if ($request->wantsJson()) {

                return response()->json([
                    'message' => $message,
                    'conflict' => $this->conflictPayload($conflicts->first()),
                    'conflicts' => $conflicts
                        ->map(fn (MealPlan $meal) => $this->conflictPayload($meal))
                        ->all(),
                ], 409);
            }

            return back()
                ->withInput()
                ->with('error', $message);
        }

        /*
        |--------------------------------------------------------------------------
        | Create or replace the selected slots
        |--------------------------------------------------------------------------
        */

        $added = 0;
        $replaced = 0;

        foreach ($slots as $slot) {

            $existing = $existingMeals->get($slot['date'].'|'.$slot['meal_type']);

            if ($existing) {

                if ($existing->recipe_id === $recipe->id) {
                    continue;
                }

                $existing->update([
                    'recipe_id' => $recipe->id,
                ]);

                $replaced++;

                continue;
            }

            MealPlan::create([
                'user_id' => Auth::id(),
                'recipe_id' => $recipe->id,
                'date' => $slot['date'],
                'meal_type' => $slot['meal_type'],
            ]);

            $added++;
        }

        /*
        |--------------------------------------------------------------------------
        | Sync every affected week
        |--------------------------------------------------------------------------
        */

        collect($slots)
            ->map(fn (array $slot) => Carbon::parse($slot['date'])->startOfWeek())
            ->unique(fn (Carbon $week) => $week->toDateString())
            ->each(fn (Carbon $week) => $this->syncShoppingList($week));

        $startOfWeek = Carbon::parse($slots[0]['date'])->startOfWeek();

        return redirect(
            '/meal-planner?week='.
            $startOfWeek->format('Y-m-d')
        )->with(
            'success',
            $this->successMessage($slots, $recipe, $added, $replaced)
        );
    }

    /**
     * Report whether a date + meal type slot is already taken, so the
     * add-to-plan modals can ask the user before replacing a planned meal.
     */
    public function slotStatus(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|in:Breakfast,Lunch,Dinner',
        ]);

        $existingMeal = MealPlan::with('recipe')
            ->where('user_id', Auth::id())
            ->whereDate('date', $validated['date'])
            ->where('meal_type', $validated['meal_type'])
            ->first();

        return response()->json([
            'occupied' => $existingMeal !== null,
            'date' => $validated['date'],
            'meal_type' => $validated['meal_type'],
            'recipe' => $existingMeal ? [
                'id' => $existingMeal->recipe->id,
                'name' => $existingMeal->recipe->name,
            ] : null,
        ]);
    }

    /**
     * Return every planned meal for a week so the add-to-plan picker can show
     * which slots are already taken.
     */
    public function slotsForWeek(Request $request)
    {
        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : now()->startOfWeek();

        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $mealPlans = MealPlan::with('recipe')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [
                $startOfWeek->startOfDay(),
                $endOfWeek->endOfDay(),
            ])
            ->get();

        return response()->json([
            'week_start' => $startOfWeek->toDateString(),
            'week_end' => $endOfWeek->toDateString(),
            'slots' => $mealPlans
                ->map(fn (MealPlan $meal) => [
                    'date' => $meal->date->format('Y-m-d'),
                    'meal_type' => $meal->meal_type,
                    'recipe' => $meal->recipe ? [
                        'id' => $meal->recipe->id,
                        'name' => $meal->recipe->name,
                    ] : null,
                ])
                ->values(),
        ]);
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

        return redirect('/meal-planner?week='.
            $startOfWeek->format('Y-m-d')
        )->with('success', 'Meal removed from your plan.');
    }

    public function clear(Request $request)
    {
        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
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
            '/meal-planner?week='.$startOfWeek->format('Y-m-d')
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

                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('ingredients', 'like', '%'.$search.'%');

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
                'CASE
                    WHEN category = ? THEN 0
                    ELSE 1
                END',
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

        if (! $hasFilters) {

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

    /**
     * Normalise the posted selection into unique date + meal type pairs.
     *
     * Accepts either a `slots[]` array (from the week picker) or the legacy
     * single `date` + `meal_type` fields.
     *
     * @return array<int, array{date: string, meal_type: string}>
     */
    private function normalizeSlots(Request $request): array
    {
        $raw = $request->input('slots');

        if (is_array($raw)) {

            $pairs = collect($raw)->map(fn ($slot) => [
                'date' => is_array($slot) ? ($slot['date'] ?? null) : null,
                'meal_type' => is_array($slot) ? ($slot['meal_type'] ?? null) : null,
            ]);

        } else {

            $pairs = collect([[
                'date' => $request->input('date'),
                'meal_type' => $request->input('meal_type'),
            ]]);
        }

        Validator::make(
            ['slots' => $pairs->all()],
            [
                'slots' => 'required|array|min:1',
                'slots.*.date' => 'required|date',
                'slots.*.meal_type' => 'required|in:Breakfast,Lunch,Dinner',
            ]
        )->validate();

        return $pairs
            ->map(fn (array $pair) => [
                'date' => Carbon::parse($pair['date'])->toDateString(),
                'meal_type' => $pair['meal_type'],
            ])
            ->unique(fn (array $slot) => $slot['date'].'|'.$slot['meal_type'])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, MealPlan>  $conflicts
     * @param  array<int, array{date: string, meal_type: string}>  $slots
     */
    private function conflictMessage($conflicts, array $slots): string
    {
        $first = $conflicts->first();

        if (count($slots) === 1) {

            return sprintf(
                '%s on %s is already planned as "%s". Confirm the replacement to continue.',
                $first->meal_type,
                $first->date->format('j M Y'),
                $first->recipe->name
            );
        }

        return sprintf(
            '%d of the selected slots are already planned. Confirm to replace them.',
            $conflicts->count()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function conflictPayload(MealPlan $meal): array
    {
        return [
            'meal_plan_id' => $meal->id,
            'date' => $meal->date->format('Y-m-d'),
            'meal_type' => $meal->meal_type,
            'recipe' => [
                'id' => $meal->recipe->id,
                'name' => $meal->recipe->name,
            ],
        ];
    }

    /**
     * @param  array<int, array{date: string, meal_type: string}>  $slots
     */
    private function successMessage(array $slots, Recipe $recipe, int $added, int $replaced): string
    {
        if (count($slots) === 1) {

            if ($replaced > 0) {

                $slot = $slots[0];

                return sprintf(
                    '%s on %s was replaced with "%s".',
                    $slot['meal_type'],
                    Carbon::parse($slot['date'])->format('j M Y'),
                    $recipe->name
                );
            }

            if ($added === 0) {

                return '"'.$recipe->name.'" is already planned for that slot.';
            }

            return 'Meal added to your plan!';
        }

        $parts = [];

        if ($added > 0) {
            $parts[] = $added.' '.Str::plural('slot', $added).' added';
        }

        if ($replaced > 0) {
            $parts[] = $replaced.' '.Str::plural('slot', $replaced).' replaced';
        }

        if (empty($parts)) {
            return '"'.$recipe->name.'" is already planned for the selected slots.';
        }

        return ucfirst(implode(', ', $parts)).' with "'.$recipe->name.'".';
    }

    /**
     * Rebuild the shopping list for the affected week.
     */
    private function syncShoppingList(Carbon $startOfWeek): void
    {
        app(SyncShoppingList::class)(
            Auth::id(),
            $startOfWeek
        );
    }
}
