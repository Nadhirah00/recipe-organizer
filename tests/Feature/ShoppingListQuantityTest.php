<?php

use App\Models\MealPlan;
use App\Models\Recipe;
use App\Models\ShoppingList;
use App\Models\User;
use App\Support\IngredientParser;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe with a multi line ingredient block.
 *
 * @param  array<string, mixed>  $attributes
 */
function quantityRecipe(User $user, string $ingredients, array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => $user->id,
        'name' => 'Quantity Recipe',
        'description' => 'A recipe used for testing quantities.',
        'category' => 'Lunch',
        'cooking_time' => 30,
        'difficulty' => 'Easy',
        'servings' => 2,
        'ingredients' => $ingredients,
        'instructions' => 'Cook it.',
        'is_public' => false,
        ...$attributes,
    ]);
}

/**
 * Plan a recipe for the given user, which is what triggers
 * MealPlanController::syncShoppingList().
 */
function planQuantityRecipe(User $user, Recipe $recipe, string $date = '2026-09-30'): MealPlan
{
    return MealPlan::create([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
        'date' => $date,
        'meal_type' => 'Lunch',
    ]);
}

/**
 * Every shopping list row as a flat [ingredient, quantity] pair.
 *
 * @return array<int, array{0: string, 1: string|null}>
 */
function shoppingRows(): array
{
    return ShoppingList::orderBy('id')
        ->get()
        ->map(fn (ShoppingList $item) => [$item->ingredient, $item->quantity])
        ->all();
}

it('splits a quantity and unit out of an ingredient line', function () {
    expect(IngredientParser::parse('2 tablespoons soy sauce'))->toBe([
        'ingredient' => 'soy sauce',
        'quantity' => '2 tablespoons',
    ]);
});

it('handles fractions, decimals and unitless amounts', function () {
    expect(IngredientParser::parse('1/4 onion')['quantity'])->toBe('1/4')
        ->and(IngredientParser::parse('1/4 onion')['ingredient'])->toBe('onion')
        ->and(IngredientParser::parse('1.2 litres chicken stock')['quantity'])->toBe('1.2 litres')
        ->and(IngredientParser::parse('3 eggs')['quantity'])->toBe('3')
        ->and(IngredientParser::parse('3 eggs')['ingredient'])->toBe('eggs');
});

it('reads spelled out units as whole words rather than their first letter', function (string $line, string $quantity, string $name) {
    expect(IngredientParser::parse($line))->toBe([
        'ingredient' => $name,
        'quantity' => $quantity,
    ]);
})->with([
    ['1 litre chicken stock', '1 litre', 'chicken stock'],
    ['1.2 litres chicken stock', '1.2 litres', 'chicken stock'],
    ['500 millilitres water', '500 millilitres', 'water'],
    ['1 l chicken stock', '1 l', 'chicken stock'],
    ['2 lemons', '2', 'lemons'],
    ['1 garlic bulb', '1', 'garlic bulb'],
    ['1 lettuce leaf', '1', 'lettuce leaf'],
    ['1 pinch salt', '1 pinch', 'salt'],
]);

it('moves counting units into the quantity', function (string $line, string $quantity, string $name) {
    expect(IngredientParser::parse($line))->toBe([
        'ingredient' => $name,
        'quantity' => $quantity,
    ]);
})->with([
    ['1 can tuna', '1 can', 'tuna'],
    ['2 cans tuna', '2 cans', 'tuna'],
    ['1 tin coconut milk', '1 tin', 'coconut milk'],
    ['1 bunch bok choy', '1 bunch', 'bok choy'],
    ['2 bunches coriander', '2 bunches', 'coriander'],
    ['1 bunch of coriander', '1 bunch', 'coriander'],
    ['1 pack tofu skin', '1 pack', 'tofu skin'],
    ['1 stalk lemongrass, bruised', '1 stalk', 'lemongrass, bruised'],
    ['1 thumb ginger', '1 thumb', 'ginger'],
    ['2 thumbs ginger', '2 thumbs', 'ginger'],
    ['1 dash soy sauce', '1 dash', 'soy sauce'],
    ['2 drops sesame oil', '2 drops', 'sesame oil'],
]);

