@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-8">

    <div>

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
            Your Saved Recipes
        </p>

        <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
            ❤️ My Favorites
        </h1>

        <p class="text-gray-500 mt-2 max-w-xl">
            Keep the recipes you love close by and come back to them anytime.
        </p>

    </div>


    <a
        href="{{ url('/recipes') }}"
        class="inline-flex items-center justify-center gap-2 border border-gray-200 bg-white text-gray-700 px-5 py-3 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
    >
        🍽️ Browse My Recipes
    </a>

</div>


<!-- ========================================================= -->
<!-- Sort -->
<!-- ========================================================= -->

<form
    action="{{ url('/favorites') }}"
    method="GET"
    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8"
>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <p class="text-sm font-semibold text-gray-700">
                Organize Favorites
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Choose how your saved recipes are displayed.
            </p>

        </div>


        <div class="flex items-center gap-3">

            <label
                for="sort"
                class="text-sm font-medium text-gray-500 hidden sm:block"
            >
                Sort by
            </label>

            <select
                id="sort"
                name="sort"
                onchange="this.form.submit()"
                class="border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
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

</form>


@if ($favorites->isNotEmpty())

    <!-- ===================================================== -->
    <!-- Result Header -->
    <!-- ===================================================== -->

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Saved Collection
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                {{ $favorites->total() }}
                {{ $favorites->total() === 1 ? 'Favorite' : 'Favorites' }}
            </h2>

        </div>


        <p class="text-sm text-gray-400">
            Recipes you've saved for later
        </p>

    </div>


    <!-- ===================================================== -->
    <!-- Favorite Grid -->
    <!-- ===================================================== -->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        @foreach ($favorites as $favorite)

            @php
                $recipe = $favorite->recipe;
            @endphp


            @if ($recipe)

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

                                <span class="text-7xl">
                                    🍳
                                </span>

                            </div>

                        @endif


                        <!-- Card Actions -->

                        <div class="absolute top-4 right-4 z-10 flex items-center gap-1.5">

                            <button
                                type="button"
                                onclick="openFavouritesCardMealModal({{ $recipe->id }}, @js($recipe->name), @js($recipe->category))"
                                title="Add to meal plan"
                                aria-label="Add {{ $recipe->name }} to meal plan"
                                class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                            >
                                📅
                            </button>


                            <!-- Favorite Badge -->

                            <span class="w-9 h-9 rounded-full bg-white/95 backdrop-blur flex items-center justify-center shadow-sm text-sm">
                                ❤️
                            </span>

                        </div>

                    </div>


                    <!-- Content -->

                    <div class="p-6">

                        <!-- Recipe Name -->

                        <h3 class="text-xl font-bold leading-snug text-gray-800 group-hover:text-orange-600 transition">
                            {{ $recipe->name }}
                        </h3>


                        <!-- Owner -->

                        <p class="text-sm text-gray-500 mt-2">

                            👨‍🍳 By

                            @if ($recipe->user_id === auth()->id())

                                <span class="font-semibold text-orange-600">
                                    You
                                </span>

                            @else

                                <span class="font-semibold text-gray-700">
                                    {{ $recipe->user->name }}
                                </span>

                            @endif

                        </p>


                        <!-- Description -->

                        <div class="mt-3 min-h-[48px]">

                            @if ($recipe->description)

                                <p class="text-sm text-gray-500 leading-6 line-clamp-2">
                                    {{ $recipe->description }}
                                </p>

                            @else

                                <p class="text-sm text-gray-400 italic">
                                    No description provided.
                                </p>

                            @endif

                        </div>


                        <!-- Recipe Information -->

                        <div class="flex flex-wrap gap-2 mt-5">

                            <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                🏷 {{ $recipe->category }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                ⏱ {{ $recipe->cooking_time }} min
                            </span>

                            <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                                ⭐ {{ $recipe->difficulty }}
                            </span>

                        </div>


                        <!-- Servings -->

                        <p class="text-sm text-gray-500 mt-4">
                            👥
                            {{ $recipe->servings }}
                            {{ $recipe->servings == 1 ? 'serving' : 'servings' }}
                        </p>

                    </div>

                </article>

            @endif

        @endforeach

    </div>


    <!-- ===================================================== -->
    <!-- Pagination -->
    <!-- ===================================================== -->

    @if ($favorites->hasPages())

        <div class="mt-10 flex flex-col items-center gap-4">

            <p class="text-sm text-gray-500">

                Showing

                <span class="font-semibold text-gray-700">
                    {{ $favorites->firstItem() }}
                </span>

                -

                <span class="font-semibold text-gray-700">
                    {{ $favorites->lastItem() }}
                </span>

                of

                <span class="font-semibold text-gray-700">
                    {{ $favorites->total() }}
                </span>

                favorites

            </p>


            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-4 py-3">

                {{ $favorites->links() }}

            </div>

        </div>

    @endif


@else

    <!-- ===================================================== -->
    <!-- Empty State -->
    <!-- ===================================================== -->

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

        <div class="mx-auto w-20 h-20 rounded-3xl bg-red-50 flex items-center justify-center text-5xl">
            ❤️
        </div>

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
            Saved Recipes
        </p>

        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
            No Favorites Yet
        </h2>

        <p class="text-gray-500 mt-3 max-w-md mx-auto">
            When you find a recipe you love, save it here so you can easily find it again.
        </p>


        <a
            href="{{ route('recipes.explore') }}"
            class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition mt-6"
        >
            🌎 Explore Recipes
        </a>

    </div>

@endif

</div>


<!-- ================================================= -->
<!-- Add to Meal Plan Modal -->
<!-- ================================================= -->

<div
    id="favouritesCardMealModal"
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

                <p class="text-sm text-gray-500 mt-1" id="favouritesCardMealName"></p>

            </div>


            <button
                type="button"
                onclick="closeFavouritesCardMealModal()"
                class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl"
                aria-label="Close"
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
                id="favouritesCardMealRecipeId"
            >

            <div class="mb-5">

                <label
                    for="favouritesCardMealDate"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    id="favouritesCardMealDate"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
                >

            </div>


            <div class="mb-6">

                <label
                    for="favouritesCardMealType"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Meal Type
                </label>

                <select
                    name="meal_type"
                    id="favouritesCardMealType"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
                >

                    <option value="Breakfast">🍳 Breakfast</option>
                    <option value="Lunch">🍱 Lunch</option>
                    <option value="Dinner">🍽️ Dinner</option>

                </select>

            </div>


            <div class="grid grid-cols-2 gap-3">

                <button
                    type="button"
                    onclick="closeFavouritesCardMealModal()"
                    class="bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
                >
                    Cancel
                </button>


                <button
                    type="submit"
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
    function openFavouritesCardMealModal(recipeId, recipeName, category)
    {
        const modal = document.getElementById('favouritesCardMealModal');
        const recipeIdInput = document.getElementById('favouritesCardMealRecipeId');
        const nameLabel = document.getElementById('favouritesCardMealName');
        const mealType = document.getElementById('favouritesCardMealType');

        recipeIdInput.value = recipeId;
        nameLabel.textContent = recipeName;

        const validMealTypes = ['Breakfast', 'Lunch', 'Dinner'];

        mealType.value = validMealTypes.includes(category) ? category : 'Lunch';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeFavouritesCardMealModal()
    {
        const modal = document.getElementById('favouritesCardMealModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection
