@extends('layouts.app')

@section('title', 'Meal Planner')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

    <!-- Header -->

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Weekly Planning
            </p>

            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
                📅 Meal Planner
            </h1>

            <p class="text-gray-500 mt-2 max-w-xl">
                Plan and organize your breakfast, lunch, and dinner for the week.
            </p>

        </div>


        <a
            href="{{ url('/recipes') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-orange-500 transition"
        >
            🍽️ My Recipes →
        </a>

        </div>

        <!-- Success Message -->

        @if (session('success'))

        <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-4 mb-6">

            <span class="text-lg">
                ✓
            </span>

            <p class="font-medium">
                {{ session('success') }}
            </p>

        </div>

        @endif

        <!-- Planner Controls -->

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-6 mb-6">

        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">


            <!-- Selected Week -->

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Selected Week
                </p>

                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    {{ $startOfWeek->format('d M') }}
                    -
                    {{ $endOfWeek->format('d M Y') }}
                </h2>

            </div>


            <!-- Week Navigation -->

            <div class="flex items-center gap-2">

                <a
                    href="{{ url('/meal-planner?week=' . $startOfWeek->copy()->subWeek()->format('Y-m-d')) }}"
                    class="inline-flex items-center gap-1 border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
                >
                    ← Previous
                </a>


                <a
                    href="{{ url('/meal-planner') }}"
                    class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition"
                >
                    This Week
                </a>


                <a
                    href="{{ url('/meal-planner?week=' . $startOfWeek->copy()->addWeek()->format('Y-m-d')) }}"
                    class="inline-flex items-center gap-1 border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
                >
                    Next →
                </a>

            </div>


            <!-- Actions -->

            <div class="flex flex-wrap items-center gap-2">

                <form
                    action="{{ url('/shopping-list/generate') }}"
                    method="POST"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="week"
                        value="{{ $startOfWeek->format('Y-m-d') }}"
                    >

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 bg-green-500 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-green-600 transition"
                    >
                        🛒 Shopping List
                    </button>

                </form>


                <a
                    href="{{ route('meal-planner.ai.setup', [
                        'week' => $startOfWeek->format('Y-m-d')
                    ]) }}"
                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-gray-800 transition"
                >
                    🤖 Plan With AI
                </a>


                <form
                    action="{{ route('meal-planner.clear') }}"
                    method="POST"
                >

                    @csrf
                    @method('DELETE')

                    <input
                        type="hidden"
                        name="week"
                        value="{{ $startOfWeek->format('Y-m-d') }}"
                    >

                    <button
                        type="submit"
                        onclick="return confirm('Are you sure you want to clear all meals from {{ $startOfWeek->format('d M') }} to {{ $endOfWeek->format('d M Y') }}? This action cannot be undone.')"
                        class="inline-flex items-center gap-2 border border-red-200 text-red-500 px-4 py-2.5 rounded-xl font-semibold hover:bg-red-50 transition"
                    >
                        🗑 Clear Week
                    </button>

                </form>

            </div>

        </div>

        </div>

        <!-- Weekly Plan Card -->

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">

        <div class="px-6 py-5 border-b border-gray-100">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Weekly Schedule
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    📅 Your Meal Plan
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Choose a recipe for each meal slot.
                </p>

            </div>

        </div>

        <!-- Weekly Grid -->

        <div class="overflow-x-auto">

            <div class="min-w-[1100px]">

                <!-- Days -->

                <div class="grid grid-cols-7 border-b border-gray-200">

                    @foreach ($dates as $date)

                        <div class="p-4 text-center border-r border-gray-200 last:border-r-0">

                            <p class="text-xs uppercase tracking-wide text-orange-500 font-bold">
                                {{ $date->format('D') }}
                            </p>

                            <p class="text-2xl font-bold text-gray-800">
                                {{ $date->format('d') }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ $date->format('M') }}
                            </p>

                        </div>

                    @endforeach

                </div>


                <!-- Breakfast -->

                <div class="grid grid-cols-7 border-b border-gray-200">

                    @foreach ($dates as $date)

                        @php
                            $breakfast = $mealPlanMap->get(
                                $date->format('Y-m-d') . '_Breakfast'
                            );
                        @endphp

                        <div class="p-4 border-r border-gray-200 last:border-r-0 min-h-[150px]">

                            <p class="text-sm font-bold text-gray-600 mb-3">
                                🍳 Breakfast
                            </p>

                            @if ($breakfast)

                                <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">

                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $breakfast->recipe->name }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-3">

                                        <!-- Change -->

                                        <button
                                            type="button"
                                            onclick="openMealModal(
                                                '{{ $date->format('Y-m-d') }}',
                                                'Breakfast',
                                                {{ $breakfast->recipe->id }}
                                            )"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="Change Meal"
                                            aria-label="Change Recipe"
                                        >
                                            🔄
                                        </button>


                                        <!-- View Recipe -->

                                        <a
                                            href="{{ url('/recipes/' . $breakfast->recipe->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="View Recipe"
                                            aria-label="View Recipe"
                                        >
                                            👁
                                        </a>

                                        <!-- Remove -->

                                        <form
                                            action="{{ url('/meal-planner/' . $breakfast->id) }}"
                                            method="POST"
                                            class="inline-flex"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Remove this meal?')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                                title="Remove Meal"
                                                aria-label="Remove Meal"
                                            >
                                                🗑️
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            @else

                                <button
                                    type="button"
                                    onclick="openMealModal(
                                        '{{ $date->format('Y-m-d') }}',
                                        'Breakfast',
                                        null
                                    )"
                                    class="w-full border border-dashed border-gray-300 rounded-xl py-3 text-sm text-gray-400 hover:border-orange-400 hover:text-orange-500 transition"
                                >
                                    + Add Meal
                                </button>

                            @endif

                        </div>

                    @endforeach

                </div>


                <!-- Lunch -->

                <div class="grid grid-cols-7 border-b border-gray-200">

                    @foreach ($dates as $date)

                        @php
                            $lunch = $mealPlanMap->get(
                                $date->format('Y-m-d') . '_Lunch'
                            );
                        @endphp

                        <div class="p-4 border-r border-gray-200 last:border-r-0 min-h-[150px]">

                            <p class="text-sm font-bold text-gray-600 mb-3">
                                🍱 Lunch
                            </p>

                            @if ($lunch)

                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3">

                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $lunch->recipe->name }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-3">

                                        <!-- Change -->

                                        <button
                                            type="button"
                                            onclick="openMealModal(
                                                '{{ $date->format('Y-m-d') }}',
                                                'Lunch',
                                                {{ $lunch->recipe->id }}
                                            )"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="Change Meal"
                                            aria-label="Change Recipe"
                                        >
                                            🔄
                                        </button>

                                        <!-- View Recipe -->

                                        <a
                                            href="{{ url('/recipes/' . $lunch->recipe->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="View Recipe"
                                            aria-label="View Recipe"
                                        >
                                            👁
                                        </a>

                                        <!-- Remove -->

                                        <form
                                            action="{{ url('/meal-planner/' . $lunch->id) }}"
                                            method="POST"
                                            class="inline-flex"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Remove this meal?')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                                title="Remove Meal"
                                                aria-label="Remove Meal"
                                            >
                                                🗑️
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            @else

                                <button
                                    type="button"
                                    onclick="openMealModal(
                                        '{{ $date->format('Y-m-d') }}',
                                        'Lunch',
                                        null
                                    )"
                                    class="w-full border border-dashed border-gray-300 rounded-xl py-3 text-sm text-gray-400 hover:border-orange-400 hover:text-orange-500 transition"
                                >
                                    + Add Meal
                                </button>

                            @endif

                        </div>

                    @endforeach

                </div>


                <!-- Dinner -->

                <div class="grid grid-cols-7">

                    @foreach ($dates as $date)

                        @php
                            $dinner = $mealPlanMap->get(
                                $date->format('Y-m-d') . '_Dinner'
                            );
                        @endphp

                        <div class="p-4 border-r border-gray-200 last:border-r-0 min-h-[150px]">

                            <p class="text-sm font-bold text-gray-600 mb-3">
                                🍽️ Dinner
                            </p>

                            @if ($dinner)

                                <div class="bg-purple-50 border border-purple-100 rounded-xl p-3">

                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $dinner->recipe->name }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-3">

                                        <!-- Change -->

                                        <button
                                            type="button"
                                            onclick="openMealModal(
                                                '{{ $date->format('Y-m-d') }}',
                                                'Dinner',
                                                {{ $dinner->recipe->id }}
                                            )"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="Change Meal"
                                            aria-label="Change Recipe"
                                        >
                                            🔄
                                        </button>


                                        <!-- View Recipe -->

                                        <a
                                            href="{{ url('/recipes/' . $dinner->recipe->id) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-orange-500 hover:bg-orange-50 transition"
                                            title="View Recipe"
                                            aria-label="View Recipe"
                                        >
                                            👁
                                        </a>


                                        <!-- Remove -->

                                        <form
                                            action="{{ url('/meal-planner/' . $dinner->id) }}"
                                            method="POST"
                                            class="inline-flex"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Remove this meal?')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                                title="Remove Meal"
                                                aria-label="Remove Meal"
                                            >
                                                🗑️
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            @else

                                <button
                                    type="button"
                                    onclick="openMealModal(
                                        '{{ $date->format('Y-m-d') }}',
                                        'Dinner',
                                        null
                                    )"
                                    class="w-full border border-dashed border-gray-300 rounded-xl py-3 text-sm text-gray-400 hover:border-orange-400 hover:text-orange-500 transition"
                                >
                                    + Add Meal
                                </button>

                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

    <!-- Table Legend -->

        <div class="px-6 py-4 border-t border-gray-100 bg-stone-50">

            <div class="flex flex-wrap items-center justify-center gap-5 text-xs text-gray-400">

                <span class="inline-flex items-center gap-1.5">
                    🔄 Change recipe
                </span>

                <span class="inline-flex items-center gap-1.5">
                    👁 View recipe
                </span>

                <span class="inline-flex items-center gap-1.5">
                    🗑️ Remove meal
                </span>

                <span class="inline-flex items-center gap-1.5">
                    + Add meal
                </span>

            </div>

        </div>

    </div>

</div>

<!-- Meal Modal -->

<div
    id="mealModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4"
>

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] p-6 flex flex-col">

        <div class="flex items-center justify-between mb-5 shrink-0">

            <div>

                <h2
                    id="mealModalTitle"
                    class="text-2xl font-bold text-gray-800"
                >
                    Add Meal
                </h2>

                <p
                    id="mealModalInfo"
                    class="text-gray-500 mt-1"
                ></p>

            </div>

            <button
                type="button"
                onclick="closeMealModal()"
                class="text-gray-400 hover:text-gray-700 text-2xl"
            >
                &times;
            </button>

        </div>


        <form
    action="{{ url('/meal-planner') }}"
    method="POST"
    class="flex flex-col min-h-0 flex-1"
>
    @csrf

    <input
        type="hidden"
        name="date"
        id="selectedMealDate"
    >

    <input
        type="hidden"
        name="meal_type"
        id="selectedMealType"
    >

    <input
        type="hidden"
        name="recipe_id"
        id="selectedRecipeId"
        required
    >

    <input
        type="hidden"
        id="selectedRecipeName"
    >

    <!-- Recipe Search -->

    <div class="mb-4">

        <label
            id="recipeSearchLabel"
            class="block text-sm font-semibold text-gray-700 mb-2"
        >
            Recipes
        </label>

        <input
            type="text"
            id="recipeSearch"
            placeholder="🔎 Search recipe name or ingredients..."
            oninput="searchRecipes()"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
        >

    </div>


    <!-- Filters -->

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">

        <select
            id="recipeCategory"
            onchange="searchRecipes()"
            class="border border-gray-300 rounded-lg px-3 py-2"
        >

            <option value="">
                All Categories
            </option>

            <option value="Breakfast">Breakfast</option>
            <option value="Lunch">Lunch</option>
            <option value="Dinner">Dinner</option>
            <option value="Dessert">Dessert</option>
            <option value="Snack">Snack</option>
            <option value="Drinks">Drinks</option>

        </select>


        <select
            id="recipeDifficulty"
            onchange="searchRecipes()"
            class="border border-gray-300 rounded-lg px-3 py-2"
        >

            <option value="">
                Any Difficulty
            </option>

            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>

        </select>


        <select
            id="recipeTime"
            onchange="searchRecipes()"
            class="border border-gray-300 rounded-lg px-3 py-2"
        >

            <option value="">
                Any Time
            </option>

            <option value="15">Under 15 min</option>
            <option value="30">Under 30 min</option>
            <option value="60">Under 60 min</option>

        </select>

    </div>


    <!-- Favorites -->

    <label class="flex items-center gap-2 mb-4 cursor-pointer">

        <input
            type="checkbox"
            id="recipeFavorites"
            onchange="searchRecipes()"
            class="w-4 h-4 text-orange-500 rounded"
        >

        <span class="text-sm text-gray-600">
            ❤️ Favorites only
        </span>

    </label>


    <!-- Recipe Results Area -->

    <div class="flex-1 min-h-0 overflow-y-auto pr-1">

        <p
            id="recipeResultsTitle"
            class="text-sm font-semibold text-gray-600 mb-2"
        >
            Recent Recipes
        </p>

        <p class="text-xs text-gray-400 mb-3">
            Recommended recipes are shown first. Use the search box to find any recipe.
        </p>


        <!-- Loading -->

        <div
            id="recipeLoading"
            class="hidden min-h-[220px] items-center justify-center text-center text-sm text-gray-400"
        >
            Searching recipes...
        </div>


        <!-- Empty -->

        <div
            id="recipeEmpty"
            class="hidden min-h-[220px] items-center justify-center text-center text-sm text-gray-400"
        >
            No recipes found.
        </div>


        <!-- Results -->

        <div
            id="recipeResults"
            class="space-y-2"
        >
        </div>

    </div>


    <!-- Selected Recipe -->

    <div
        id="selectedRecipePreview"
        class="hidden bg-orange-50 border border-orange-200 rounded-xl p-4 mt-4"
    >

        <p class="text-xs text-orange-600 font-semibold uppercase">
            Selected Recipe
        </p>

        <p
            id="selectedRecipeDisplay"
            class="font-bold text-gray-800 mt-1"
        ></p>

    </div>


    <!-- Buttons -->

    <div class="flex gap-3 mt-5">

        <button
            type="button"
            onclick="closeMealModal()"
            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
        >
            Cancel
        </button>

        <button
            type="submit"
            class="flex-1 bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
        >
            Save Meal
        </button>

    </div>

</form>
        </div>
        </div>

    </div>

</div>

<script>

    let selectedMealDate = null;
    let selectedMealType = null;
    let currentRecipeId = null;

    /*
    |--------------------------------------------------------------------------
    | Open Meal Modal
    |--------------------------------------------------------------------------
    */

    function openMealModal(date, mealType, recipeId)
    {
        selectedMealDate = date;
        selectedMealType = mealType;
        currentRecipeId = recipeId;

        document.getElementById('selectedMealDate').value = date;

        document.getElementById('selectedMealType').value = mealType;

        document.getElementById(
            'recipeSearchLabel'
        ).innerText =
            recipeId
                ? `Change ${mealType} Recipe`
                : `Add ${mealType} Recipe`;

        const title =
            document.getElementById('mealModalTitle');

        const info =
            document.getElementById('mealModalInfo');


        if (currentRecipeId) {
            title.innerText = 'Change Meal';
        } else {
            title.innerText = 'Add Meal';
        }


        const formattedDate = new Date(
            date + 'T00:00:00'
        ).toLocaleDateString(
            'en-GB',
            {
                weekday: 'long',
                day: 'numeric',
                month: 'short'
            }
        );


        info.innerText =
            formattedDate + ' · ' + mealType;


        // Reset filters

        document.getElementById('recipeSearch').value = '';

        document.getElementById('recipeCategory').value = '';

        document.getElementById('recipeDifficulty').value = '';

        document.getElementById('recipeTime').value = '';

        document.getElementById('recipeFavorites').checked = false;


        // Open modal

        const modal =
            document.getElementById('mealModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.getElementById(
            'recipeResultsTitle'
        ).innerText =
            recipeId
                ? `Recommended ${mealType} Recipes`
                : `Recent ${mealType} Recipes`;

        // Load recipes

        searchRecipes();
    }


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    function closeMealModal()
    {
        const modal =
            document.getElementById('mealModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    /*
    |--------------------------------------------------------------------------
    | Search Recipes
    |--------------------------------------------------------------------------
    */

    async function searchRecipes()
    {
        const results =
            document.getElementById('recipeResults');

        const loading =
            document.getElementById('recipeLoading');

        const empty =
            document.getElementById('recipeEmpty');


        const search =
            document.getElementById('recipeSearch').value;

        const category =
            document.getElementById('recipeCategory').value;

        const difficulty =
            document.getElementById('recipeDifficulty').value;

        const maxTime =
            document.getElementById('recipeTime').value;

        const favorites =
            document.getElementById('recipeFavorites').checked;


        results.innerHTML = '';

        empty.classList.add('hidden');
        empty.classList.remove('flex');

        loading.classList.remove('hidden');
        loading.classList.add('flex');


        const params =
            new URLSearchParams();


        if (search) {
            params.append('search', search);
        }

        if (category) {
            params.append('category', category);
        }

        if (difficulty) {
            params.append('difficulty', difficulty);
        }

        if (maxTime) {
            params.append('max_time', maxTime);
        }

        if (favorites) {
            params.append('favorites', '1');
        }

        if (selectedMealType) {
            params.append(
                'meal_type',
                selectedMealType
            );
        }

        if (currentRecipeId) {
            params.append(
                'current_recipe_id',
                currentRecipeId
            );
        }


        try {

            const response = await fetch(
    `{{ route('meal-planner.recipes.search') }}?${params.toString()}`,
    {
        headers: {
            'Accept': 'application/json'
        }
    }
);

console.log(
    'Recipe search status:',
    response.status
);

console.log(
    'Recipe search URL:',
    response.url
);


            if (!response.ok) {
                throw new Error('Unable to search recipes.');
            }


            const data =
                await response.json();


            loading.classList.add('hidden');
            loading.classList.remove('flex');


            if (!data.data || data.data.length === 0) {

                empty.classList.remove('hidden');
                empty.classList.add('flex');

                return;
            }


            data.data.forEach(recipe => {

                const button =
                    document.createElement('button');

                button.type = 'button';

                button.className =
                    'w-full text-left border border-gray-200 rounded-xl p-4 hover:bg-orange-50 hover:border-orange-300 transition';


                button.innerHTML = `

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="font-semibold text-gray-800 truncate">
                                ${escapeHtml(recipe.name)}
                            </p>

                            <p class="text-sm text-gray-400 mt-1">
                                ${escapeHtml(recipe.category)}
                                ·
                                ${recipe.cooking_time} min
                                ·
                                ${escapeHtml(recipe.difficulty)}
                            </p>

                        </div>

                        <span class="text-orange-500 font-semibold text-sm whitespace-nowrap">
                            Select
                        </span>

                    </div>

                `;


                button.addEventListener(
                    'click',
                    function () {

                        selectRecipe(
                            recipe.id,
                            recipe.name
                        );

                    }
                );


                results.appendChild(button);

            });


        } catch (error) {

    loading.classList.add('hidden');
    loading.classList.remove('flex');

    empty.classList.add('hidden');
    empty.classList.remove('flex');

    console.error('Recipe search error:', error);

    results.innerHTML = `

        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">

            <p class="font-semibold text-red-600">
                Unable to load recipes.
            </p>

            <p class="text-sm text-red-500 mt-1">
                Please check the browser console for the error.
            </p>

        </div>

    `;
}
    }


    /*
    |--------------------------------------------------------------------------
    | Select Recipe
    |--------------------------------------------------------------------------
    */

    function selectRecipe(id, name)
    {
        document.getElementById('selectedRecipeId').value = id;

        document.getElementById('selectedRecipeName').value = name;

        document.getElementById('selectedRecipeDisplay').innerText =
            name;


        document.getElementById('selectedRecipePreview')
            .classList.remove('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value)
    {
        const div =
            document.createElement('div');

        div.textContent = value;

        return div.innerHTML;
    }

</script>

@endsection
