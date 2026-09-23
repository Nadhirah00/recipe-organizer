@extends('layouts.app')

@section('title', 'Explore Recipes')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- =========================================================
         Page Header
    ========================================================== --}}
    <div class="mb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
            Discover & Cook
        </p>

        <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
            🌎 Explore Recipes
        </h1>

        <p class="text-gray-500 mt-2 max-w-2xl">
            Discover recipes shared by the community or find something
            you can make with the ingredients you already have.
        </p>
    </div>


    {{-- =========================================================
         Explore Mode Tabs
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2 mb-8 inline-flex flex-wrap gap-2">

        <a
            href="{{ route('recipes.explore', ['mode' => 'browse']) }}"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold transition
                {{ $mode === 'browse'
                    ? 'bg-orange-500 text-white shadow-sm'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}"
        >
            🌎 Discover Recipes
        </a>

        <a
            href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold transition
                {{ $mode === 'ingredients'
                    ? 'bg-orange-500 text-white shadow-sm'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}"
        >
            🥕 Find by Ingredients
        </a>

    </div>


    {{-- =========================================================
         MODE 1: FIND BY INGREDIENTS
    ========================================================== --}}
    @if ($mode === 'ingredients')

        {{-- Ingredient Search --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-7 mb-8">

            <div class="mb-5">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Ingredient Search
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    🥕 What ingredients do you have?
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Enter the ingredients you currently have and we'll find recipes that match.
                    Separate them with commas, one per line, or slashes — e.g. <span class="font-semibold text-gray-600">rice / chicken</span>.
                </p>
            </div>


            <form
                action="{{ route('recipes.explore') }}"
                method="GET"
            >

                <input
                    type="hidden"
                    name="mode"
                    value="ingredients"
                >

                <textarea
                    name="ingredients"
                    rows="4"
                    placeholder="Chicken
Rice
Egg
Garlic"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50
                           focus:bg-white focus:outline-none focus:ring-2
                           focus:ring-orange-400 focus:border-orange-400 transition"
                >{{ request('ingredients') }}</textarea>


                {{-- Quick examples --}}
                <div class="mt-3">

                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Quick examples · click to add
                    </p>

                    <div class="flex flex-wrap gap-2">

                        @foreach (['Chicken', 'Rice', 'Egg', 'Garlic', 'Onion', 'Tomato'] as $example)

                            <button
                                type="button"
                                class="quick-ingredient inline-flex items-center gap-1
                                       bg-gray-100 text-gray-600 border border-gray-200
                                       px-3 py-1.5 rounded-full text-sm font-medium
                                       hover:bg-orange-50 hover:text-orange-600
                                       hover:border-orange-200 transition"
                                data-ingredient="{{ $example }}"
                            >
                                + {{ $example }}
                            </button>

                        @endforeach

                    </div>

                </div>


                <div class="flex flex-col sm:flex-row gap-3 mt-4">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2
                               bg-orange-500 text-white px-6 py-3 rounded-xl
                               font-semibold hover:bg-orange-600 transition"
                    >
                        🔎 Find Recipes
                    </button>


                    @if (request()->filled('ingredients'))

                        <a
                            href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
                            class="inline-flex items-center justify-center
                                   bg-gray-100 text-gray-700 px-6 py-3 rounded-xl
                                   font-semibold hover:bg-gray-200 transition"
                        >
                            Clear Search
                        </a>

                    @endif

                </div>

            </form>

        </section>


        {{-- Entered Ingredients --}}
        @if (!empty($userIngredients))

            <section class="mb-8">

                <div class="flex items-center justify-between gap-3 mb-3">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Your Ingredients
                        </p>

                        <p class="text-sm font-semibold text-gray-700 mt-1">
                            Ingredients you entered
                        </p>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ count($userIngredients) }}
                        {{ count($userIngredients) === 1 ? 'item' : 'items' }}
                    </span>

                </div>


                <div class="flex flex-wrap gap-2">

                    @foreach ($userIngredients as $ingredient)

                        <span
                            class="inline-flex items-center gap-1.5
                                   bg-green-50 text-green-700 border border-green-100
                                   px-3 py-1.5 rounded-full text-sm font-medium"
                        >
                            ✓ {{ $ingredient }}
                        </span>

                    @endforeach

                </div>

            </section>

        @endif


        {{-- Ingredient Results --}}
        @if (request()->filled('ingredients'))

            @if ($ingredientResults->isNotEmpty())

                <div class="mb-5">

                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                        Recipe Matches
                    </p>

                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        Matching Recipes
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Recipes are ranked by how many of your ingredients they use.
                    </p>

                </div>


                <div class="space-y-5">

                    @foreach ($ingredientResults as $result)

                        @php
                            $recipe = $result['recipe'];
                        @endphp

                        <article
                            class="bg-white rounded-2xl border border-gray-100
                                   shadow-sm p-6 hover:shadow-md transition"
                        >

                            <div class="flex flex-col lg:flex-row gap-6">

                                {{-- Recipe Image --}}
                                <div
                                    class="w-full lg:w-56 h-40 shrink-0
                                           rounded-2xl overflow-hidden bg-orange-50"
                                >

                                    @if ($recipe->image)

                                        <img
                                            src="{{ asset('storage/' . $recipe->image) }}"
                                            alt="{{ $recipe->name }}"
                                            class="w-full h-full object-cover"
                                        >

                                    @else

                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-5xl">🍳</span>
                                        </div>

                                    @endif

                                </div>


                                {{-- Recipe Content --}}
                                <div class="flex-1 min-w-0">

                                    {{-- Recipe Header --}}
                                    <div
                                        class="flex flex-col sm:flex-row
                                               sm:items-start sm:justify-between gap-4"
                                    >

                                        <div>

                                            <h3 class="text-2xl font-bold text-gray-800">
                                                {{ $recipe->name }}
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $recipe->category }}
                                                ·
                                                {{ $recipe->cooking_time }} min
                                                ·
                                                {{ $recipe->difficulty }}
                                            </p>

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

                                        </div>


                                        <div class="shrink-0">

                                            <span
                                                class="inline-flex items-center gap-1.5
                                                       bg-orange-50 text-orange-700
                                                       px-3 py-1.5 rounded-full
                                                       text-sm font-bold"
                                            >
                                                {{ $result['percentage'] }}% match
                                            </span>

                                        </div>

                                    </div>


                                    {{-- Match Progress --}}
                                    <div class="mt-5">

                                        <div
                                            class="flex items-center justify-between
                                                   gap-3 text-sm mb-2"
                                        >

                                            <span class="text-gray-500">
                                                {{ $result['matched_count'] }}
                                                of
                                                {{ $result['total_count'] }}
                                                ingredients available
                                            </span>

                                            <span class="font-semibold text-gray-700">
                                                {{ $result['percentage'] }}%
                                            </span>

                                        </div>


                                        <div
                                            class="w-full h-2.5 bg-gray-100
                                                   rounded-full overflow-hidden"
                                        >

                                            <div
                                                class="h-full bg-orange-500 rounded-full
                                                       transition-all duration-500"
                                                style="width: {{ $result['percentage'] }}%"
                                            ></div>

                                        </div>

                                    </div>


                                    {{-- Matched Ingredients --}}
                                    <div class="mt-5">

                                        <p class="text-sm font-semibold text-gray-700 mb-2">
                                            ✓ You Have
                                        </p>


                                        @if (!empty($result['matched']))

                                            <div class="flex flex-wrap gap-2">

                                                @foreach ($result['matched'] as $ingredient)

                                                    <span
                                                        class="bg-green-50 text-green-700
                                                               border border-green-100
                                                               px-3 py-1 rounded-full text-sm"
                                                    >
                                                        {{ $ingredient }}
                                                    </span>

                                                @endforeach

                                            </div>

                                        @else

                                            <p class="text-sm text-gray-400">
                                                None of the listed ingredients matched.
                                            </p>

                                        @endif

                                    </div>


                                    {{-- Missing Ingredients --}}
                                    @if (!empty($result['missing']))

                                        <div class="mt-4">

                                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                                ✕ Still Needed
                                            </p>


                                            <div class="flex flex-wrap gap-2">

                                                @foreach ($result['missing'] as $ingredient)

                                                    <span
                                                        class="bg-red-50 text-red-600
                                                               border border-red-100
                                                               px-3 py-1 rounded-full text-sm"
                                                    >
                                                        {{ $ingredient }}
                                                    </span>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    {{-- Actions --}}
                                    <div class="flex flex-col sm:flex-row gap-3 mt-6">

                                        <a
                                            href="{{ url('/recipes/' . $recipe->id) }}"
                                            class="inline-flex items-center justify-center gap-2
                                                   bg-orange-500 text-white px-5 py-2.5
                                                   rounded-xl font-semibold
                                                   hover:bg-orange-600 transition"
                                        >
                                            View Recipe →
                                        </a>


                                        <button
                                            type="button"
                                            onclick="openExploreMealModal(
                                                {{ $recipe->id }},
                                                @js($recipe->name),
                                                @js($recipe->category)
                                            )"
                                            class="inline-flex items-center justify-center gap-2
                                                   border border-orange-200 text-orange-500
                                                   px-5 py-2.5 rounded-xl font-semibold
                                                   hover:bg-orange-50 transition"
                                        >
                                            📅 Add to Meal Plan
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


            @else

                {{-- No Ingredient Matches --}}
                <div
                    class="bg-white rounded-3xl border border-gray-100
                           shadow-sm p-12 md:p-16 text-center"
                >

                    <div
                        class="mx-auto w-20 h-20 rounded-3xl bg-orange-50
                               flex items-center justify-center text-5xl"
                    >
                        🥕
                    </div>

                    <p
                        class="text-xs font-semibold uppercase
                               tracking-[0.15em] text-orange-500 mt-6"
                    >
                        No Matches
                    </p>

                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                        No Matching Recipes
                    </h2>

                    <p class="text-gray-500 mt-3 max-w-lg mx-auto leading-7">
                        We compared your ingredients against <span class="font-semibold text-gray-600">your recipes and public recipes from the community</span>,
                        but nothing matched yet.
                    </p>


                    {{-- Tips --}}
                    <div
                        class="mt-8 bg-gray-50 border border-gray-100 rounded-2xl
                               p-5 text-left max-w-lg mx-auto"
                    >

                        <p class="text-sm font-bold text-gray-800 mb-3">
                            💡 How to get better matches
                        </p>

                        <ul class="space-y-2 text-sm text-gray-600">

                            <li class="flex items-start gap-2">
                                <span>•</span>
                                <span>Use simple, general names — <span class="font-semibold text-gray-700">chicken</span>, <span class="font-semibold text-gray-700">rice</span>, <span class="font-semibold text-gray-700">egg</span> — instead of "boneless skinless chicken breast".</span>
                            </li>

                            <li class="flex items-start gap-2">
                                <span>•</span>
                                <span>Separate ingredients with commas, one per line, or slashes: <span class="font-semibold text-gray-700">rice / chicken</span>.</span>
                            </li>

                            <li class="flex items-start gap-2">
                                <span>•</span>
                                <span>You're searching your recipes plus recipes shared publicly by other users.</span>
                            </li>

                        </ul>

                    </div>


                    {{-- Try an example --}}
                    <div class="mt-8">

                        <p class="text-sm font-semibold text-gray-600 mb-3">
                            Or try a quick example ingredient
                        </p>

                        <div class="flex flex-wrap justify-center gap-2">

                            @foreach (['Chicken', 'Rice', 'Egg', 'Garlic'] as $example)

                                <form
                                    action="{{ route('recipes.explore') }}"
                                    method="GET"
                                >

                                    <input type="hidden" name="mode" value="ingredients">

                                    <input type="hidden" name="ingredients" value="{{ $example }}">

                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1
                                               bg-gray-100 text-gray-600 border border-gray-200
                                               px-4 py-2 rounded-full text-sm font-medium
                                               hover:bg-orange-50 hover:text-orange-600
                                               hover:border-orange-200 transition"
                                    >
                                        + {{ $example }}
                                    </button>

                                </form>

                            @endforeach

                        </div>

                    </div>


                    {{-- Fallback links --}}
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">

                        <a
                            href="{{ route('recipes.explore', ['mode' => 'browse']) }}"
                            class="inline-flex items-center justify-center gap-2
                                   bg-orange-500 text-white px-6 py-3 rounded-xl
                                   font-semibold hover:bg-orange-600 transition"
                        >
                            🌎 Browse All Public Recipes
                        </a>

                        <a
                            href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
                            class="inline-flex items-center justify-center gap-2
                                   bg-gray-100 text-gray-700 px-6 py-3 rounded-xl
                                   font-semibold hover:bg-gray-200 transition"
                        >
                            ← Search Again
                        </a>

                    </div>

                </div>

            @endif

        @endif


    {{-- =========================================================
         MODE 2: DISCOVER PUBLIC RECIPES
    ========================================================== --}}
    @else

        <section>

            <div class="mb-5">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Community Recipes
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    Discover Public Recipes
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Explore recipes shared by other users.
                </p>

            </div>


            {{-- Browse Filters --}}
            <form
                action="{{ route('recipes.explore') }}"
                method="GET"
                class="bg-white rounded-2xl border border-gray-100
                       shadow-sm p-5 mb-7"
            >

                <input
                    type="hidden"
                    name="mode"
                    value="browse"
                >


                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-2">

                        <label
                            for="exploreSearch"
                            class="block text-xs font-semibold
                                   uppercase tracking-wide text-gray-500 mb-2"
                        >
                            🔎 Search
                        </label>

                        <input
                            type="text"
                            id="exploreSearch"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search public recipes..."
                            class="w-full border border-gray-200 rounded-xl
                                   px-4 py-3 bg-gray-50 focus:bg-white
                                   focus:outline-none focus:ring-2
                                   focus:ring-orange-400 focus:border-orange-400"
                        >

                    </div>


                    {{-- Category --}}
                    <div>

                        <label
                            for="exploreCategory"
                            class="block text-xs font-semibold
                                   uppercase tracking-wide text-gray-500 mb-2"
                        >
                            🏷 Category
                        </label>

                        <select
                            id="exploreCategory"
                            name="category"
                            class="w-full border border-gray-200 rounded-xl
                                   px-4 py-3 bg-white focus:outline-none
                                   focus:ring-2 focus:ring-orange-400"
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


                    {{-- Sort --}}
                    <div>

                        <label
                            for="exploreSort"
                            class="block text-xs font-semibold
                                   uppercase tracking-wide text-gray-500 mb-2"
                        >
                            ↕ Sort By
                        </label>

                        <select
                            id="exploreSort"
                            name="sort"
                            class="w-full border border-gray-200 rounded-xl
                                   px-4 py-3 bg-white focus:outline-none
                                   focus:ring-2 focus:ring-orange-400"
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


                    {{-- Search Button --}}
                    <div class="flex items-end">

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center
                                   gap-2 bg-orange-500 text-white py-3
                                   rounded-xl font-semibold
                                   hover:bg-orange-600 transition"
                        >
                            🔎 Search
                        </button>

                    </div>

                </div>

            </form>


            {{-- Public Recipe Results --}}
            @if ($publicRecipes->isNotEmpty())

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($publicRecipes as $recipe)

                        <article
                            class="group bg-white rounded-2xl border border-gray-100
                                   shadow-sm overflow-hidden hover:shadow-lg
                                   transition duration-300"
                        >

                            {{-- Image --}}
                            <div
                                class="relative h-48 bg-orange-50 overflow-hidden"
                            >

                                @if ($recipe->image)

                                    <img
                                        src="{{ asset('storage/' . $recipe->image) }}"
                                        alt="{{ $recipe->name }}"
                                        class="w-full h-full object-cover
                                               group-hover:scale-105
                                               transition duration-500"
                                    >

                                @else

                                    <div
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <span class="text-6xl">🍳</span>
                                    </div>

                                @endif


                                <span
                                    class="absolute top-4 left-4 inline-flex items-center
                                           gap-1 bg-white/95 backdrop-blur
                                           text-green-700 px-3 py-1.5 rounded-full
                                           text-xs font-semibold shadow-sm"
                                >
                                    🌎 Public
                                </span>

                            </div>


                            {{-- Content --}}
                            <div class="p-6">

                                <h3
                                    class="text-xl font-bold text-gray-800
                                           group-hover:text-orange-600 transition"
                                >
                                    {{ $recipe->name }}
                                </h3>


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


                                @if ($recipe->description)

                                    <p class="text-sm text-gray-500 mt-3 line-clamp-2">
                                        {{ $recipe->description }}
                                    </p>

                                @endif


                                {{-- Recipe Details --}}
                                <div class="flex flex-wrap gap-2 mt-4">

                                    <span
                                        class="bg-orange-50 text-orange-700
                                               px-3 py-1.5 rounded-full
                                               text-xs font-semibold"
                                    >
                                        🏷 {{ $recipe->category }}
                                    </span>

                                    <span
                                        class="bg-blue-50 text-blue-700
                                               px-3 py-1.5 rounded-full
                                               text-xs font-semibold"
                                    >
                                        ⏱ {{ $recipe->cooking_time }} min
                                    </span>

                                    <span
                                        class="bg-purple-50 text-purple-700
                                               px-3 py-1.5 rounded-full
                                               text-xs font-semibold"
                                    >
                                        ⭐ {{ $recipe->difficulty }}
                                    </span>

                                </div>


                                {{-- Actions --}}
                                <div class="grid grid-cols-2 gap-2 mt-6">

                                    <a
                                        href="{{ url('/recipes/' . $recipe->id) }}"
                                        class="inline-flex items-center justify-center gap-1
                                               bg-orange-500 text-white py-2.5
                                               rounded-xl font-semibold
                                               hover:bg-orange-600 transition"
                                    >
                                        View →
                                    </a>


                                    @auth

                                        @if (
                                            $recipe->favorites
                                                ->contains(
                                                    fn ($favorite) =>
                                                        $favorite->user_id === auth()->id()
                                                )
                                        )

                                            <form
                                                action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                                                method="POST"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="w-full inline-flex items-center
                                                           justify-center gap-1
                                                           border border-red-200
                                                           text-red-500 py-2.5 rounded-xl
                                                           font-semibold
                                                           hover:bg-red-50 transition"
                                                >
                                                    ❤️ Favorited
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
                                                    class="w-full inline-flex items-center
                                                           justify-center gap-1
                                                           border border-orange-200
                                                           text-orange-500 py-2.5 rounded-xl
                                                           font-semibold
                                                           hover:bg-orange-50 transition"
                                                >
                                                    ♡ Favorite
                                                </button>

                                            </form>

                                        @endif

                                    @endauth

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


                {{-- Pagination --}}
                @if ($publicRecipes->hasPages())

                    <div class="mt-10 flex justify-center">

                        <div
                            class="bg-white border border-gray-100
                                   rounded-2xl shadow-sm px-4 py-3"
                        >
                            {{ $publicRecipes->links() }}
                        </div>

                    </div>

                @endif


            @else

                {{-- No Public Recipes --}}
                <div
                    class="bg-white rounded-3xl border border-gray-100
                           shadow-sm p-12 md:p-16 text-center"
                >

                    <div
                        class="mx-auto w-20 h-20 rounded-3xl bg-orange-50
                               flex items-center justify-center text-5xl"
                    >
                        🌎
                    </div>

                    <p
                        class="text-xs font-semibold uppercase
                               tracking-[0.15em] text-orange-500 mt-6"
                    >
                        Nothing Found
                    </p>

                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                        No Public Recipes Found
                    </h2>

                    <p class="text-gray-500 mt-3 max-w-md mx-auto">
                        Try a different search term or category.
                    </p>

                </div>

            @endif

        </section>

    @endif

</div>

<!-- ========================================================= -->
<!-- Add to Meal Plan Modal -->
<!-- ========================================================= -->

<div
    id="exploreMealModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4"
>

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

        <div class="flex items-center justify-between mb-5">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Add to Meal Plan
                </h2>

                <p
                    id="exploreMealRecipeName"
                    class="text-gray-500 mt-1"
                ></p>

            </div>

            <button
                type="button"
                onclick="closeExploreMealModal()"
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
                id="exploreRecipeId"
            >


            <div class="mb-5">

                <label
                    for="exploreMealDate"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    id="exploreMealDate"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    required
                >

            </div>


            <div class="mb-6">

                <label
                    for="exploreMealType"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Meal Type
                </label>

                <p class="text-xs text-gray-400 mt-2">
                    The meal type is based on the recipe category, but you can change it.
                </p>

                <select
                    name="meal_type"
                    id="exploreMealType"
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
                    onclick="closeExploreMealModal()"
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

    document.querySelectorAll('.quick-ingredient').forEach(button => {

        button.addEventListener('click', () => {

            const textarea = document.querySelector(
                'textarea[name="ingredients"]'
            );

            if (!textarea) {
                return;
            }

            const ingredient = button.dataset.ingredient;

            const current = textarea.value.trim();

            if (current === '') {

                textarea.value = ingredient;

            } else if (
                current
                    .toLowerCase()
                    .split(/[\r\n,;\/&|]+/)
                    .map(item => item.trim())
                    .filter(Boolean)
                    .some(item => item.toLowerCase() === ingredient.toLowerCase())
            ) {

                return;

            } else {

                textarea.value = current + ', ' + ingredient;

            }

            textarea.focus();
        });

    });


    function openExploreMealModal(recipeId, recipeName, category)
    {
        document.getElementById('exploreRecipeId').value = recipeId;

        document.getElementById('exploreMealRecipeName').innerText = recipeName;

        const mealType = document.getElementById('exploreMealType');

        const validMealTypes = ['Breakfast', 'Lunch', 'Dinner'];

        mealType.value = validMealTypes.includes(category) ? category : 'Lunch';

        const modal = document.getElementById('exploreMealModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeExploreMealModal()
    {
        const modal = document.getElementById('exploreMealModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection