<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExploreRecipeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Main Explore Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $mode = $request->input(
            'mode',
            'browse'
        );

        /*
        |--------------------------------------------------------------------------
        | Browse Public Recipes
        |--------------------------------------------------------------------------
        */

        $sort = $request->input(
            'sort',
            'newest'
        );

        $publicQuery = Recipe::with([
            'user',
            'favorites',
        ])
            ->where('is_public', true);

        /*
        |--------------------------------------------------------------------------
        | Public Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $publicQuery->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%'.$search.'%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%'.$search.'%'
                    )
                    ->orWhere(
                        'ingredients',
                        'like',
                        '%'.$search.'%'
                    );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $publicQuery->where(
                'category',
                $request->category
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'oldest':

                $publicQuery->orderBy(
                    'created_at',
                    'asc'
                );

                break;

            case 'name_asc':

                $publicQuery->orderBy(
                    'name',
                    'asc'
                );

                break;

            case 'name_desc':

                $publicQuery->orderBy(
                    'name',
                    'desc'
                );

                break;

            case 'time_asc':

                $publicQuery->orderBy(
                    'cooking_time',
                    'asc'
                );

                break;

            case 'time_desc':

                $publicQuery->orderBy(
                    'cooking_time',
                    'desc'
                );

                break;

            case 'newest':

            default:

                $sort = 'newest';

                $publicQuery->orderBy(
                    'created_at',
                    'desc'
                );

                break;
        }

        $publicRecipes = $publicQuery
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Ingredient Search
        |--------------------------------------------------------------------------
        */

        $userIngredients = [];

        $ingredientResults = collect();

        if ($request->filled('ingredients')) {

            $userIngredients =
                $this->normalizeUserIngredients(
                    $request->input('ingredients')
                );

            if (! empty($userIngredients)) {

                /*
                |--------------------------------------------------------------------------
                | Own recipes + public recipes
                |--------------------------------------------------------------------------
                |
                | This lets the user find:
                |
                | 1. Their own recipes, including private ones
                | 2. Public recipes from other users
                |
                */

                $recipes = Recipe::with([
                    'user',
                    'favorites',
                ])
                    ->where(function ($query) {

                        $query->where(
                            'user_id',
                            Auth::id()
                        )
                            ->orWhere(
                                'is_public',
                                true
                            );

                    })
                    ->get();

                $ingredientResults = $recipes

                    ->map(function ($recipe) use (
                        $userIngredients
                    ) {

                        $recipeIngredients =
                            $this->normalizeRecipeIngredients(
                                $recipe->ingredients
                            );

                        if (empty($recipeIngredients)) {
                            return null;
                        }

                        $matched = [];
                        $missing = [];

                        foreach (
                            $recipeIngredients as $recipeIngredient
                        ) {

                            $isMatched = false;

                            foreach (
                                $userIngredients as $userIngredient
                            ) {

                                if (
                                    $this->ingredientsMatch(
                                        $userIngredient,
                                        $recipeIngredient
                                    )
                                ) {

                                    $isMatched = true;

                                    break;
                                }
                            }

                            if ($isMatched) {

                                $matched[] =
                                    $recipeIngredient;

                            } else {

                                $missing[] =
                                    $recipeIngredient;
                            }
                        }

                        $totalIngredients =
                            count(
                                $recipeIngredients
                            );

                        $matchedCount =
                            count($matched);

                        $percentage =
                            $totalIngredients > 0
                                ? round(
                                    (
                                        $matchedCount /
                                        $totalIngredients
                                    ) * 100
                                )
                                : 0;

                        /*
                        |--------------------------------------------------------------------------
                        | Only show recipes with at least one match
                        |--------------------------------------------------------------------------
                        */

                        if ($matchedCount <= 0) {
                            return null;
                        }

                        return [
                            'recipe' => $recipe,

                            'percentage' => $percentage,

                            'matched' => $matched,

                            'missing' => $missing,

                            'matched_count' => $matchedCount,

                            'total_count' => $totalIngredients,
                        ];
                    })

                    ->filter()

                    ->sortByDesc(
                        'percentage'
                    )

                    ->values();
            }
        }

        return view(
            'recipes.explore',
            compact(
                'mode',
                'sort',
                'publicRecipes',
                'userIngredients',
                'ingredientResults'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize User Ingredients
    |--------------------------------------------------------------------------
    */

    private function normalizeUserIngredients(
        string $input
    ): array {

        $items = preg_split(
            '/[\r\n,;\/&|]+/',
            $input
        );

        $normalized = [];

        foreach ($items as $item) {

            $item = trim($item);

            if ($item === '') {
                continue;
            }

            $item =
                $this->normalizeIngredientName(
                    $item
                );

            if ($item !== '') {

                $normalized[$item] =
                    $item;
            }
        }

        return array_values(
            $normalized
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Recipe Ingredients
    |--------------------------------------------------------------------------
    */

    private function normalizeRecipeIngredients(
        ?string $ingredients
    ): array {

        if (! $ingredients) {
            return [];
        }

        $lines = preg_split(
            '/\r\n|\r|\n/',
            $ingredients
        );

        $normalized = [];

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Remove quantities and units
            |--------------------------------------------------------------------------
            */

            $line = preg_replace(
                '/^\d+(?:[\/\.]\d+)?\s*(?:'.
                'tablespoons?|tbsp|'.
                'teaspoons?|tsp|'.
                'cups?|'.
                'grams?|g|'.
                'kilograms?|kg|'.
                'milliliters?|ml|'.
                'liters?|l|'.
                'pieces?|pcs|'.
                'cloves?|'.
                'slices?'.
                ')\s*(?:of\s+)?/i',
                '',
                $line
            );

            $line =
                $this->normalizeIngredientName(
                    trim($line)
                );

            if ($line !== '') {

                $normalized[$line] =
                    $line;
            }
        }

        return array_values(
            $normalized
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ingredient Name Normalization
    |--------------------------------------------------------------------------
    */

    private function normalizeIngredientName(
        string $ingredient
    ): string {

        $ingredient =
            strtolower(trim($ingredient));

        $ingredient = str_replace(
            [
                '-',
                '_',
                '(',
                ')',
                ',',
                '.',
                '/',
                ';',
                '&',
                '|',
            ],
            ' ',
            $ingredient
        );

        $ingredient = preg_replace(
            '/\b(
                fresh|
                chopped|
                minced|
                sliced|
                diced|
                cooked|
                ground|
                grated|
                crushed|
                finely|
                roughly|
                small|
                medium|
                large|
                boneless|
                skinless|
                unsalted|
                softened
            )\b/ix',
            '',
            $ingredient
        );

        $ingredient = preg_replace(
            '/\s+/',
            ' ',
            $ingredient
        );

        $ingredient =
            trim($ingredient);

        $specialCases = [

            'chicken breasts' => 'chicken',

            'chicken breast' => 'chicken',

            'chicken pieces' => 'chicken',

            'chicken fillet' => 'chicken',

            'chicken fillets' => 'chicken',

            'eggs' => 'egg',

            'cooked rice' => 'rice',

            'white rice' => 'rice',

            'brown rice' => 'rice',

            'tomatoes' => 'tomato',

            'onions' => 'onion',

            'potatoes' => 'potato',

            'carrots' => 'carrot',

            'mushrooms' => 'mushroom',

            'peppers' => 'pepper',

            'bell peppers' => 'bell pepper',

            'bell pepper' => 'pepper',

            'bananas' => 'banana',

            'strawberries' => 'strawberry',

            'garlic cloves' => 'garlic',

            'clove garlic' => 'garlic',

            'parsley leaves' => 'parsley',

            'all purpose flour' => 'flour',

            'all-purpose flour' => 'flour',

            'spaghetti' => 'pasta',
        ];

        if (
            isset(
                $specialCases[$ingredient]
            )
        ) {

            return $specialCases[$ingredient];
        }

        if (
            str_ends_with(
                $ingredient,
                'ies'
            )
            &&
            strlen($ingredient) > 4
        ) {

            return
                substr(
                    $ingredient,
                    0,
                    -3
                ).'y';
        }

        if (
            str_ends_with(
                $ingredient,
                'es'
            )
            &&
            strlen($ingredient) > 4
        ) {

            return
                substr(
                    $ingredient,
                    0,
                    -2
                );
        }

        if (
            str_ends_with(
                $ingredient,
                's'
            )
            &&
            ! str_ends_with(
                $ingredient,
                'ss'
            )
            &&
            strlen($ingredient) > 3
        ) {

            return
                substr(
                    $ingredient,
                    0,
                    -1
                );
        }

        return $ingredient;
    }

    /*
    |--------------------------------------------------------------------------
    | Ingredient Matching
    |--------------------------------------------------------------------------
    */

    private function ingredientsMatch(
        string $userIngredient,
        string $recipeIngredient
    ): bool {

        $userIngredient =
            $this->normalizeIngredientName(
                $userIngredient
            );

        $recipeIngredient =
            $this->normalizeIngredientName(
                $recipeIngredient
            );

        if (
            $userIngredient === '' ||
            $recipeIngredient === ''
        ) {
            return false;
        }

        if (
            $userIngredient ===
            $recipeIngredient
        ) {

            return true;
        }

        if (
            str_contains(
                ' '.
                $recipeIngredient.
                ' ',
                ' '.
                $userIngredient.
                ' '
            )
        ) {

            return true;
        }

        if (
            str_contains(
                ' '.
                $userIngredient.
                ' ',
                ' '.
                $recipeIngredient.
                ' '
            )
        ) {

            return true;
        }

        return false;
    }
}