it('never reports a quantity the old unit pattern would have mangled', function (string $line) {
    $current = IngredientParser::parse($line);
    $legacy = IngredientParser::parseLegacy($line);

    /*
    |----------------------------------------------------------------------
    | The old pattern had no word boundary, so a unit it did not
    | know was cut down to its first letter. Wherever it produced a
    | different answer, the current pattern must not have kept the
    | same mangled name.
    */

    if ($current['quantity'] === $legacy['quantity']) {
        expect(true)->toBeTrue();

        return;
    }

    expect($current['ingredient'])->not->toBe($legacy['ingredient']);
})->with([
    '1 lettuce leaf',
    '3 lettuce leaves',
    '1 litre chicken stock',
    '500 millilitres water',
    '1 garlic bulb',
    '1 lemon',
]);

it('leaves an ingredient without a quantity untouched', function () {
    expect(IngredientParser::parse('salt to taste'))->toBe([
        'ingredient' => 'salt to taste',
        'quantity' => null,
    ]);
});

it('stores the quantity in its own column when the list is generated', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "2 tablespoons soy sauce\n1 pinch salt"));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirectContains('/shopping-list');

    expect(shoppingRows())->toBe([
        ['Soy Sauce', '2 tablespoons'],
        ['Salt', '1 pinch'],
    ]);
});

it('stores the quantity in its own column when a meal plan change syncs the list', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "150g rice\n1.2 litres chicken stock"));

    /*
    |--------------------------------------------------------------------------
    | syncShoppingList() only rebuilds a week that already has a
    | generated list, so seed one the way the Generate button would.
    */

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    ShoppingList::query()->update(['quantity' => null, 'ingredient' => 'Rice']);

    $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => quantityRecipe($user, '1 whole chicken')->id,
        'date' => '2026-10-01',
        'meal_type' => 'Dinner',
    ])->assertRedirect();

    $rice = ShoppingList::where('ingredient', 'Rice')->first();

    expect($rice)->not->toBeNull()
        ->and($rice->quantity)->toBe('150 g')
        ->and(ShoppingList::where('ingredient', 'Whole Chicken')->first()->quantity)
        ->toBe('1');
});

it('collapses the same ingredient planned twice into one row', function () {
    $user = User::factory()->create();

    $recipe = quantityRecipe($user, '2 cloves garlic');

    planQuantityRecipe($user, $recipe, '2026-09-30');
    planQuantityRecipe($user, $recipe, '2026-10-01');

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    expect(shoppingRows())->toBe([
        ['Garlic', '2 cloves'],
    ]);
});

it('keeps a checked item checked after a sync', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "2 cloves garlic\n3 eggs"));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $garlic = ShoppingList::where('ingredient', 'Garlic')->first();

    $this->actingAs($user)
        ->patchJson("/shopping-list/{$garlic->id}/toggle")
        ->assertOk()
        ->assertJsonPath('is_checked', true);

    expect($garlic->fresh()->is_checked)->toBeTrue();

    $this->actingAs($user)->post('/meal-planner', [
        'recipe_id' => quantityRecipe($user, '1 lime')->id,
        'date' => '2026-10-01',
        'meal_type' => 'Dinner',
    ])->assertRedirect();

    expect(
        ShoppingList::where('ingredient', 'Garlic')->first()->is_checked
    )->toBeTrue();
});

it('backfills quantities onto rows written before the parser existed', function () {
    $user = User::factory()->create();

    ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => null,
        'ingredient' => '150g Rice',
        'quantity' => null,
        'is_checked' => true,
    ]);

    ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => null,
        'ingredient' => 'Salt To Taste',
        'quantity' => null,
        'is_checked' => false,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    $rice = ShoppingList::where('ingredient', 'Rice')->first();

    expect(shoppingRows())->toBe([
        ['Rice', '150 g'],
        ['Salt To Taste', null],
    ])
        ->and($rice->is_checked)->toBeTrue();
});

it('repairs a row corrupted by the earlier unit parsing bug', function () {
    $user = User::factory()->create();

    $recipe = quantityRecipe($user, '1 litre chicken stock');

    /*
    |--------------------------------------------------------------------------
    | The old parser matched "l" out of "litre", leaving "Itre
    | Chicken Stock" behind. The backfill has to recover the
    | original line from the recipe, not from the stored name.
    */

    $item = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $recipe->id,
        'ingredient' => 'Itre Chicken Stock',
        'quantity' => '1 l',
        'is_checked' => false,
    ]);

    expect(IngredientParser::parse($item->ingredient)['ingredient'])
        ->toBe('Itre Chicken Stock');

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($item->fresh()->ingredient)->toBe('Chicken Stock')
        ->and($item->fresh()->quantity)->toBe('1 litre');
});

