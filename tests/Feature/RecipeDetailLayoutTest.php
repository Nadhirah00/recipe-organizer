<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by a fresh user.
 *
 * @param  array<string, mixed>  $attributes
 */
function detailRecipe(array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Detail Recipe',
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

it('splits ingredients into one trimmed entry per line', function () {
    $recipe = detailRecipe([
        'ingredients' => "2 chicken breasts\n\n- 3 tablespoons soy sauce\n  1 teaspoon sesame seeds",
    ]);

    expect($recipe->ingredientList())->toBe([
        '2 chicken breasts',
        '3 tablespoons soy sauce',
        '1 teaspoon sesame seeds',
    ]);
});

it('strips numbering from instruction steps', function () {
    $recipe = detailRecipe([
        'instructions' => "1. Cut the chicken.\n2) Mix the sauce.\n3. Cook until done.",
    ]);

    expect($recipe->instructionSteps())->toBe([
        'Cut the chicken.',
        'Mix the sauce.',
        'Cook until done.',
    ]);
});

it('returns empty lists for blank ingredients and instructions', function () {
    $recipe = new Recipe([
        'ingredients' => null,
        'instructions' => "  \n\n ",
    ]);

    expect($recipe->ingredientList())->toBe([]);
    expect($recipe->instructionSteps())->toBe([]);
});

it('leaves the raw columns untouched for the edit form', function () {
    $recipe = detailRecipe([
        'ingredients' => "2 chicken breasts\n1 teaspoon salt",
        'instructions' => "1. Cut the chicken.\n2. Cook it.",
    ]);

    expect($recipe->getRawOriginal('ingredients'))
        ->toBe("2 chicken breasts\n1 teaspoon salt");

    expect($recipe->getRawOriginal('instructions'))
        ->toBe("1. Cut the chicken.\n2. Cook it.");
});

it('renders one list row per ingredient line and one step per instruction line', function () {
    $recipe = detailRecipe([
        'ingredients' => "2 chicken breasts\n3 tablespoons soy sauce\n1 teaspoon sesame seeds",
        'instructions' => "1. Cut the chicken.\n2. Mix the sauce.\n3. Cook until done.",
    ]);

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}");

    $response->assertOk();

    expect(substr_count($response->getContent(), 'w-1.5 h-1.5 rounded-full bg-orange-400'))
        ->toBe(3);

    expect(substr_count($response->getContent(), 'rounded-full bg-orange-50 text-orange-600 text-xs font-bold'))
        ->toBe(3);

    $response->assertSee('2 chicken breasts');
    $response->assertSee('1 teaspoon sesame seeds');
});

it('does not double number the instruction steps', function () {
    $recipe = detailRecipe([
        'instructions' => '1. Beat the eggs in a bowl.',
    ]);

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee('Beat the eggs in a bowl.')
        ->assertDontSee('1. Beat the eggs in a bowl.');
});

it('uses a compact container and hero', function () {
    $recipe = detailRecipe();

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee('max-w-5xl', escape: false)
        ->assertSee('h-56 sm:h-64 md:h-80', escape: false)
        ->assertDontSee('md:h-[420px]', escape: false)
        ->assertSee('rounded-2xl', escape: false);
});

it('replaces the full width buttons with a compact icon row', function () {
    $recipe = detailRecipe();

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}");

    $response->assertOk();

    $response->assertSee('absolute top-4 right-4 z-10 flex items-center gap-1.5', escape: false);
    $response->assertSee('title="Add to favourites"', escape: false);
    $response->assertSee('title="Add to meal plan"', escape: false);

    $response->assertDontSee('Add to Favorites');
    $response->assertDontSee('Edit Recipe');
    $response->assertDontSee('Delete Recipe');
    $response->assertDontSee('📅 Add to Meal Plan');
});

it('shows edit and delete icons only to the owner', function () {
    $owner = detailRecipe();
    $other = detailRecipe();
    $viewer = User::factory()->create();

    $this->actingAs($owner->user)
        ->get("/recipes/{$owner->id}")
        ->assertOk()
        ->assertSee('title="Edit recipe"', escape: false)
        ->assertSee('title="Delete recipe"', escape: false);

    $this->actingAs($viewer)
        ->get("/recipes/{$other->id}")
        ->assertOk()
        ->assertDontSee('title="Edit recipe"', escape: false)
        ->assertDontSee('title="Delete recipe"', escape: false);
});

it('places the tutorial next to the ingredients', function () {
    $recipe = detailRecipe([
        'ingredients' => "Salt\nPepper",
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
    ]);

    $html = $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->getContent();

    expect(strpos($html, 'aspect-video'))->not->toBeFalse();
    expect(strpos($html, 'aspect-video'))->toBeLessThan(strpos($html, 'w-1.5 h-1.5 rounded-full bg-orange-400'));
});

it('offers a back link', function () {
    $recipe = detailRecipe();

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}")
        ->assertOk()
        ->assertSee('Back', escape: false);
});
