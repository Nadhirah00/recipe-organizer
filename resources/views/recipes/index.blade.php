@extends('layouts.app')

@section('title', 'My Recipes')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- Header -->

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-8">

        <div>

            <p class="text-sm font-semibold uppercase tracking-[0.15em] text-orange-500">
                Your Collection
            </p>

            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-gray-800 mt-1">
                My Recipes
            </h1>

            <p class="text-gray-500 mt-2 max-w-xl">
                Keep your recipes organized in one place and easily find what you want to cook.
            </p>

        </div>


        <a
            href="{{ url('/recipes/create') }}"
            class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-3 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >
            <span class="text-lg">+</span>
            <span>Add Recipe</span>
        </a>

    </div>


    <!-- Search & Filter -->

        <form
            action="{{ url('/recipes') }}"
            method="GET"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 md:p-6 mb-8"
        >

        <!-- Search -->

        <div>

            <label
                for="search"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                🔎 Search your recipes
            </label>

            <div class="flex flex-col sm:flex-row gap-3">

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, description, or ingredients..."
                    class="flex-1 border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
                >

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                >
                    🔎 Search
                </button>

            </div>

        </div>


        <!-- Filters -->

        <div class="border-t border-gray-100 mt-5 pt-5">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

                <div>

                    <p class="text-sm font-semibold text-gray-700">
                        Filter & Sort
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Narrow down your recipe collection.
                    </p>

                </div>


                @if (
                    request()->filled('search') ||
                    request()->filled('category') ||
                    request()->filled('difficulty') ||
                    request()->filled('max_time') ||
                    request()->filled('sort')
                )

                    <a
                        href="{{ url('/recipes') }}"
                        class="text-sm font-semibold text-gray-400 hover:text-orange-500 transition"
                    >
                        Clear all
                    </a>

                @endif

            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                <!-- Category -->

                <div>

                    <label
                        for="category"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                    >
                        🏷 Category
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                        <option value="">
                            All Categories
                        </option>

                        <option
                            value="Breakfast"
                            {{ request('category') === 'Breakfast' ? 'selected' : '' }}
                        >
                            Breakfast
                        </option>

                        <option
                            value="Lunch"
                            {{ request('category') === 'Lunch' ? 'selected' : '' }}
                        >
                            Lunch
                        </option>

                        <option
                            value="Dinner"
                            {{ request('category') === 'Dinner' ? 'selected' : '' }}
                        >
                            Dinner
                        </option>

                        <option
                            value="Dessert"
                            {{ request('category') === 'Dessert' ? 'selected' : '' }}
                        >
                            Dessert
                        </option>

                        <option
                            value="Snack"
                            {{ request('category') === 'Snack' ? 'selected' : '' }}
                        >
                            Snack
                        </option>

                        <option
                            value="Drinks"
                            {{ request('category') === 'Drinks' ? 'selected' : '' }}
                        >
                            Drinks
                        </option>

                    </select>

                </div>


                <!-- Difficulty -->

                <div>

                    <label
                        for="difficulty"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                    >
                        ⭐ Difficulty
                    </label>

                    <select
                        id="difficulty"
                        name="difficulty"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                        <option value="">
                            All Difficulties
                        </option>

                        <option
                            value="Easy"
                            {{ request('difficulty') === 'Easy' ? 'selected' : '' }}
                        >
                            Easy
                        </option>

                        <option
                            value="Medium"
                            {{ request('difficulty') === 'Medium' ? 'selected' : '' }}
                        >
                            Medium
                        </option>

                        <option
                            value="Hard"
                            {{ request('difficulty') === 'Hard' ? 'selected' : '' }}
                        >
                            Hard
                        </option>

                    </select>

                </div>


                <!-- Cooking Time -->

                <div>

                    <label
                        for="max_time"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                    >
                        ⏱ Cooking Time
                    </label>

                    <select
                        id="max_time"
                        name="max_time"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                        <option value="">
                            Any Time
                        </option>

                        <option
                            value="15"
                            {{ request('max_time') == '15' ? 'selected' : '' }}
                        >
                            Under 15 min
                        </option>

                        <option
                            value="30"
                            {{ request('max_time') == '30' ? 'selected' : '' }}
                        >
                            Under 30 min
                        </option>

                        <option
                            value="60"
                            {{ request('max_time') == '60' ? 'selected' : '' }}
                        >
                            Under 60 min
                        </option>

                    </select>

                </div>


                <!-- Sort -->

                <div>

                    <label
                        for="sort"
                        class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                    >
                        ↕ Sort By
                    </label>

                    <select
                        id="sort"
                        name="sort"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                        <option
                            value="newest"
                            {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}
                        >
                            Newest
                        </option>

                        <option
                            value="oldest"
                            {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}
                        >
                            Oldest
                        </option>

                        <option
                            value="name_asc"
                            {{ ($sort ?? '') === 'name_asc' ? 'selected' : '' }}
                        >
                            Name A–Z
                        </option>

                        <option
                            value="name_desc"
                            {{ ($sort ?? '') === 'name_desc' ? 'selected' : '' }}
                        >
                            Name Z–A
                        </option>

                        <option
                            value="time_asc"
                            {{ ($sort ?? '') === 'time_asc' ? 'selected' : '' }}
                        >
                            Shortest Cooking Time
                        </option>

                        <option
                            value="time_desc"
                            {{ ($sort ?? '') === 'time_desc' ? 'selected' : '' }}
                        >
                            Longest Cooking Time
                        </option>

                    </select>

                </div>

            </div>

        </div>

    </form>

    <!-- Results -->

    @if ($recipes->count())

        <!-- Result Header -->

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">

            <div>

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Your Collection
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    {{ $recipes->total() }}
                    {{ $recipes->total() === 1 ? 'Recipe' : 'Recipes' }}
                </h2>

            </div>


            @if (request()->filled('search') || request()->filled('category') || request()->filled('difficulty') || request()->filled('max_time'))

                <p class="text-sm text-gray-400">
                    Filtered results
                </p>

            @else

                <p class="text-sm text-gray-400">
                    Your latest recipes
                </p>

            @endif

        </div>


        <!-- Recipe Grid -->

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($recipes as $recipe)

                <article
                    class="group relative bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition duration-300"
                >

                    <!-- Whole-card link, kept beneath the action icons -->

                    <a
                        href="{{ url('/recipes/' . $recipe->id) }}"
                        class="absolute inset-0 z-0"
                        aria-label="View {{ $recipe->name }}"
                    ></a>

                    <!-- Image -->

                    <div class="relative h-52 bg-orange-50 overflow-hidden">

                        @if ($recipe->image)

                            <img
                                src="{{ asset('storage/' . $recipe->image) }}"
                                alt="{{ $recipe->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >

                        @else

                            <div class="w-full h-full flex items-center justify-center">

                                <span class="text-6xl">
                                    🍳
                                </span>

                            </div>

                        @endif


                        <!-- Visibility Badge -->

                        @if ($recipe->is_public)

                            <span class="absolute top-4 left-4 inline-flex items-center gap-1 bg-white/95 backdrop-blur text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                🌎 Public
                            </span>

                        @else

                            <span class="absolute top-4 left-4 inline-flex items-center gap-1 bg-white/95 backdrop-blur text-gray-600 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                🔒 Private
                            </span>

                        @endif


                        <!-- Card Actions -->

                        <div class="absolute top-4 right-4 z-10 flex items-center gap-1.5">

                            <button
                                type="button"
                                onclick="openRecipeCardMealModal({{ $recipe->id }}, @js($recipe->name), @js($recipe->category))"
                                title="Add to meal plan"
                                aria-label="Add {{ $recipe->name }} to meal plan"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                            >
                                📅
                            </button>

                            <a
                                href="{{ url('/recipes/' . $recipe->id . '/edit') }}"
                                title="Edit recipe"
                                aria-label="Edit {{ $recipe->name }}"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                            >
                                ✏️
                            </a>

                            <form
                                action="{{ url('/recipes/' . $recipe->id) }}"
                                method="POST"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    onclick="return confirm('Are you sure you want to delete this recipe?')"
                                    title="Delete recipe"
                                    aria-label="Delete {{ $recipe->name }}"
                                    class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                                >
                                    🗑
                                </button>

                            </form>

                        </div>

                    </div>

                    <!-- Content -->

                    <div class="p-6">

                        <!-- Name -->

                        <h3 class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition">
                            {{ $recipe->name }}
                        </h3>


                        <!-- Owner -->

                        <p class="text-sm text-gray-500 mt-2">
                            👨‍🍳 By
                            <span class="font-semibold text-orange-600">
                                You
                            </span>
                        </p>

                        <!-- Description -->

                        @if ($recipe->description)

                            <p class="text-sm text-gray-500 mt-3 line-clamp-2">
                                {{ $recipe->description }}
                            </p>

                        @endif

                        <!-- Details -->

                        <div class="flex flex-wrap gap-2 mt-4">

                            <span class="bg-orange-50 text-orange-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                🏷 {{ $recipe->category }}
                            </span>

                            <span class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                ⏱ {{ $recipe->cooking_time }} min
                            </span>

                            <span class="bg-purple-50 text-purple-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                ⭐ {{ $recipe->difficulty }}
                            </span>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

        <!-- Pagination -->

        @if ($recipes->hasPages())

            <div class="mt-10 flex flex-col items-center gap-4">

                <div class="text-sm text-gray-500">

                    Showing

                    <span class="font-semibold text-gray-700">
                        {{ $recipes->firstItem() }}
                    </span>

                    -

                    <span class="font-semibold text-gray-700">
                        {{ $recipes->lastItem() }}
                    </span>

                    of

                    <span class="font-semibold text-gray-700">
                        {{ $recipes->total() }}
                    </span>

                    recipes

                </div>


                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-4 py-3">

                    {{ $recipes->links() }}

                </div>

            </div>

        @endif

        @else

        <!-- Empty State -->

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

            <div class="mx-auto w-20 h-20 rounded-3xl bg-orange-50 flex items-center justify-center text-5xl">
                🍳
            </div>


            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
                Nothing here yet
            </p>


            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                No recipes found
            </h2>


            @if (
                request()->filled('search') ||
                request()->filled('category') ||
                request()->filled('difficulty') ||
                request()->filled('max_time')
            )

                <p class="text-gray-500 mt-3 max-w-md mx-auto">
                    We couldn't find any recipes matching your current filters.
                    Try adjusting your search or clearing the filters.
                </p>


                <div class="flex flex-col sm:flex-row justify-center gap-3 mt-6">

                    <a
                        href="{{ url('/recipes') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-800 text-white px-5 py-3 rounded-xl font-semibold hover:bg-gray-700 transition"
                    >
                        Clear Filters
                    </a>


                    <a
                        href="{{ url('/recipes/create') }}"
                        class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                    >
                        + Add Recipe
                    </a>

                </div>

            @else

                <p class="text-gray-500 mt-3 max-w-md mx-auto">
                    Your recipe collection is empty. Start by adding your first recipe.
                </p>


                <a
                    href="{{ url('/recipes/create') }}"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition mt-6"
                >
                    + Add Your First Recipe
                </a>

            @endif

        </div>

        @endif