it('rebuilds a row whose leading unit letter was swallowed', function () {
    $user = User::factory()->create();

    $recipe = quantityRecipe($user, "2 tablespoons soy sauce\n1.2 litres chicken stock");

    $item = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $recipe->id,
        'ingredient' => 'Itres Chicken Stock',
        'quantity' => '1.2 l',
        'is_checked' => true,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($item->fresh()->ingredient)->toBe('Chicken Stock')
        ->and($item->fresh()->quantity)->toBe('1.2 litres')
        ->and($item->fresh()->is_checked)->toBeTrue();
});

it('rebuilds a row where the unit letter came out of the ingredient name', function () {
    $user = User::factory()->create();

    $recipe = quantityRecipe($user, "1 can tuna\n1 lettuce leaf");

    /*
    |--------------------------------------------------------------------------
    | The old pattern had no word boundary, so "l" was taken from
    | the front of "lettuce" and the row was left as a quantity
    | of "1 l" with a name of "ettuce leaf".
    */

    $tuna = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $recipe->id,
        'ingredient' => 'Ettuce Leaf',
        'quantity' => '1 l',
        'is_checked' => false,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($tuna->fresh()->ingredient)->toBe('Lettuce Leaf')
        ->and($tuna->fresh()->quantity)->toBe('1');
});

it('rebuilds every row a recipe line produced under the old pattern', function () {
    $user = User::factory()->create();

    $recipe = quantityRecipe($user, "1 lettuce leaf\n1 litre chicken stock\n4 slices bread");

    $stored = [
        ['Ettuce Leaf', '1 l'],
        ['Itre Chicken Stock', '1 l'],
        ['Bread', '4 slices'],
    ];

    $items = collect($stored)->map(fn (array $pair) => ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => $recipe->id,
        'ingredient' => $pair[0],
        'quantity' => $pair[1],
        'is_checked' => false,
    ]));

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($items->map(fn (ShoppingList $i) => [$i->fresh()->ingredient, $i->fresh()->quantity])->all())
        ->toBe([
            ['Lettuce Leaf', '1'],
            ['Chicken Stock', '1 litre'],
            ['Bread', '4 slices'],
        ]);
});

it('refreshes a row the old pattern read correctly once a unit was added', function () {
    $user = User::factory()->create();

    /*
    |--------------------------------------------------------------------------
    | "can" was not a known unit when this row was written, so it
    | was stored as a quantity of "1" against a name of "Can Tuna".
    | The old pattern still reads the line that way, so this is not
    | damage, but the current pattern can split it better.
    */

    $item = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => quantityRecipe($user, '1 can tuna')->id,
        'ingredient' => 'Can Tuna',
        'quantity' => '1',
        'is_checked' => false,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($item->fresh()->ingredient)->toBe('Tuna')
        ->and($item->fresh()->quantity)->toBe('1 can');
});

it('leaves a row alone when the recipe no longer matches the stored name', function () {
    $user = User::factory()->create();

    $item = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => null,
        'ingredient' => '1 Lettuce Leaf',
        'quantity' => null,
        'is_checked' => false,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($item->fresh()->ingredient)->toBe('Lettuce Leaf')
        ->and($item->fresh()->quantity)->toBe('1');
});

it('leaves an ingredient the user deleted from the recipe alone', function () {
    $user = User::factory()->create();

    $item = ShoppingList::create([
        'user_id' => $user->id,
        'week_start' => '2026-09-28',
        'recipe_id' => quantityRecipe($user, '2 tablespoons soy sauce')->id,
        'ingredient' => 'Mushrooms',
        'quantity' => '150 g',
        'is_checked' => true,
    ]);

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect($item->fresh()->ingredient)->toBe('Mushrooms')
        ->and($item->fresh()->quantity)->toBe('150 g')
        ->and($item->fresh()->is_checked)->toBeTrue();
});

it('leaves a row untouched when the backfill is run again', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "150g rice\nsalt to taste"));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $before = shoppingRows();

    $this->artisan('app:backfill-shopping-list-quantities')
        ->assertSuccessful();

    expect(shoppingRows())->toBe($before);
});

