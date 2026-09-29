<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * A payload that satisfies every validation rule on the store endpoint.
 *
 * @param  array<string, mixed>  $attributes
 * @return array<string, mixed>
 */
function createRecipePayload(array $attributes = []): array
{
    return [
        'name' => 'Brand New Recipe',
        'description' => 'Freshly created.',
        'category' => 'Dinner',
        'cooking_time' => 25,
        'difficulty' => 'Easy',
        'servings' => 2,
        'ingredients' => "2 eggs\n1/2 teaspoon salt",
        'instructions' => "1. Beat the eggs.\n2. Cook.",
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
        'is_public' => 1,
        ...$attributes,
    ];
}

it('renders every field on the create form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/recipes/create');

    $response->assertOk();

    foreach ([
        'name',
        'description',
        'image',
        'category',
        'cooking_time',
        'difficulty',
        'servings',
        'ingredients',
        'instructions',
        'tutorial_url',
        'is_public',
    ] as $field) {

        $response->assertSee('name="'.$field.'"', escape: false);
    }

    $response->assertSee('action="http://recipe-organizer.test/recipes"', escape: false);
    $response->assertSee('enctype="multipart/form-data"', escape: false);
    $response->assertDontSee('_method" value="PUT"', escape: false);
});

it('preselects Dinner, Easy and Public for a new recipe', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)
        ->get('/recipes/create')
        ->assertOk()
        ->getContent();

    expect(preg_match('/<option value="Dinner" selected>/', $html))->toBe(1);
    expect(preg_match('/<option value="Easy" selected>/', $html))->toBe(1);

    $private = [];
    preg_match('/<input\s+type="radio"[^>]*name="is_public"[^>]*value="0"[^>]*>/', $html, $private);

    $public = [];
    preg_match('/<input\s+type="radio"[^>]*name="is_public"[^>]*value="1"[^>]*>/', $html, $public);

    expect($public[0] ?? '')->toContain('checked');
    expect($private[0] ?? 'public checked')->not->toContain('checked');
});

it('keeps a single class attribute on the image preview', function () {
    $user = User::factory()->create();

    $html = $this->actingAs($user)
        ->get('/recipes/create')
        ->assertOk()
        ->getContent();

    $preview = [];
    preg_match('/<img\s+id="image-preview".*?>/s', $html, $preview);

    expect(substr_count($preview[0], 'class='))->toBe(1);
    expect($preview[0])->toContain('class="w-full h-full object-cover hidden"');
    expect($html)->toContain('id="file-name"');
    expect($html)->toContain('id="image-placeholder"');
});

it('creates a recipe for the authenticated user and redirects to the index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/recipes', createRecipePayload());

    $response->assertRedirect('/recipes');

    $recipe = Recipe::where('name', 'Brand New Recipe')->first();

    expect($recipe)->not->toBeNull();
    expect($recipe->user_id)->toBe($user->id);
    expect($recipe->cooking_time)->toBe(25);
    expect($recipe->is_public)->toBeTrue();
});

it('stores multi line ingredients and instructions verbatim', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/recipes', createRecipePayload([
            'ingredients' => "300g rice\n2 eggs\n1/2 teaspoon salt",
            'instructions' => "1. Rinse the rice.\n2. Boil the eggs.\n3. Plate up.",
        ]));

    $recipe = Recipe::where('name', 'Brand New Recipe')->first();

    expect($recipe->ingredients)->toBe("300g rice\n2 eggs\n1/2 teaspoon salt");
    expect($recipe->instructions)->toBe("1. Rinse the rice.\n2. Boil the eggs.\n3. Plate up.");
});

it('rejects an invalid submission and does not create a recipe', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/recipes/create')
        ->post('/recipes', createRecipePayload([
            'name' => '',
            'servings' => 0,
        ]));

    $response->assertRedirect('/recipes/create');
    $response->assertSessionHasErrors(['name', 'servings']);

    expect(Recipe::count())->toBe(0);
});

it('rejects a duplicate name within the same account', function () {
    $user = User::factory()->create();

    Recipe::create(createRecipePayload() + ['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->from('/recipes/create')
        ->post('/recipes', createRecipePayload());

    $response->assertRedirect('/recipes/create');
    $response->assertSessionHas('error', 'You already have a recipe with this name.');

    expect(Recipe::count())->toBe(1);
});

it('allows the same name on a different account', function () {
    $user = User::factory()->create();

    Recipe::create(createRecipePayload() + ['user_id' => User::factory()->create()->id]);

    $this->actingAs($user)
        ->post('/recipes', createRecipePayload())
        ->assertRedirect('/recipes');

    expect(Recipe::count())->toBe(2);
});
