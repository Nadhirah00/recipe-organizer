@extends('layouts.app')

@section('title', 'Recipe Matches')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

    <!-- Header -->

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">

        <div>

            <a
                href="{{ route('ingredients.search') }}"
                class="text-orange-500 font-semibold hover:text-orange-600"
            >
                ← Search Again
            </a>

            <h1 class="text-4xl font-bold text-gray-800 mt-4">
                🍳 Recipe Matches
            </h1>

            <p class="text-gray-500 mt-2">
                Recipes ranked by how many ingredients you already have.
            </p>

        </div>

    </div>


    <!-- Your Ingredients -->

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

        <h2 class="text-lg font-bold text-gray-800 mb-4">
            🛒 Your Ingredients
        </h2>

        <div class="flex flex-wrap gap-2">

            @foreach ($userIngredients as $ingredient)

                <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-full text-sm font-medium">
                    ✓ {{ ucwords($ingredient) }}
                </span>

            @endforeach

        </div>

    </div>

    <!-- Match Filters -->

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h2 class="font-bold text-gray-800">
                    Match Level
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Filter recipes based on how many ingredients you already have.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <button
                    type="button"
                    onclick="filterRecipes('all', this)"
                    class="match-filter active-filter px-4 py-2 rounded-lg text-sm font-semibold border border-orange-500 bg-orange-500 text-white"
                >
                    All
                </button>

                <button
                    type="button"
                    onclick="filterRecipes(70, this)"
                    class="match-filter px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50"
                >
                    70%+
                </button>

                <button
                    type="button"
                    onclick="filterRecipes(50, this)"
                    class="match-filter px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50"
                >
                    50%+
                </button>

                <button
                    type="button"
                    onclick="filterRecipes(30, this)"
                    class="match-filter px-4 py-2 rounded-lg text-sm font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50"
                >
                    30%+
                </button>

            </div>

        </div>

    </div>


    @if ($results->count())

        <!-- Results -->

        <div class="space-y-5">

            @foreach ($results as $index => $result)

                <div
                    class="recipe-match"
                    data-match="{{ $result['percentage'] }}"
                >

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    <div class="p-6">

                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">

                            <div class="flex-1">

                                <!-- Rank -->

                                <div class="flex items-center gap-3">

                                    <span class="text-2xl font-bold text-orange-500">
                                        #{{ $index + 1 }}
                                    </span>

                                    <div>

                                        <h2 class="text-2xl font-bold text-gray-800">
                                            {{ $result['recipe']->name }}
                                        </h2>

                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $result['recipe']->category }}
                                            ·
                                            {{ $result['recipe']->cooking_time }} min
                                            ·
                                            {{ $result['recipe']->difficulty }}
                                        </p>

                                    </div>

                                </div>


                                <!-- Match Percentage -->

                                <div class="mt-5">

                                    <div class="flex items-center justify-between mb-2">

                                        <span class="text-sm font-semibold text-gray-600">
                                            Ingredient Match
                                        </span>

                                        <span class="font-bold text-orange-500">
                                            {{ $result['percentage'] }}%
                                        </span>

                                    </div>

                                    <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">

                                        <div
                                            class="h-full bg-orange-500 rounded-full"
                                            style="width: {{ $result['percentage'] }}%"
                                        ></div>

                                    </div>

                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ $result['matched_count'] }}
                                        of
                                        {{ $result['total_count'] }}
                                        ingredients available
                                    </p>

                                </div>

                            </div>


                            <!-- View Recipe -->

                            <div>

                                <div class="flex flex-col sm:flex-row gap-2">

                                    <a
                                        href="{{ url('/recipes/' . $result['recipe']->id) }}"
                                        class="inline-flex items-center justify-center bg-orange-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                                    >
                                        View Recipe
                                    </a>

                                    <button
                                        type="button"
                                        onclick="openExploreMealModal(
                                            {{ $recipe->id }},
                                            @js($recipe->name),
                                            @js($recipe->category)
                                        )"
                                        class="text-center border border-orange-200 text-orange-500 px-5 py-2.5 rounded-lg font-semibold hover:bg-orange-50 transition"
                                    >
                                        📅 Add to Meal Plan
                                    </button>

                                </div>

                            </div>

                        </div>


                        <!-- Matched / Missing -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6 pt-5 border-t border-gray-100">

                            <!-- Have -->

                            <div>

                                <h3 class="font-semibold text-green-700 mb-3">
                                    ✓ You Have
                                </h3>

                                <div class="flex flex-wrap gap-2">

                                    @foreach ($result['matched'] as $ingredient)

                                        <span class="bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-sm">
                                            {{ ucwords($ingredient) }}
                                        </span>

                                    @endforeach

                                </div>

                            </div>


                            <!-- Missing -->

                            <div>

                                <h3 class="font-semibold text-red-600 mb-3">
                                    ✕ Still Needed
                                </h3>

                                @if (count($result['missing']))

                                    <div class="flex flex-wrap gap-2">

                                        @foreach ($result['missing'] as $ingredient)

                                            <span class="bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-sm">
                                                {{ ucwords($ingredient) }}
                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-green-600 font-semibold text-sm">
                                        🎉 You have everything needed!
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <!-- No Results -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">

            <div class="text-6xl mb-5">
                🔎
            </div>

            <h2 class="text-2xl font-bold text-gray-800">
                No matching recipes found
            </h2>

            <p class="text-gray-500 mt-2 max-w-lg mx-auto">
                Try adding more ingredients or search your recipe collection
                with different ingredients.
            </p>

            <a
                href="{{ route('ingredients.search') }}"
                class="inline-block mt-6 bg-orange-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                ← Try Different Ingredients
            </a>

        </div>

    @endif

