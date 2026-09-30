<?php

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by the given user.
 */
function slotRecipe(User $user, array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => $user->id,
        'name' => 'Planned Recipe',
        'description' => 'A recipe used for testing.',
        'category' => 'Lunch',
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
function occupySlot(User $user, Recipe $recipe, string $date, string $mealType = 'Lunch'): MealPlan
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
function plannedMeals(): array
{
    return MealPlan::with('recipe')
        ->get()
        ->map(fn (MealPlan $meal) => [
            'user_id' => $meal->user_id,
            'recipe' => $meal->recipe->name,
            'date' => $meal->date->format('Y-m-d'),
            'meal_type' => $meal->meal_type,
        ])
        ->all();
}

it('adds the meal when the slot is empty', function () {
    $user = User::factory()->create();
    $recipe = slotRecipe($user);

    $response = $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => $recipe->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
    ]);

    $response->assertRedirectContains('/meal-planner?week=2026-09-28');
    $response->assertSessionHas('success');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Planned Recipe',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('refuses to overwrite an occupied slot when the replacement is not confirmed', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);
    $incoming = slotRecipe($user, ['name' => 'Salad']);

    occupySlot($user, $planned, '2026-09-30');

    $response = $this->actingAs($user)->postJson('/meal-planner', [
        'recipe_id' => $incoming->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
    ]);

    $response->assertStatus(409);
    $response->assertJsonStructure([
        'message',
        'conflict' => ['meal_plan_id', 'date', 'meal_type', 'recipe' => ['id', 'name']],
    ]);

    $response->assertJsonPath('conflict.recipe.id', $planned->id);
    $response->assertJsonPath('conflict.recipe.name', 'Pancakes');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Pancakes',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('names the displaced recipe in the conflict response', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);

    occupySlot($user, $planned, '2026-09-30', 'Dinner');

    $response = $this->actingAs($user)->postJson('/meal-planner', [
        'recipe_id' => slotRecipe($user)->id,
        'date' => '2026-09-30',
        'meal_type' => 'Dinner',
    ]);

    $response->assertStatus(409);

    expect($response->json('message'))
        ->toContain('Dinner')
        ->toContain('30 Sep 2026')
        ->toContain('Pancakes');
});

it('redirects back with an error when a non json request hits an occupied slot', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);
    $incoming = slotRecipe($user, ['name' => 'Salad']);

    occupySlot($user, $planned, '2026-09-30');

    $response = $this->actingAs($user)->from('/recipes')->post('/meal-planner', [
        'recipe_id' => $incoming->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
    ]);

    $response->assertRedirect('/recipes');
    $response->assertSessionHas('error');
    $response->assertSessionHasInput('recipe_id', $incoming->id);

    expect(session('error'))->toContain('Pancakes');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Pancakes',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('replaces the meal once the replacement is confirmed', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);
    $incoming = slotRecipe($user, ['name' => 'Salad']);

    occupySlot($user, $planned, '2026-09-30');

    $response = $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => $incoming->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
        'replace' => 1,
    ]);

    $response->assertRedirectContains('/meal-planner?week=2026-09-28');
    $response->assertSessionHas('success');

    expect(session('success'))
        ->toContain('Salad')
        ->toContain('was replaced');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Salad',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('does not let one user overwrite another user slot', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $otherRecipe = slotRecipe($other, ['name' => 'Their Pancakes']);

    occupySlot($other, $otherRecipe, '2026-09-30');

    $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => slotRecipe($user, ['name' => 'My Lunch'])->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
    ])->assertRedirectContains('/meal-planner?week=2026-09-28');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $other->id,
            'recipe' => 'Their Pancakes',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
        [
            'user_id' => $user->id,
            'recipe' => 'My Lunch',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});

it('reports an empty slot as unoccupied', function () {
    $user = User::factory()->create();

    slotRecipe($user);

    $this->actingAs($user)
        ->getJson('/meal-planner/slots/check?date=2026-09-30&meal_type=Lunch')
        ->assertOk()
        ->assertJson([
            'occupied' => false,
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
            'recipe' => null,
        ]);
});

it('reports an occupied slot with the recipe currently planned', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);

    occupySlot($user, $planned, '2026-09-30', 'Dinner');

    $this->actingAs($user)
        ->getJson('/meal-planner/slots/check?date=2026-09-30&meal_type=Dinner')
        ->assertOk()
        ->assertJson([
            'occupied' => true,
            'date' => '2026-09-30',
            'meal_type' => 'Dinner',
            'recipe' => [
                'id' => $planned->id,
                'name' => 'Pancakes',
            ],
        ]);
});

