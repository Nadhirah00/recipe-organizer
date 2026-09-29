<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\Middleware\ShareErrorsFromSession;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by a fresh user.
 *
 * @param  array<string, mixed>  $attributes
 */
function editableRecipe(array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Editable Recipe',
        'description' => 'Before edit.',
        'category' => 'Dinner',
        'cooking_time' => 30,
        'difficulty' => 'Easy',
        'servings' => 2,
        'ingredients' => "2 eggs\n1 teaspoon salt",
        'instructions' => "1. Beat the eggs.\n2. Cook.",
        'is_public' => true,
        ...$attributes,
    ]);
}

/**
 * A payload that satisfies every validation rule on the update endpoint.
 *
 * @return array<string, mixed>
 */
function validUpdate(array $attributes = []): array
{
    return [
        'name' => 'Edited Recipe',
        'description' => 'After edit.',
        'category' => 'Lunch',
        'cooking_time' => 45,
        'difficulty' => 'Medium',
        'servings' => 4,
        'ingredients' => "2 eggs\n1 teaspoon salt\n3 cups rice",
        'instructions' => "1. Beat the eggs.\n2. Cook the rice.\n3. Combine.",
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
        'is_public' => 0,
        ...$attributes,
    ];
}

it('renders every form field pre-filled for the owner', function () {
    $recipe = editableRecipe();

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}/edit");

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

    $response->assertSee('value="'.$recipe->name.'"', escape: false);
    $response->assertSee('value="'.$recipe->cooking_time.'"', escape: false);
    $response->assertSee('name="_method" value="PUT"', escape: false);
});

it('preserves the newlines in ingredients and instructions', function () {
    $recipe = editableRecipe();

    $response = $this->actingAs($recipe->user)->get("/recipes/{$recipe->id}/edit");

    $response->assertOk();
    $response->assertSee(e($recipe->ingredients), escape: false);
    $response->assertSee(e($recipe->instructions), escape: false);
});

it('saves a valid edit and redirects to the recipe', function () {
    $recipe = editableRecipe();

    $response = $this->actingAs($recipe->user)->put("/recipes/{$recipe->id}", validUpdate());

    $response->assertRedirect("/recipes/{$recipe->id}");

    $recipe->refresh();

    expect($recipe->name)->toBe('Edited Recipe');
    expect($recipe->cooking_time)->toBe(45);
    expect($recipe->servings)->toBe(4);
    expect($recipe->is_public)->toBeFalse();
});

it('round trips multi line ingredients and instructions through an update', function () {
    $recipe = editableRecipe();

    $payload = validUpdate([
        'ingredients' => "300g rice\n2 eggs\n1/2 teaspoon salt",
        'instructions' => "1. Rinse the rice.\n2. Boil the eggs.\n3. Plate up.",
    ]);

    $this->actingAs($recipe->user)->put("/recipes/{$recipe->id}", $payload);

    $recipe->refresh();

    expect($recipe->ingredients)->toBe("300g rice\n2 eggs\n1/2 teaspoon salt");
    expect($recipe->instructions)->toBe("1. Rinse the rice.\n2. Boil the eggs.\n3. Plate up.");

    expect($recipe->ingredientList())->toBe(['300g rice', '2 eggs', '1/2 teaspoon salt']);
    expect($recipe->instructionSteps())->toBe(['Rinse the rice.', 'Boil the eggs.', 'Plate up.']);
});

it('rejects an invalid edit and leaves the stored recipe untouched', function () {
    $recipe = editableRecipe();

    $response = $this->actingAs($recipe->user)
        ->from("/recipes/{$recipe->id}/edit")
        ->put("/recipes/{$recipe->id}", validUpdate([
            'name' => '',
            'servings' => 0,
        ]));

    $response->assertRedirect("/recipes/{$recipe->id}/edit");
    $response->assertSessionHasErrors(['name', 'servings']);
    $response->assertSessionHasInput('name', '');
    $response->assertSessionHasInput('description', 'After edit.');

    $recipe->refresh();

    expect($recipe->name)->toBe('Editable Recipe');
    expect($recipe->servings)->toBe(2);
});

it('renders the error summary and the per field messages', function () {
    $recipe = editableRecipe();

    // The array session driver used in tests does not survive ViewErrorBag
    // serialization, so the shared bag is dropped and the summary is skipped.
    $this->withoutMiddleware(ShareErrorsFromSession::class);

    $this->withViewErrors([
        'name' => 'The name field is required.',
        'servings' => 'The servings field must be at least 1.',
    ])->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}/edit")
        ->assertOk()
        ->assertSee('Please fix the following')
        ->assertSee('The name field is required.')
        ->assertSee('The servings field must be at least 1.');
});

it('does not let another user open the edit form', function () {
    $recipe = editableRecipe();

    $this->actingAs(User::factory()->create())
        ->get("/recipes/{$recipe->id}/edit")
        ->assertNotFound();
});

it('renders the tutorial preview and the live image target', function () {
    $recipe = editableRecipe([
        'tutorial_url' => 'https://www.youtube.com/watch?v=QqdcCHQlOe0',
    ]);

    $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}/edit")
        ->assertOk()
        ->assertSee('id="image-preview"', escape: false)
        ->assertSee('id="image-placeholder"', escape: false)
        ->assertSee('id="file-name"', escape: false)
        ->assertSee('https://www.youtube-nocookie.com/embed/QqdcCHQlOe0', escape: false);
});

it('hides the preview and shows the placeholder when there is no image', function () {
    $recipe = editableRecipe();

    $html = $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}/edit")
        ->assertOk()
        ->getContent();

    $matches = [];

    preg_match('/<img\s+id="image-preview".*?>/s', $html, $matches);

    expect(substr_count($matches[0], 'class='))->toBe(1);
});

it('shows the image preview when a photo already exists', function () {
    $recipe = editableRecipe();

    $recipe->forceFill(['image' => 'recipes/existing.jpg'])->save();

    $html = $this->actingAs($recipe->user)
        ->get("/recipes/{$recipe->id}/edit")
        ->assertOk()
        ->getContent();

    expect($html)
        ->toContain('src="http://recipe-organizer.test/storage/recipes/existing.jpg"')
        ->toContain('class="w-full h-full object-cover "');

    expect(preg_match('/id="image-placeholder"\s+class="hidden w-full/', $html))->toBe(1);
});
