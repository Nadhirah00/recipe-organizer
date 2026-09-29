<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'newest');

        $query = Favorite::with('recipe')
            ->where('favorites.user_id', Auth::id());

        switch ($sort) {

            case 'oldest':

                $query->orderBy(
                    'favorites.created_at',
                    'asc'
                );

                break;

            case 'name_asc':

                $query->join(
                    'recipes',
                    'favorites.recipe_id',
                    '=',
                    'recipes.id'
                )
                    ->select('favorites.*')
                    ->orderBy(
                        'recipes.name',
                        'asc'
                    );

                break;

            case 'name_desc':

                $query->join(
                    'recipes',
                    'favorites.recipe_id',
                    '=',
                    'recipes.id'
                )
                    ->select('favorites.*')
                    ->orderBy(
                        'recipes.name',
                        'desc'
                    );

                break;

            case 'time_asc':

                $query->join(
                    'recipes',
                    'favorites.recipe_id',
                    '=',
                    'recipes.id'
                )
                    ->select('favorites.*')
                    ->orderBy(
                        'recipes.cooking_time',
                        'asc'
                    );

                break;

            case 'time_desc':

                $query->join(
                    'recipes',
                    'favorites.recipe_id',
                    '=',
                    'recipes.id'
                )
                    ->select('favorites.*')
                    ->orderBy(
                        'recipes.cooking_time',
                        'desc'
                    );

                break;

            case 'newest':

            default:

                $sort = 'newest';

                $query->orderBy(
                    'favorites.created_at',
                    'desc'
                );

                break;
        }

        $favorites = $query
            ->paginate(12)
            ->withQueryString();

        return view(
            'favorites.index',
            compact(
                'favorites',
                'sort'
            )
        );

    }

    public function store(Request $request, $recipeId)
    {
        /*
        |--------------------------------------------------------------------------
        | Allow own recipes or public recipes
        |--------------------------------------------------------------------------
        */

        $recipe = Recipe::where('id', $recipeId)
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
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Save favorite for the logged-in user
        |--------------------------------------------------------------------------
        */

        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'recipe_id' => $recipe->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'favorite' => true,
            ]);
        }

        return back()->with(
            'success',
            'Recipe added to favorites!'
        );

    }

    public function destroy(Request $request, $recipeId)
    {
        Favorite::where('user_id', Auth::id())
            ->where('recipe_id', $recipeId)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'favorite' => false,
            ]);
        }

        return back()->with(
            'success',
            'Recipe removed from favorites!'
        );
    }
}