it('does not leak another user meals through the slot check', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    occupySlot($other, slotRecipe($other, ['name' => 'Their Pancakes']), '2026-09-30');

    $this->actingAs($user)
        ->getJson('/meal-planner/slots/check?date=2026-09-30&meal_type=Lunch')
        ->assertOk()
        ->assertJson([
            'occupied' => false,
            'recipe' => null,
        ]);
});

it('rejects a slot check without a valid meal type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/meal-planner/slots/check?date=2026-09-30&meal_type=Dessert')
        ->assertStatus(422);
});

it('requires authentication for the slot check', function () {
    $this->getJson('/meal-planner/slots/check?date=2026-09-30&meal_type=Lunch')
        ->assertUnauthorized();
});

it('wires the recipe detail add to plan modal to check the slot', function () {
    $user = User::factory()->create();
    $recipe = slotRecipe($user);

    $this->actingAs($user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee('data-meal-slot-check="'.route('meal-planner.slots.check').'"', escape: false)
        ->assertSee('data-meal-plan-submit', escape: false)
        ->assertSee('id="recipeMealForm"', escape: false)
        ->assertSee('name="replace"', escape: false)
        ->assertSee('data-meal-slot-warning', escape: false)
        ->assertSee('data-meal-slot-submit', escape: false)
        ->assertSee('data-default-label="Add to Plan"', escape: false)
        ->assertSee("refreshMealSlot('recipeMealForm');", escape: false);
});

it('wires the recipe index add to plan modal to check the slot', function () {
    $user = User::factory()->create();
    $recipe = slotRecipe($user);

    $this->actingAs($user)
        ->get('/recipes')
        ->assertOk()
        ->assertSee('data-meal-slot-check="'.route('meal-planner.slots.check').'"', escape: false)
        ->assertSee('data-meal-plan-submit', escape: false)
        ->assertSee('id="recipeCardMealForm"', escape: false)
        ->assertSee('data-meal-slot-warning', escape: false)
        ->assertSee('data-meal-slot-submit', escape: false)
        ->assertSee("refreshMealSlot('recipeCardMealForm');", escape: false);
});

it('wires every browsing add to plan modal to the submit guard', function () {
    $user = User::factory()->create();
    $recipe = slotRecipe($user, ['name' => 'Hokkien Mee', 'is_public' => true]);

    $pages = [
        "/recipes/{$recipe->id}" => 'recipeMealForm',
        '/recipes' => 'recipeCardMealForm',
        '/favorites' => 'favouritesCardMealForm',
        '/discover' => 'discoverCardMealForm',
        '/explore' => 'exploreMealForm',
    ];

    foreach ($pages as $page => $formId) {
        $this->actingAs($user)
            ->get($page)
            ->assertOk()
            ->assertSee('data-meal-plan-submit', escape: false)
            ->assertSee("refreshMealSlot('{$formId}');", escape: false);
    }
});

it('wires the ingredient search results modal to the submit guard', function () {
    $user = User::factory()->create();

    slotRecipe($user, ['ingredients' => 'noodles']);

    $this->actingAs($user)
        ->post('/find-by-ingredients', ['ingredients' => 'noodles'])
        ->assertOk()
        ->assertSee('data-meal-slot-check="'.route('meal-planner.slots.check').'"', escape: false)
        ->assertSee('data-meal-plan-submit', escape: false)
        ->assertSee("refreshMealSlot('ingredientMealForm');", escape: false);
});

it('pre authorises replacement from the planner grid change flow', function () {
    $user = User::factory()->create();
    $planned = slotRecipe($user, ['name' => 'Pancakes']);

    occupySlot($user, $planned, '2026-09-30');

    $this->actingAs($user)
        ->get('/meal-planner?week=2026-09-28')
        ->assertOk()
        ->assertSee('name="replace"', escape: false);

    $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => slotRecipe($user, ['name' => 'Salad'])->id,
        'date' => '2026-09-30',
        'meal_type' => 'Lunch',
        'replace' => 1,
    ])->assertSessionHas('success');

    expect(plannedMeals())->toBe([
        [
            'user_id' => $user->id,
            'recipe' => 'Salad',
            'date' => '2026-09-30',
            'meal_type' => 'Lunch',
        ],
    ]);
});
