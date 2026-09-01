<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
    $query = Recipe::where(
    'user_id',
    Auth::id()
    );

    /*Search*/
    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere(
                    'description',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'ingredients',
                    'like',
                    '%' . $search . '%'
                );

        });
    }

    /*Category*/
    if ($request->filled('category')) {

        $query->where(
            'category',
            $request->category
        );
    }

    /*Difficulty*/
    if ($request->filled('difficulty')) {

        $query->where(
            'difficulty',
            $request->difficulty
        );
    }

    /*Cooking Time*/
    if ($request->filled('max_time')) {

        $query->where(
            'cooking_time',
            '<=',
            $request->max_time
        );
    }

    /*Sorting*/
    $sort = $request->input(
        'sort',
        'newest'
    );


    switch ($sort) {

        case 'oldest':

            $query->orderBy(
                'created_at',
                'asc'
            );

            break;


        case 'name_asc':

            $query->orderBy(
                'name',
                'asc'
            );

            break;


        case 'name_desc':

            $query->orderBy(
                'name',
                'desc'
            );

            break;


        case 'time_asc':

            $query->orderBy(
                'cooking_time',
                'asc'
            );

            break;


        case 'time_desc':

            $query->orderBy(
                'cooking_time',
                'desc'
            );

            break;


        case 'newest':
        default:

            $sort = 'newest';

            $query->orderBy(
                'created_at',
                'desc'
            );

            break;
    }

    /*Pagination*/

    $recipes = $query
        ->paginate(12)
        ->withQueryString();


    return view(
        'recipes.index',
        compact(
            'recipes',
            'sort'
        )
    );

    }


    public function create()
    {
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'cooking_time' => 'required|integer|min:1',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'servings' => 'required|integer|min:1',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tutorial_url' => 'nullable|url|max:255',
            'is_public' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('recipes', 'public');
        }

        // Connect the recipe to the logged-in user
        $validated['user_id'] = auth()->id();

        $duplicate = Recipe::where('user_id', Auth::id())
            ->whereRaw(
                'LOWER(name) = ?',
                [strtolower(trim($request->name))]
            )
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You already have a recipe with this name.'
                );
        }

        Recipe::create($validated);

        return redirect('/recipes');
    }

    public function edit($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'cooking_time' => 'required|integer|min:1',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'servings' => 'required|integer|min:1',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tutorial_url' => 'nullable|url|max:255',
            'is_public' => 'required|boolean',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Check duplicate recipe name BEFORE updating
        |--------------------------------------------------------------------------
        */

        $duplicate = Recipe::where(
            'user_id',
            Auth::id()
        )
        ->whereRaw(
            'LOWER(name) = ?',
            [strtolower(trim($request->name))]
        )
        ->where(
            'id',
            '!=',
            $recipe->id
        )
        ->exists();


        if ($duplicate) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'You already have another recipe with this name.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Replace image if a new image was uploaded
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            if ($recipe->image) {

                $oldImagePath = storage_path(
                    'app/public/' . $recipe->image
                );

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }


            $validated['image'] = $request
                ->file('image')
                ->store('recipes', 'public');

        } else {

            unset($validated['image']);
        }


        /*
        |--------------------------------------------------------------------------
        | Update recipe
        |--------------------------------------------------------------------------
        */

        $recipe->update($validated);


        return redirect(
            '/recipes/' . $recipe->id
        )->with(
            'success',
            'Recipe updated successfully!'
        );
    }

    public function destroy($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($recipe->image) {

            $imagePath = storage_path(
                'app/public/' . $recipe->image
            );

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $recipe->delete();

        return redirect('/recipes')
            ->with('success', 'Recipe deleted successfully!');
    }

    public function show($id)
    {
        $recipe = Recipe::with('user')
        ->where('id', $id)
        ->where(function ($query) {

                $query->where(
                    'user_id',
                    auth()->id()
                )
                ->orWhere(
                    'is_public',
                    true
                );

            })
            ->firstOrFail();


        return view(
            'recipes.show',
            compact('recipe')
        );

    }

    public function discover(Request $request)
    {
        $query = Recipe::with('user')
        ->where('is_public', true);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'description',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'ingredients',
                    'like',
                    '%' . $search . '%'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->input(
            'sort',
            'newest'
        );


        switch ($sort) {

            case 'oldest':

                $query->oldest();

                break;

            case 'name_asc':

                $query->orderBy(
                    'name',
                    'asc'
                );

                break;

            case 'name_desc':

                $query->orderBy(
                    'name',
                    'desc'
                );

                break;

            case 'time_asc':

                $query->orderBy(
                    'cooking_time',
                    'asc'
                );

                break;

            case 'time_desc':

                $query->orderBy(
                    'cooking_time',
                    'desc'
                );

                break;

            default:

                $query->latest();

                $sort = 'newest';

                break;
        }


        $recipes = $query
            ->paginate(12)
            ->withQueryString();


        return view(
            'recipes.discover',
            compact(
                'recipes',
                'sort'
            )
        );
    }
}