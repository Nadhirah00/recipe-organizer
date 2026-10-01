<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

/**
 * Create a recipe owned by the given user.
 */
function aiCandidateRecipe(User $user, array $attributes = []): Recipe
{
    return Recipe::create([
        'user_id' => $user->id,
        'name' => 'Candidate Recipe',
        'description' => 'A recipe offered to the AI planner.',
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
 * Stub Gemini with a plan keyed by day and meal type.
 */
function fakeGeminiPlan(array $plan): void
{
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode($plan),
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);
}

/**
 * The prompt text that was sent to Gemini.
 */
function geminiPrompt(): string
{
    $prompt = null;

    Http::assertSent(function (Request $request) use (&$prompt) {

        $prompt = $request->data()['contents'][0]['parts'][0]['text'] ?? '';

        return true;

    });

    return (string) $prompt;
}

/**
 * Request AI suggestions with the given temporary preferences.
 */
function generateSuggestions(
    User $user,
    array $payload = []
) {
    return test()
        ->actingAs($user)
        ->post(route('meal-planner.ai.generate'), [
            'use_custom' => 1,
            'meal_types' => 'Breakfast',
            'week' => '2026-09-28',
            ...$payload,
        ]);
}

it('offers the ai the user own recipes and other users public recipes', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    aiCandidateRecipe($user, ['name' => 'Own Private Porridge']);

    $publicRecipe = aiCandidateRecipe($other, [
        'name' => 'Public Hokkien Mee',
        'is_public' => true,
    ]);

    aiCandidateRecipe($other, ['name' => 'Secret Private Stew']);

    fakeGeminiPlan([
        'Monday' => [
            'Breakfast' => [
                'recipe_id' => $publicRecipe->id,
                'recipe_name' => 'Public Hokkien Mee',
            ],
        ],
    ]);

    generateSuggestions($user)->assertOk();

    /*
    |--------------------------------------------------------------------------
    | The user's own recipes and other users public recipes are both fair
    | game, but another user's private recipe must never leave the database
    |--------------------------------------------------------------------------
    */

    expect(geminiPrompt())
        ->toContain('Own Private Porridge')
        ->toContain('Public Hokkien Mee')
        ->not->toContain('Secret Private Stew');
});
it('plans a public recipe the user does not own', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $publicRecipe = aiCandidateRecipe($other, [
        'name' => 'Public Hokkien Mee',
        'is_public' => true,
    ]);

    fakeGeminiPlan([
        'Monday' => [
            'Breakfast' => [
                'recipe_id' => $publicRecipe->id,
                'recipe_name' => 'Public Hokkien Mee',
            ],
        ],
    ]);

    generateSuggestions($user)
        ->assertOk()
        ->assertSee('Public Hokkien Mee')
        ->assertSee('🌎 Public Recipe');
});

it('plans from public recipes when the user has none of their own', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    aiCandidateRecipe($other, [
        'name' => 'Public Hokkien Mee',
        'is_public' => true,
    ]);

    fakeGeminiPlan([]);

    generateSuggestions($user)
        ->assertOk()
        ->assertSee('Public Hokkien Mee')
        ->assertSee('🌎 Public Recipe');
});

it('refuses to plan when the user has no own or public recipes', function () {
    $user = User::factory()->create();

    generateSuggestions($user)->assertRedirect('/preferences');
});
