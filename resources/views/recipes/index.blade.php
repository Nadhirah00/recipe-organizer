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

            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
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

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            @foreach ($recipes as $recipe)

                <article
                    class="group bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition duration-300"
                >

                    <!-- Image -->

                    <div class="relative h-56 bg-orange-50 overflow-hidden">

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


                        <!-- Visibility Badge -->

                        <div class="absolute top-4 left-4">

                            @if ($recipe->is_public)

                                <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                    🌎 Public
                                </span>

                            @else

                                <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur text-gray-600 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                    🔒 Private
                                </span>

                            @endif

                        </div>

                    </div>

                    <!-- Content -->

                    <div class="p-6">

                        <!-- Name -->

                        <h3 class="text-xl font-bold text-gray-800 leading-snug group-hover:text-orange-600 transition">
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

                        <!-- Details -->

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

                        <div class="flex items-center justify-between mt-4 text-sm text-gray-500">

                            <span>
                                👥
                                {{ $recipe->servings }}
                                {{ $recipe->servings == 1 ? 'serving' : 'servings' }}
                            </span>

                        </div>

                        <!-- Actions -->

                        <div class="grid grid-cols-2 gap-2 mt-6">

                            <a
                                href="{{ url('/recipes/' . $recipe->id) }}"
                                class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition"
                            >
                                View
                                <span>→</span>
                            </a>

                            <a
                                href="{{ url('/recipes/' . $recipe->id . '/edit') }}"
                                class="inline-flex items-center justify-center gap-2 bg-gray-800 text-white py-2.5 rounded-xl font-semibold hover:bg-gray-700 transition"
                            >
                                ✏️ Edit
                            </a>

                        </div>

                        <!-- Delete -->

                        <form
                            action="{{ url('/recipes/' . $recipe->id) }}"
                            method="POST"
                            class="mt-2"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                onclick="return confirm('Are you sure you want to delete this recipe?')"
                                class="w-full inline-flex items-center justify-center gap-2 border border-red-200 text-red-500 py-2.5 rounded-xl font-semibold hover:bg-red-50 transition"
                            >
                                🗑 Delete Recipe
                            </button>

                        </form>

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

@endsection