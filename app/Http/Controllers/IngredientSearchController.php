<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngredientSearchController extends Controller
{
    public function index()
    {
        return view('ingredient-search.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|string',
        ]);

        $userIngredients = $this->normalizeUserIngredients(
            $request->input('ingredients')
        );

        if (empty($userIngredients)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter at least one ingredient.'
                );
        }

        $recipes = Recipe::where('user_id', Auth::id())
            ->get();

        $results = $recipes
            ->map(function ($recipe) use ($userIngredients) {

                $recipeIngredients = $this->normalizeRecipeIngredients(
                    $recipe->ingredients
                );

                if (empty($recipeIngredients)) {
                    return null;
                }

                $matched = [];
                $missing = [];

                foreach ($recipeIngredients as $recipeIngredient) {

                    $isMatched = false;

                    foreach ($userIngredients as $userIngredient) {

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
                        $matched[] = $recipeIngredient;
                    } else {
                        $missing[] = $recipeIngredient;
                    }
                }

                $totalIngredients = count($recipeIngredients);
                $matchedCount = count($matched);

                $percentage = $totalIngredients > 0
                    ? round(($matchedCount / $totalIngredients) * 100)
                    : 0;

                return [
                    'recipe' => $recipe,
                    'percentage' => $percentage,
                    'matched' => $matched,
                    'missing' => $missing,
                    'matched_count' => $matchedCount,
                    'total_count' => $totalIngredients,
                ];
            })
            ->filter(fn ($result) =>
                $result !== null &&
                $result['matched_count'] > 0
            )
            ->sortByDesc('percentage')
            ->values();

        return view(
            'ingredient-search.results',
            compact(
                'userIngredients',
                'results'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize user's typed ingredients
    |--------------------------------------------------------------------------
    */

    private function normalizeUserIngredients(
        string $input
    ): array {

        $items = preg_split(
            '/[\r\n,]+/',
            $input
        );


        $normalized = [];


        foreach ($items as $item) {

            $item = trim($item);

            if ($item === '') {
                continue;
            }


            $item = $this->normalizeIngredientName(
                $item
            );


            if ($item !== '') {
                $normalized[$item] = $item;
            }
        }


        return array_values($normalized);
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize recipe ingredients
    |--------------------------------------------------------------------------
    */

    private function normalizeRecipeIngredients(
        ?string $ingredients
    ): array {

        if (!$ingredients) {
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
                '/^\d+(?:[\/\.]\d+)?\s*(?:' .
                'tablespoons?|tbsp|' .
                'teaspoons?|tsp|' .
                'cups?|' .
                'grams?|g|' .
                'kilograms?|kg|' .
                'milliliters?|ml|' .
                'liters?|l|' .
                'pieces?|pcs|' .
                'cloves?|' .
                'slices?' .
                ')\s*(?:of\s+)?/i',
                '',
                $line
            );


            $line = trim($line);


            /*
            |--------------------------------------------------------------------------
            | Normalize ingredient name
            |--------------------------------------------------------------------------
            */

            $line = $this->normalizeIngredientName(
                $line
            );


            if ($line !== '') {
                $normalized[$line] = $line;
            }
        }


        return array_values($normalized);
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize common ingredient names
    |--------------------------------------------------------------------------
    */

    private function normalizeIngredientName(string $ingredient): string
    {
        $ingredient = strtolower(trim($ingredient));

        /*
        |--------------------------------------------------------------------------
        | Remove punctuation
        |--------------------------------------------------------------------------
        */

        $ingredient = str_replace(
            ['-', '_', '(', ')', ',', '.'],
            ' ',
            $ingredient
        );

        /*
        |--------------------------------------------------------------------------
        | Remove common preparation/descriptive words
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Remove common measurement/quantity leftovers
        |--------------------------------------------------------------------------
        */

        $ingredient = preg_replace(
            '/^\s*
            (?:\d+(?:[\/\.]\d+)?)?
            \s*
            (?:cups?|tablespoons?|tbsp|teaspoons?|tsp|
            grams?|g|kilograms?|kg|
            milliliters?|ml|liters?|l|
            cloves?|pieces?|pcs|slices?)
            \s*(?:of\s+)?
            /ix',
            '',
            $ingredient
        );

        /*
        |--------------------------------------------------------------------------
        | Normalize whitespace
        |--------------------------------------------------------------------------
        */

        $ingredient = preg_replace(
            '/\s+/',
            ' ',
            $ingredient
        );

        $ingredient = trim($ingredient);

        /*
        |--------------------------------------------------------------------------
        | Common ingredient names / variations
        |--------------------------------------------------------------------------
        */

        $specialCases = [

            // Chicken
            'chicken breasts' => 'chicken breast',
            'chicken breast' => 'chicken',
            'chicken pieces' => 'chicken',
            'chicken fillet' => 'chicken',
            'chicken fillets' => 'chicken',

            // Eggs
            'eggs' => 'egg',

            // Rice
            'cooked rice' => 'rice',
            'white rice' => 'rice',
            'brown rice' => 'rice',

            // Vegetables
            'tomatoes' => 'tomato',
            'onions' => 'onion',
            'potatoes' => 'potato',
            'carrots' => 'carrot',
            'mushrooms' => 'mushroom',
            'peppers' => 'pepper',
            'bell peppers' => 'bell pepper',
            'bell pepper' => 'pepper',

            // Fruits
            'bananas' => 'banana',
            'strawberries' => 'strawberry',

            // Garlic
            'garlic cloves' => 'garlic',
            'clove garlic' => 'garlic',

            // Herbs
            'parsley leaves' => 'parsley',

            // Flour
            'all purpose flour' => 'flour',
            'all-purpose flour' => 'flour',

            // Pasta
            'spaghetti' => 'pasta',

            // Sauces
            'soy sauce' => 'soy sauce',
            'oyster sauce' => 'oyster sauce',

        ];

        if (isset($specialCases[$ingredient])) {
            return $specialCases[$ingredient];
        }

        /*
        |--------------------------------------------------------------------------
        | Basic plural handling
        |--------------------------------------------------------------------------
        */

        if (
            str_ends_with($ingredient, 'ies') &&
            strlen($ingredient) > 4
        ) {
            return substr($ingredient, 0, -3) . 'y';
        }

        if (
            str_ends_with($ingredient, 'es') &&
            strlen($ingredient) > 4
        ) {
            return substr($ingredient, 0, -2);
        }

        if (
            str_ends_with($ingredient, 's') &&
            !str_ends_with($ingredient, 'ss') &&
            strlen($ingredient) > 3
        ) {
            return substr($ingredient, 0, -1);
        }

        return $ingredient;
    }


    /*
    |--------------------------------------------------------------------------
    | Determine whether two ingredients match
    |--------------------------------------------------------------------------
    */

    private function ingredientsMatch(
        string $userIngredient,
        string $recipeIngredient
    ): bool {

        $userIngredient = $this->normalizeIngredientName(
            $userIngredient
        );

        $recipeIngredient = $this->normalizeIngredientName(
            $recipeIngredient
        );

        if (
            $userIngredient === '' ||
            $recipeIngredient === ''
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Exact match
        |--------------------------------------------------------------------------
        */

        if ($userIngredient === $recipeIngredient) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | User ingredient is contained in recipe ingredient
        |
        | chicken → chicken breast
        | rice → cooked rice
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                ' ' . $recipeIngredient . ' ',
                ' ' . $userIngredient . ' '
            )
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Recipe ingredient is contained in user ingredient
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                ' ' . $userIngredient . ' ',
                ' ' . $recipeIngredient . ' '
            )
        ) {
            return true;
        }

        return false;
    }
}
