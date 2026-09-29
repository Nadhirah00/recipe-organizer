<?php

use App\Models\Favorite;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by a fresh user.
 *
 * @param  array<string, mixed>  $attributes
 */
function cardRecipe(array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Test Recipe',
        'description' => 'A recipe used for testing.',
        'category' => 'Dinner',
        'cooking_time' => 30,
        'difficulty' => 'Easy',
        'servings' => 2,
        'ingredients' => 'Salt',
        'instructions' => 'Cook it.',
        'is_public' => true,
        ...$attributes,
    ]);
}

it('links the whole card on the my recipes page', function () {
    $recipe = cardRecipe(['name' => 'Whole Card Test']);

    $response = $this->actingAs($recipe->user)->get('/recipes');

    $response->assertOk();

    $response->assertSee(
        'class="absolute inset-0 z-0"',
        escape: false
    );

    $response->assertSee(
        'aria-label="View Whole Card Test"',
        escape: false
    );

    $response->assertSee(url('/recipes/'.$recipe->id), escape: false);

    $response->assertDontSee('View Recipe');
});

it('shows edit and delete icon actions on the my recipes page', function () {
    $recipe = cardRecipe();

    $response = $this->actingAs($recipe->user)->get('/recipes');

    $response->assertOk();
    $response->assertSee('title="Edit recipe"', escape: false);
    $response->assertSee('title="Delete recipe"', escape: false);
    $response->assertSee(url('/recipes/'.$recipe->id.'/edit'), escape: false);
});

it('shows a meal plan and favourite action on explore ingredient matches', function () {
    $user = User::factory()->create();
    cardRecipe([
        'user_id' => $user->id,
        'name' => 'Ingredient Match Card',
        'is_public' => true,
        'ingredients' => 'chicken breast',
    ]);

    $response = $this->actingAs($user)->get(
        '/explore?mode=ingredients&ingredients=chicken'
    );

    $response->assertOk();
    $response->assertSee('aria-label="View Ingredient Match Card"', escape: false);
    $response->assertSee('title="Add to meal plan"', escape: false);
    $response->assertSee('title="Add to favourites"', escape: false);
    $response->assertDontSee('View Recipe');
});

it('shows a meal plan action on every card list', function (string $path) {
    $recipe = cardRecipe();

    $response = $this->actingAs($recipe->user)->get($path);

    $response->assertOk();
    $response->assertSee('title="Add to meal plan"', escape: false);
    $response->assertSee('aria-label="View Test Recipe"', escape: false);
})->with([
    'my recipes' => '/recipes',
    'explore' => '/explore',
    'discover' => '/discover',
]);

it('shows a favourite icon action on explore and discover', function (string $path) {
    $recipe = cardRecipe();

    $response = $this->actingAs($recipe->user)->get($path);

    $response->assertOk();
    $response->assertSee('title="Add to favourites"', escape: false);
})->with([
    'explore' => '/explore',
    'discover' => '/discover',
]);

it('marks an already favourited recipe on explore', function () {
    $user = User::factory()->create();
    $recipe = cardRecipe(['user_id' => $user->id]);

    Favorite::create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $this->actingAs($user)
        ->get('/explore')
        ->assertOk()
        ->assertSee('title="Remove from favourites"', escape: false)
        ->assertDontSee('title="Add to favourites"', escape: false);
});

it('links the whole card on the favourites page', function () {
    $user = User::factory()->create();
    $recipe = cardRecipe(['user_id' => $user->id, 'name' => 'Favourite Card']);

    Favorite::create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->get('/favorites');

    $response->assertOk();
    $response->assertSee('aria-label="View Favourite Card"', escape: false);
    $response->assertSee('title="Add to meal plan"', escape: false);
    $response->assertDontSee('View Recipe');
});

it('links the whole card on ingredient search results', function () {
    $user = User::factory()->create();
    cardRecipe(['user_id' => $user->id, 'name' => 'Match Card']);

    $response = $this->actingAs($user)->post('/find-by-ingredients', [
        'ingredients' => 'salt',
    ]);

    $response->assertOk();
    $response->assertSee('aria-label="View Match Card"', escape: false);
    $response->assertSee('title="Add to meal plan"', escape: false);
    $response->assertDontSee('View Recipe');
});

it('never puts a form inside the whole card link', function () {
    $user = User::factory()->create();
    $recipe = cardRecipe(['user_id' => $user->id]);

    $html = $this->actingAs($user)->get('/explore')->getContent();

    $overlayStart = strpos($html, 'class="absolute inset-0 z-0"');

    expect($overlayStart)->not->toBeFalse();

    $overlayEnd = strpos($html, '</a>', $overlayStart);

    expect(substr($html, $overlayStart, $overlayEnd - $overlayStart))
        ->not->toContain('<form')
        ->not->toContain('<button');

    expect($recipe->exists)->toBeTrue();
});