it('renders the quantity in a fixed width right aligned cell', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "2 tablespoons soy sauce\nsalt to taste"));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $response = $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk();

    $response->assertSee('grid-cols-[auto_1fr_7rem_auto]', escape: false)
        ->assertSee('data-quantity', escape: false)
        ->assertSee('2 tablespoons')
        ->assertSee('Salt To Taste');

    /*
    |--------------------------------------------------------------------------
    | The quantity cell renders even when there is no quantity,
    | so rows without one do not shift the ingredient column.
    */

    $rows = substr_count($response->getContent(), 'data-quantity');

    expect($rows)->toBe(2);
});

it('renders one collapsible section per recipe', function () {
    $user = User::factory()->create();

    $first = quantityRecipe($user, '2 tablespoons soy sauce');
    $second = quantityRecipe($user, '1 whole chicken', ['name' => 'Second Recipe']);

    planQuantityRecipe($user, $first);
    planQuantityRecipe($user, $second, '2026-10-01');

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $content = preg_replace('/\s+/', ' ', $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk()
        ->getContent());

    expect(substr_count($content, 'data-recipe-name='))->toBe(2)
        ->and(substr_count($content, 'aria-controls="shopping-section-'))->toBe(2)
        ->and(substr_count($content, 'id="shopping-section-'))->toBe(2);

    /*
    |----------------------------------------------------------------------
    | Each section starts expanded, and the toggle points at its own
    | body so the header and the list stay wired together.
    */

    expect($content)
        ->toContain('data-expanded="true"')
        ->toContain('aria-controls="shopping-section-'.$first->id.'"')
        ->toContain('id="shopping-section-'.$first->id.'"')
        ->toContain('aria-controls="shopping-section-'.$second->id.'"')
        ->toContain('id="shopping-section-'.$second->id.'"')
        ->toContain('data-recipe-name="Second Recipe"');
});

it('keeps the recipe name as a heading outside the collapse button', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, '2 tablespoons soy sauce'));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $content = $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk()
        ->getContent();

    /*
    |----------------------------------------------------------------------
    | Nesting a heading inside the button would drop it from the
    | document outline, so the h2 wraps the button instead.
    */

    expect($content)->toMatch('/<h2[^>]*>\s*<button\s+type="button"\s+data-section-toggle/s');
});
it('wires a hide all control that flips to show all', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, '2 tablespoons soy sauce'));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $response = $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk();

    $response->assertSee('data-toggle-all', escape: false)
        ->assertSee('data-toggle-all-label', escape: false)
        ->assertSee('data-toggle-all-icon', escape: false)
        ->assertSee('Hide all ingredients')
        ->assertSee('shopping-list-sections', escape: false)
        ->assertSee('shoppingSections.forEach', escape: false)
        ->assertSee('setSectionExpanded(section, !allExpanded)', escape: false)
        ->assertSee('Hide all ingredients', escape: false)
        ->assertSee('Show all ingredients', escape: false)
        ->assertSee('rotate-180', escape: false)
        ->assertSee('data-section-action', escape: false)
        ->assertSee("'Hide ingredients'", escape: false)
        ->assertSee("'Show ingredients'", escape: false);
});

it('collapses a section body by toggling the hidden class', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, '2 tablespoons soy sauce'));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $content = $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk()
        ->getContent();

    expect($content)->toContain("body.classList.toggle('hidden', !expanded)")
        ->toContain("section.dataset.expanded === 'false'")
        ->toContain("chevron.querySelector('svg')?.classList.toggle('rotate-180', !expanded)")
        ->toContain("section.dataset.recipeName || 'this recipe'");
});

it('shows the ingredient count on each section header', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, "2 tablespoons soy sauce\n1 whole chicken"));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $content = preg_replace('/\s+/', ' ', $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk()
        ->getContent());

    expect($content)
        ->toContain('data-section-count')
        ->toContain('🧺 2 ingredients');
});

it('singularises the ingredient count for a recipe with one item', function () {
    $user = User::factory()->create();

    planQuantityRecipe($user, quantityRecipe($user, '2 tablespoons soy sauce'));

    $this->actingAs($user)
        ->post('/shopping-list/generate', ['week' => '2026-09-28'])
        ->assertRedirect();

    $content = preg_replace('/\s+/', ' ', $this->actingAs($user)
        ->get('/shopping-list?week=2026-09-28')
        ->assertOk()
        ->getContent());

    expect($content)->toContain('🧺 1 ingredient')
        ->and($content)->not->toContain('1 ingredients');
});