</div>


<!-- ================================================= -->
<!-- Add to Meal Plan Modal -->
<!-- ================================================= -->

<div
    id="recipeCardMealModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6">

        <div class="flex items-start justify-between gap-4 mb-6">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Meal Planner
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    Add to Meal Plan
                </h2>

                <p class="text-sm text-gray-500 mt-1" id="recipeCardMealName"></p>

            </div>


            <button
                type="button"
                onclick="closeRecipeCardMealModal()"
                class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl"
                aria-label="Close"
            >
                &times;
            </button>

        </div>


        <form
            id="recipeCardMealForm"
            data-meal-slot-check="{{ route('meal-planner.slots.check') }}"
            data-meal-plan-submit
            action="{{ url('/meal-planner') }}"
            method="POST"
        >

            @csrf

            <input
                type="hidden"
                name="recipe_id"
                id="recipeCardMealRecipeId"
            >

            <input
                type="hidden"
                name="replace"
                value="0"
            >

            <div class="mb-5">

                <label
                    for="recipeCardMealDate"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    id="recipeCardMealDate"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
                >

            </div>


            <div class="mb-6">

                <label
                    for="recipeCardMealType"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Meal Type
                </label>

                <select
                    name="meal_type"
                    id="recipeCardMealType"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
                >

                    <option value="Breakfast">🍳 Breakfast</option>
                    <option value="Lunch">🍱 Lunch</option>
                <option value="Dinner">🍽️ Dinner</option>

            </select>

        </div>


        <!-- Existing Meal Warning -->

        <div
            data-meal-slot-warning
            role="status"
            class="hidden mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3"
        >

            <p
                data-meal-slot-title
                class="text-sm font-semibold text-amber-800"
            >
            </p>

            <p
                data-meal-slot-message
                class="text-sm text-amber-700 mt-1"
            >
            </p>

        </div>


        <div class="grid grid-cols-2 gap-3">

            <button
                type="button"
                onclick="closeRecipeCardMealModal()"
                class="bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
            >
                Cancel
            </button>


            <button
                type="submit"
                data-meal-slot-submit
                data-default-label="Add to Plan"
                class="bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                Add to Plan
            </button>

        </div>


        </form>

    </div>

</div>

<script>

    /**
     * Open the add-to-plan modal for a card, preselecting the meal type that
     * best matches the recipe category.
     */
    function openRecipeCardMealModal(recipeId, recipeName, category)
    {
        const modal = document.getElementById('recipeCardMealModal');
        const recipeIdInput = document.getElementById('recipeCardMealRecipeId');
        const nameLabel = document.getElementById('recipeCardMealName');
        const mealType = document.getElementById('recipeCardMealType');

        recipeIdInput.value = recipeId;
        nameLabel.textContent = recipeName;

        const validMealTypes = ['Breakfast', 'Lunch', 'Dinner'];

        mealType.value = validMealTypes.includes(category) ? category : 'Lunch';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        refreshMealSlot('recipeCardMealForm');
    }


    function closeRecipeCardMealModal()
    {
        const modal = document.getElementById('recipeCardMealModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection