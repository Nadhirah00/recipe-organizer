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


                        <!-- Favorite Badge -->

                        <div class="absolute top-4 right-4">

                            <span class="w-10 h-10 rounded-full bg-white/95 backdrop-blur flex items-center justify-center shadow-sm text-lg">
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


                        <!-- View -->

                        <a
                            href="{{ url('/recipes/' . $recipe->id) }}"
                            class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition mt-6"
                        >
                            View Recipe
                            <span>→</span>
                        </a>

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

@endsection
