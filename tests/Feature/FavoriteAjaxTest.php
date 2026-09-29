<?php

use App\Models\Favorite;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe for the given relationship.
 *
 * @param  array<string, mixed>  $attributes
 */
function favoriteRecipe(User $owner, array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => $owner->id,
        'name' => 'Favorite Test Dish',
        'description' => 'A recipe used for favorite testing.',
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

it('adds a favorite over JSON and returns the new state', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create());

    $response = $this->actingAs($user)->postJson('/recipes/'.$recipe->id.'/favorite');

    $response->assertOk()->assertJson(['favorite' => true]);

    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
    ]);
});

it('is idempotent when adding an existing favorite over JSON', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create());

    Favorite::create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->postJson('/recipes/'.$recipe->id.'/favorite');

    $response->assertOk()->assertJson(['favorite' => true]);

    $this->assertSame(
        1,
        Favorite::where('user_id', $user->id)->where('recipe_id', $recipe->id)->count()
    );
});

it('removes a favorite over JSON and returns the new state', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create());

    Favorite::create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->deleteJson('/recipes/'.$recipe->id.'/favorite');

    $response->assertOk()->assertJson(['favorite' => false]);

    $this->assertDatabaseMissing('favorites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
    ]);
});

it('removing a favorite that does not exist over JSON stays harmless', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create());

    $response = $this->actingAs($user)->deleteJson('/recipes/'.$recipe->id.'/favorite');

    $response->assertOk()->assertJson(['favorite' => false]);
});

it('redirects with a success flash for a plain form submit', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create());

    $response = $this->actingAs($user)
        ->from('/explore')
        ->post('/recipes/'.$recipe->id.'/favorite');

    $response->assertRedirect('/explore');
    $response->assertSessionHas('success', 'Recipe added to favorites!');

    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
    ]);
});

it('rejects someone else private recipe over JSON', function () {
    $user = User::factory()->create();
    $recipe = favoriteRecipe(User::factory()->create(), ['is_public' => false]);

    $this->actingAs($user)->postJson('/recipes/'.$recipe->id.'/favorite')
        ->assertNotFound();
});

it('redirects guests away from the favorite endpoints', function () {
    $recipe = favoriteRecipe(User::factory()->create());

    $this->postJson('/recipes/'.$recipe->id.'/favorite')->assertUnauthorized();
});
