<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a public recipe owned by a fresh user.
 *
 * @param  array<string, mixed>  $attributes
 */
function publicRecipe(array $attributes = []): Recipe
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

it('embeds a youtube watch url in an inline player', function () {
    $recipe = publicRecipe([
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
    ]);

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}");

    $response->assertOk();

    $response->assertSee(
        'https://www.youtube-nocookie.com/embed/QqdcCHQlOe0',
        escape: false
    );

    $response->assertSee('<iframe', escape: false);
    $response->assertSee('aspect-video', escape: false);
});

it('keeps the watch on youtube link alongside the embed', function () {
    $recipe = publicRecipe([
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
    ]);

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee('Watch on YouTube');
});

it('embeds a short youtu.be url', function () {
    $recipe = publicRecipe([
        'tutorial_url' => 'https://youtu.be/qXPhVYpQLPA',
    ]);

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee(
            'https://www.youtube-nocookie.com/embed/qXPhVYpQLPA',
            escape: false
        );
});

it('falls back to a link for a non youtube tutorial url', function () {
    $recipe = publicRecipe([
        'tutorial_url' => 'https://vimeo.com/123456789',
    ]);

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}");

    $response->assertOk();

    $response->assertSee('Watch Tutorial');
    $response->assertDontSee('<iframe', escape: false);
    $response->assertDontSee('youtube-nocookie', escape: false);
});

it('hides the tutorial section when there is no tutorial url', function () {
    $recipe = publicRecipe(['tutorial_url' => null]);

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}");

    $response->assertOk();

    $response->assertDontSee('<iframe', escape: false);
    $response->assertDontSee('Watch Tutorial');
    $response->assertDontSee('Watch on YouTube');
});
