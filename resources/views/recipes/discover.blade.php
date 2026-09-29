@extends('layouts.app')

@section('title', 'Discover Recipes')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

<!-- Header -->

<div class="mb-8">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">

        <div>

            <p class="text-orange-500 font-semibold mb-2">
                🌎 Explore the community
            </p>

            <h1 class="text-4xl font-bold text-gray-800">
                Discover Recipes
            </h1>

            <p class="text-gray-500 mt-2">
                Discover recipes shared publicly by other users.
            </p>

        </div>

        <a
            href="{{ url('/recipes') }}"
            class="text-orange-500 font-semibold hover:text-orange-600 transition"
        >
            ← My Recipes
        </a>

    </div>

</div>


<!-- Search / Filters -->

<form
    action="{{ route('recipes.discover') }}"
    method="GET"
    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8"
>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

        <!-- Search -->

        <div class="md:col-span-2">

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Search
            </label>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search public recipes..."
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
            >

        </div>


        <!-- Category -->

        <div>

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Category
            </label>

            <select
                name="category"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
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


        <!-- Sort -->

        <div>

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Sort By
            </label>

            <select
                name="sort"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
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


        <!-- Submit -->

        <div class="flex items-end">

            <button
                type="submit"
                class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
            >
                🔎 Search
            </button>

        </div>

    </div>

</form>


<!-- Results -->

@if ($recipes->isNotEmpty())

    <div class="flex items-center justify-between mb-5">

        <div>

            <h2 class="text-xl font-bold text-gray-800">
                🌎 Public Recipes
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $recipes->total() }} public recipes available
            </p>

        </div>

    </div>


    <!-- Recipe Grid -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($recipes as $recipe)

            <div class="group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">

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


                    <span class="absolute top-4 left-4 inline-flex items-center gap-1.5 bg-white/95 backdrop-blur text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                        🌎 Public
                    </span>


                    <!-- Card Actions -->

                    <div class="absolute top-4 right-4 z-10 flex items-center gap-1.5">

                        <button
                            type="button"
                            onclick="openDiscoverCardMealModal({{ $recipe->id }}, @js($recipe->name), @js($recipe->category))"
                            title="Add to meal plan"
                            aria-label="Add {{ $recipe->name }} to meal plan"
                            class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur text-gray-600 rounded-full shadow-sm text-sm hover:bg-orange-500 hover:text-white transition"
                        >
                            📅
                        </button>

                        @auth

                            @if (
                                $recipe->favorites()
                                    ->where('user_id', auth()->id())
                                    ->exists()
                            )

                                <form
                                    action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        title="Remove from favourites"
                                        aria-label="Remove {{ $recipe->name }} from favourites"
                                        class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                                    >
                                        ❤️
                                    </button>

                                </form>

                            @else

                                <form
                                    action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                                    method="POST"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        title="Add to favourites"
                                        aria-label="Add {{ $recipe->name }} to favourites"
                                        class="w-9 h-9 inline-flex items-center justify-center bg-white/95 backdrop-blur rounded-full shadow-sm text-sm hover:bg-red-500 hover:text-white transition"
                                    >
                                        ♡
                                    </button>

                                </form>

                            @endif

                        @endauth

                    </div>

                </div>


                <!-- Content -->

                <div class="p-6">

                    <h3 class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition">
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

                    @if ($recipe->description)

                        <p class="text-gray-500 mt-3 line-clamp-2">
                            {{ $recipe->description }}
                        </p>

                    @endif


                    <!-- Recipe Info -->

                    <div class="flex flex-wrap gap-2 mt-4">

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

                </div>

            </div>

        @endforeach

    </div>


    <!-- Pagination -->

    @if ($recipes->hasPages())

        <div class="mt-10 flex flex-col items-center gap-3">

            <p class="text-sm text-gray-500">

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

            </p>


            <div class="bg-white border border-gray-100 rounded-xl shadow-sm px-4 py-3">

                {{ $recipes->links() }}

            </div>

        </div>

    @endif


@else

    <!-- Empty State -->

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">

        <div class="text-6xl mb-5">
            🌎
        </div>

        <h2 class="text-2xl font-bold text-gray-800">
            No Public Recipes Found
        </h2>

        <p class="text-gray-500 mt-2">
            Try a different search or category.
        </p>

        @if (request()->hasAny(['search', 'category', 'sort']))

            <a
                href="{{ route('recipes.discover') }}"
                class="inline-block mt-6 bg-gray-800 text-white px-5 py-3 rounded-lg font-semibold hover:bg-gray-700 transition"
            >
                Clear Filters
            </a>

        @endif

    </div>

@endif

</div>


<!-- ================================================= -->
<!-- Add to Meal Plan Modal -->
<!-- ================================================= -->

<div
    id="discoverCardMealModal"
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

                <p class="text-sm text-gray-500 mt-1" id="discoverCardMealName"></p>

            </div>


            <button
                type="button"
                onclick="closeDiscoverCardMealModal()"
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
                id="discoverCardMealRecipeId"
            >

            <div class="mb-5">

                <label
                    for="discoverCardMealDate"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    id="discoverCardMealDate"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
                >

            </div>


            <div class="mb-6">

                <label
                    for="discoverCardMealType"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Meal Type
                </label>

                <select
                    name="meal_type"
                    id="discoverCardMealType"
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
                    onclick="closeDiscoverCardMealModal()"
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
    function openDiscoverCardMealModal(recipeId, recipeName, category)
    {
        const modal = document.getElementById('discoverCardMealModal');
        const recipeIdInput = document.getElementById('discoverCardMealRecipeId');
        const nameLabel = document.getElementById('discoverCardMealName');
        const mealType = document.getElementById('discoverCardMealType');

        recipeIdInput.value = recipeId;
        nameLabel.textContent = recipeName;

        const validMealTypes = ['Breakfast', 'Lunch', 'Dinner'];

        mealType.value = validMealTypes.includes(category) ? category : 'Lunch';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeDiscoverCardMealModal()
    {
        const modal = document.getElementById('discoverCardMealModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection

