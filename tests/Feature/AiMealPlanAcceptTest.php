<?php

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by the given user.
 */
function aiRecipe(User $user, array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => $user->id,
        'name' => 'Suggested Recipe',
        'description' => 'A recipe used for testing.',
        'category' => 'Breakfast',
        'cooking_time' => 30,
        'difficulty' => 'Easy',
        'servings' => 2,
        'ingredients' => 'Salt',
        'instructions' => 'Cook it.',
        'is_public' => false,
        ...$attributes,
    ]);
}

/**
 * Occupy a date + meal type slot for the given user.
 */
function aiSlot(User $user, Recipe $recipe, string $date, string $mealType): MealPlan
{
    return MealPlan::create([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
        'date' => $date,
        'meal_type' => $mealType,
    ]);
}

/**
 * Every planned meal, flattened to assertable values.
 *
 * Reads the `date` through the model cast rather than the raw column so the
 * assertions hold on both MySQL and SQLite.
 *
 * @return array<int, array<string, mixed>>
 */
function aiPlannedMeals(?int $userId = null): array
{
    return MealPlan::with('recipe')
        ->when($userId, fn ($query) => $query->where('user_id', $userId))
        ->get()
        ->map(fn (MealPlan $meal) => [
            'user_id' => $meal->user_id,
            'recipe' => $meal->recipe->name,
            'date' => $meal->date->format('Y-m-d'),
            'meal_type' => $meal->meal_type,
        ])
        ->sortBy(fn (array $meal) => $meal['date'].'|'.$meal['meal_type'])
        ->values()
        ->all();
}

/**
 * Submit an AI plan to the accept endpoint.
 *
 * @param  array<int, array<string, mixed>>  $plan
 */
function acceptPlan(
    User $user,
    array $plan,
    string $week = '2026-09-28',
    ?string $mealTypes = 'Breakfast'
) {
    return test()
        ->actingAs($user)
        ->post(route('meal-planner.accept'), [
            'weekly_plan' => json_encode($plan),
            'week' => $week,
            'meal_types' => $mealTypes,
        ]);
}

/**
 * A single entry in the submitted AI plan.
 *
 * @return array<string, mixed>
 */
function aiEntry(string $day, string $mealType, int $recipeId): array
{
    return [
        'day' => $day,
        'meal_type' => $mealType,
        'recipe_id' => $recipeId,
    ];
}

it('keeps lunch and dinner when only breakfast was requested', function () {
    $user = User::factory()->create();

    $breakfast = aiRecipe($user, ['name' => 'Old Breakfast']);
    $lunch = aiRecipe($user, ['name' => 'Tuna Salad']);
    $dinner = aiRecipe($user, ['name' => 'Beef Stew']);

    $suggested = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $breakfast, '2026-09-28', 'Breakfast');
    aiSlot($user, $lunch, '2026-09-28', 'Lunch');
    aiSlot($user, $dinner, '2026-09-28', 'Dinner');

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggested->id),
    ])->assertRedirect('/meal-planner?week=2026-09-28');

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Pancakes',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
        [
            'user_id' => $user->id,
            'recipe' => 'Beef Stew',
            'date' => '2026-09-28',
            'meal_type' => 'Dinner',
        ],
        [
            'user_id' => $user->id,
            'recipe' => 'Tuna Salad',
            'date' => '2026-09-28',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('overwrites a requested slot that is already occupied', function () {
    $user = User::factory()->create();

    $existing = aiRecipe($user, ['name' => 'Old Breakfast']);
    $suggested = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $existing, '2026-09-29', 'Breakfast');

    acceptPlan($user, [
        aiEntry('Tuesday', 'Breakfast', $suggested->id),
    ]);

    expect(aiPlannedMeals())->toHaveCount(1)
        ->and(aiPlannedMeals()[0]['recipe'])->toBe('Pancakes');
});