</div>

<!-- Add to Meal Plan Modal -->

<div
    id="ingredientMealModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4"
>

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

        <div class="flex items-center justify-between mb-5">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Add to Meal Plan
                </h2>

                <p
                    id="ingredientMealRecipeName"
                    class="text-gray-500 mt-1"
                ></p>

            </div>

            <button
                type="button"
                onclick="closeMealPlannerModal()"
                class="text-gray-400 hover:text-gray-700 text-2xl"
            >
                &times;
            </button>

        </div>


        <form
            action="{{ url('/meal-planner') }}"
            method="POST"
        >

            @csrf

            <input
                type="hidden"
                name="recipe_id"
                id="ingredientRecipeId"
            >


            <div class="mb-5">

                <label
                    for="ingredientMealDate"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    id="ingredientMealDate"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    required
                >

            </div>


            <div class="mb-6">

                <label
                    for="ingredientMealType"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Meal Type
                </label>

                <p class="text-xs text-gray-400 mt-2">
                    The meal type is based on the recipe category, but you can change it.
                </p>

                <select
                    name="meal_type"
                    id="ingredientMealType"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    required
                >

                    <option value="Breakfast">
                        🍳 Breakfast
                    </option>

                    <option value="Lunch">
                        🍱 Lunch
                    </option>

                    <option value="Dinner">
                        🍽️ Dinner
                    </option>

                </select>

            </div>


            <div class="flex gap-3">

                <button
                    type="button"
                    onclick="closeMealPlannerModal()"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="flex-1 bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
                >
                    Add to Plan
                </button>

            </div>

        </form>

    </div>

</div>

<script>

    function openMealPlannerModal(
        recipeId,
        recipeName,
        category
    )
    {
        document.getElementById(
            'ingredientRecipeId'
        ).value = recipeId;


        document.getElementById(
            'ingredientMealRecipeName'
        ).innerText = recipeName;


        const mealType =
            document.getElementById(
                'ingredientMealType'
            );


        /*
        |--------------------------------------------------------------------------
        | Match recipe category to meal type
        |--------------------------------------------------------------------------
        */

        const validMealTypes = [
            'Breakfast',
            'Lunch',
            'Dinner'
        ];


        if (
            category &&
            validMealTypes.includes(category)
        ) {

            mealType.value = category;

        } else {

            mealType.value = 'Lunch';

        }


        const modal =
            document.getElementById(
                'ingredientMealModal'
            );


        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeMealPlannerModal()
    {
        const modal =
            document.getElementById(
                'ingredientMealModal'
            );

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    function filterRecipes(minimum, button)
    {
        const cards =
            document.querySelectorAll('.recipe-match');

        const buttons =
            document.querySelectorAll('.match-filter');


        buttons.forEach(item => {

            item.classList.remove(
                'bg-orange-500',
                'text-white',
                'border-orange-500'
            );

            item.classList.add(
                'text-gray-600',
                'border-gray-300'
            );

        });


        button.classList.remove(
            'text-gray-600',
            'border-gray-300'
        );

        button.classList.add(
            'bg-orange-500',
            'text-white',
            'border-orange-500'
        );


        cards.forEach(card => {

            const percentage =
                parseInt(
                    card.dataset.match
                );


            if (
                minimum === 'all' ||
                percentage >= minimum
            ) {

                card.classList.remove('hidden');

            } else {

                card.classList.add('hidden');

            }

        });

    }

</script>

@endsection