it('keeps the existing meal in a requested slot the ai could not fill', function () {
    $user = User::factory()->create();

    $wednesday = aiRecipe($user, ['name' => 'Old Breakfast']);
    $monday = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $wednesday, '2026-09-30', 'Breakfast');

    /*
    |--------------------------------------------------------------------------
    | Wednesday breakfast has no suggestion, so the plan only covers Monday
    |--------------------------------------------------------------------------
    */

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $monday->id),
    ]);

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Pancakes',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
        [
            'user_id' => $user->id,
            'recipe' => 'Old Breakfast',
            'date' => '2026-09-30',
            'meal_type' => 'Breakfast',
        ],
    ]);
});

it('ignores a meal type that was not requested even when it is submitted', function () {
    $user = User::factory()->create();

    $dinner = aiRecipe($user, ['name' => 'Beef Stew']);
    $suggestedBreakfast = aiRecipe($user, ['name' => 'Pancakes']);
    $suggestedDinner = aiRecipe($user, ['name' => 'Pasta']);

    aiSlot($user, $dinner, '2026-09-28', 'Dinner');

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggestedBreakfast->id),
        aiEntry('Monday', 'Dinner', $suggestedDinner->id),
    ]);

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Pancakes',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
        [
            'user_id' => $user->id,
            'recipe' => 'Beef Stew',
            'date' => '2026-09-28',
            'meal_type' => 'Dinner',
        ],
    ]);
});

it('never touches another user meals', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $otherBreakfast = aiRecipe($other, ['name' => 'Other Breakfast']);
    $suggested = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($other, $otherBreakfast, '2026-09-28', 'Breakfast');

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggested->id),
    ]);

    expect(aiPlannedMeals($other->id))->toBe([
        [
            'user_id' => $other->id,
            'recipe' => 'Other Breakfast',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
    ]);
});

it('never overwrites a slot with a private recipe the user does not own', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $existing = aiRecipe($user, ['name' => 'Old Breakfast']);
    $foreignRecipe = aiRecipe($other, ['name' => 'Not Mine']);

    aiSlot($user, $existing, '2026-09-28', 'Breakfast');

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $foreignRecipe->id),
    ])->assertSessionHas('error');

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Old Breakfast',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
    ]);
});

it('plans a public recipe owned by another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $publicRecipe = aiRecipe($other, [
        'name' => 'Hokkien Mee',
        'is_public' => true,
        'ingredients' => 'Noodles',
    ]);

    $existing = aiRecipe($user, [
        'name' => 'Old Breakfast',
        'ingredients' => 'Toast',
    ]);

    aiSlot($user, $existing, '2026-09-28', 'Breakfast');

    ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $existing->id,
        'ingredient' => 'Toast',
        'quantity' => null,
        'is_checked' => false,
    ]);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $publicRecipe->id),
    ])->assertSessionHasNoErrors();

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Hokkien Mee',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
    ]);

    expect(
        ShoppingList::where('user_id', $user->id)
            ->pluck('ingredient')
            ->sort()
            ->values()
            ->all()
    )->toBe(['Noodles']);
});

it('rebuilds the shopping list from the updated plan', function () {
    $user = User::factory()->create();

    $oldBreakfast = aiRecipe($user, [
        'name' => 'Old Breakfast',
        'ingredients' => 'Toast',
    ]);

    $lunch = aiRecipe($user, [
        'name' => 'Tuna Salad',
        'ingredients' => 'Lettuce',
    ]);

    $newBreakfast = aiRecipe($user, [
        'name' => 'Pancakes',
        'ingredients' => 'Flour',
    ]);

    aiSlot($user, $oldBreakfast, '2026-09-28', 'Breakfast');
    aiSlot($user, $lunch, '2026-09-28', 'Lunch');

    ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $oldBreakfast->id,
        'ingredient' => 'Toast',
        'quantity' => null,
        'is_checked' => false,
    ]);

    ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $lunch->id,
        'ingredient' => 'Lettuce',
        'quantity' => null,
        'is_checked' => false,
    ]);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $newBreakfast->id),
    ]);

    expect(
        ShoppingList::where('user_id', $user->id)
            ->pluck('ingredient')
            ->sort()
            ->values()
            ->all()
    )->toBe(['Flour', 'Lettuce']);
});

it('leaves the shopping list alone when the user never generated one', function () {
    $user = User::factory()->create();

    $suggested = aiRecipe($user, [
        'name' => 'Pancakes',
        'ingredients' => 'Flour',
    ]);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggested->id),
    ]);

    expect(
        ShoppingList::where('user_id', $user->id)->count()
    )->toBe(0);
});

it('writes nothing when the submitted plan has no valid meals', function () {
    $user = User::factory()->create();

    $existing = aiRecipe($user, ['name' => 'Old Breakfast']);

    aiSlot($user, $existing, '2026-09-28', 'Breakfast');

    acceptPlan($user, [])->assertSessionHas('error');

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Old Breakfast',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
    ]);
});

it('writes nothing when every submitted meal is outside the requested scope', function () {
    $user = User::factory()->create();

    $existing = aiRecipe($user, ['name' => 'Tuna Salad']);
    $suggested = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $existing, '2026-09-28', 'Lunch');

    acceptPlan($user, [
        aiEntry('Monday', 'Lunch', $suggested->id),
    ], mealTypes: 'Breakfast')->assertSessionHas('error');

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Tuna Salad',
            'date' => '2026-09-28',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('reports what was updated and what was left alone', function () {
    $user = User::factory()->create();

    $lunch = aiRecipe($user, ['name' => 'Tuna Salad']);
    $suggested = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $lunch, '2026-09-28', 'Lunch');

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggested->id),
    ])->assertSessionHas(
        'success',
        '1 meal updated for 28 Sep - 4 Oct 2026. Your lunch, dinner were left as they are.'
    );
});

it('pluralises the update count and omits the kept note when everything was requested', function () {
    $user = User::factory()->create();

    $first = aiRecipe($user, ['name' => 'Pancakes']);
    $second = aiRecipe($user, ['name' => 'Omelette']);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $first->id),
        aiEntry('Tuesday', 'Dinner', $second->id),
    ], mealTypes: 'Breakfast,Dinner')->assertSessionHas(
        'success',
        '2 meals updated for 28 Sep - 4 Oct 2026. Your lunch was left as it is.'
    );
});

it('falls back to planning every meal type when no selection is submitted', function () {
    $user = User::factory()->create();

    $breakfast = aiRecipe($user, ['name' => 'Pancakes']);
    $lunch = aiRecipe($user, ['name' => 'Tuna Salad']);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $breakfast->id),
        aiEntry('Monday', 'Lunch', $lunch->id),
    ], mealTypes: null)->assertSessionHasNoErrors();

    expect(aiPlannedMeals())->toHaveCount(2);
});

it('collapses a repeated slot to a single meal', function () {
    $user = User::factory()->create();

    $first = aiRecipe($user, ['name' => 'Pancakes']);
    $second = aiRecipe($user, ['name' => 'Omelette']);

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $first->id),
        aiEntry('Monday', 'Breakfast', $second->id),
    ])->assertSessionHas(
        'success',
        '1 meal updated for 28 Sep - 4 Oct 2026. Your lunch, dinner were left as they are.'
    );

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Omelette',
            'date' => '2026-09-28',
            'meal_type' => 'Breakfast',
        ],
    ]);
});

it('drops meal types it does not recognise from the submitted selection', function () {
    $user = User::factory()->create();

    $lunch = aiRecipe($user, ['name' => 'Tuna Salad']);
    $suggestedLunch = aiRecipe($user, ['name' => 'Soup']);
    $suggestedBreakfast = aiRecipe($user, ['name' => 'Pancakes']);

    aiSlot($user, $lunch, '2026-09-28', 'Lunch');

    /*
    |--------------------------------------------------------------------------
    | Brunch is not a meal type this planner knows, so the selection narrows
    | to Lunch alone and the breakfast entry must be ignored
    |--------------------------------------------------------------------------
    */

    acceptPlan($user, [
        aiEntry('Monday', 'Breakfast', $suggestedBreakfast->id),
        aiEntry('Monday', 'Lunch', $suggestedLunch->id),
    ], mealTypes: 'Brunch, Lunch');

    expect(aiPlannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Soup',
            'date' => '2026-09-28',
            'meal_type' => 'Lunch',
        ],
    ]);
});
