@extends('layouts.app')

@section('title', 'Explore Recipes')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

<!-- Header -->

<div class="mb-8">

    <p class="text-orange-500 font-semibold mb-2">
        🌎 Explore
    </p>

    <h1 class="text-4xl font-bold text-gray-800">
        Explore Recipes
    </h1>

    <p class="text-gray-500 mt-2">
        Discover public recipes or find recipes based on the ingredients you have.
    </p>

</div>


<!-- Mode Tabs -->

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2 mb-8 inline-flex flex-wrap gap-2">

    <a
        href="{{ route('recipes.explore', ['mode' => 'browse']) }}"
        class="px-5 py-3 rounded-xl font-semibold transition
        {{ $mode === 'browse'
            ? 'bg-orange-500 text-white'
            : 'text-gray-600 hover:bg-gray-50' }}"
    >
        🌎 Discover Recipes
    </a>


    <a
        href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
        class="px-5 py-3 rounded-xl font-semibold transition
        {{ $mode === 'ingredients'
            ? 'bg-orange-500 text-white'
            : 'text-gray-600 hover:bg-gray-50' }}"
    >
        🥕 Find by Ingredients
    </a>

</div>


@if ($mode === 'ingredients')

    <!-- Ingredient Search -->

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">

        <h2 class="text-2xl font-bold text-gray-800">
            🥕 What ingredients do you have?
        </h2>

        <p class="text-gray-500 mt-1">
            Enter ingredients separated by commas.
        </p>


        <form
            action="{{ route('recipes.explore') }}"
            method="GET"
            class="mt-5"
        >

            <input
                type="hidden"
                name="mode"
                value="ingredients"
            >


            <textarea
                name="ingredients"
                rows="4"
                placeholder="Example: chicken, egg, rice, garlic"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
            >{{ request('ingredients') }}</textarea>


            <div class="flex flex-col sm:flex-row gap-3 mt-4">

                <button
                    type="submit"
                    class="bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                >
                    🔎 Find Recipes
                </button>


                @if (request()->filled('ingredients'))

                    <a
                        href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
                        class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition text-center"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </div>


    <!-- Entered Ingredients -->

    @if (!empty($userIngredients))

        <div class="mb-8">

            <p class="text-sm font-semibold text-gray-600 mb-3">
                You have:
            </p>

            <div class="flex flex-wrap gap-2">

                @foreach ($userIngredients as $ingredient)

                    <span class="bg-green-50 text-green-700 border border-green-100 px-3 py-1.5 rounded-full text-sm font-medium">
                        ✓ {{ $ingredient }}
                    </span>

                @endforeach

            </div>

        </div>

    @endif


    <!-- Ingredient Results -->

    @if (request()->filled('ingredients'))

        @if ($ingredientResults->isNotEmpty())

            <div class="mb-5">

                <h2 class="text-2xl font-bold text-gray-800">
                    Matching Recipes
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Recipes with at least one matching ingredient are shown.
                </p>

            </div>


            <div class="space-y-5">

                @foreach ($ingredientResults as $result)

                    @php
                        $recipe = $result['recipe'];
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                        <div class="flex flex-col lg:flex-row gap-6">

                            <!-- Image -->

                            <div class="w-full lg:w-56 h-40 shrink-0 rounded-xl overflow-hidden bg-orange-100">

                                @if ($recipe->image)

                                    <img
                                        src="{{ asset('storage/' . $recipe->image) }}"
                                        alt="{{ $recipe->name }}"
                                        class="w-full h-full object-cover"
                                    >

                                @else

                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-5xl">
                                            🍳
                                        </span>
                                    </div>

                                @endif

                            </div>


                            <!-- Content -->

                            <div class="flex-1">

                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

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

                                        <span class="inline-flex items-center bg-orange-50 text-orange-700 px-3 py-1.5 rounded-full font-bold">
                                            {{ $result['percentage'] }}%
                                            match
                                        </span>

                                    </div>

                                </div>


                                <!-- Progress -->

                                <div class="mt-4">

                                    <div class="flex justify-between text-sm mb-2">

                                        <span class="text-gray-500">
                                            {{ $result['matched_count'] }}
                                            of
                                            {{ $result['total_count'] }}
                                            ingredients available
                                        </span>

                                    </div>


                                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">

                                        <div
                                            class="h-full bg-orange-500 rounded-full"
                                            style="width: {{ $result['percentage'] }}%"
                                        ></div>

                                    </div>

                                </div>


                                <!-- Matched -->

                                <div class="mt-5">

                                    <p class="text-sm font-semibold text-gray-700 mb-2">
                                        ✓ You Have
                                    </p>

                                    <div class="flex flex-wrap gap-2">

                                        @foreach ($result['matched'] as $ingredient)

                                            <span class="bg-green-50 text-green-700 border border-green-100 px-3 py-1 rounded-full text-sm">
                                                {{ $ingredient }}
                                            </span>

                                        @endforeach

                                    </div>

                                </div>


                                <!-- Missing -->

                                @if (!empty($result['missing']))

                                    <div class="mt-4">

                                        <p class="text-sm font-semibold text-gray-700 mb-2">
                                            ✕ Still Needed
                                        </p>

                                        <div class="flex flex-wrap gap-2">

                                            @foreach ($result['missing'] as $ingredient)

                                                <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-full text-sm">
                                                    {{ $ingredient }}
                                                </span>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif


                                <!-- Actions -->

                                <div class="flex flex-col sm:flex-row gap-3 mt-6">

                                    <a
                                        href="{{ url('/recipes/' . $recipe->id) }}"
                                        class="text-center bg-orange-500 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-orange-600 transition"
                                    >
                                        View Recipe
                                    </a>


                                    <a
                                        href="{{ url('/recipes/' . $recipe->id) }}"
                                        class="text-center border border-orange-200 text-orange-500 px-5 py-2.5 rounded-lg font-semibold hover:bg-orange-50 transition"
                                    >
                                        📅 Add to Meal Plan
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">

                <div class="text-6xl mb-5">
                    🥕
                </div>

                <h2 class="text-2xl font-bold text-gray-800">
                    No Matching Recipes
                </h2>

                <p class="text-gray-500 mt-2">
                    Try adding another ingredient or using a more general ingredient name.
                </p>

            </div>

        @endif@extends('layouts.app')

@section('title', 'Explore Recipes')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

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


<!-- ========================================================= -->
<!-- Mode Tabs -->
<!-- ========================================================= -->

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-2 mb-8 inline-flex flex-wrap gap-2">

    <a
        href="{{ route('recipes.explore', ['mode' => 'browse']) }}"
        class="
            inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold transition
            {{ $mode === 'browse'
                ? 'bg-orange-500 text-white shadow-sm'
                : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}
        "
    >
        🌎 Discover Recipes
    </a>


    <a
        href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
        class="
            inline-flex items-center gap-2 px-5 py-3 rounded-xl font-semibold transition
            {{ $mode === 'ingredients'
                ? 'bg-orange-500 text-white shadow-sm'
                : 'text-gray-600 hover:bg-gray-50 hover:text-orange-500' }}
        "
    >
        🥕 Find by Ingredients
    </a>

</div>


@if ($mode === 'ingredients')


    <!-- ===================================================== -->
    <!-- Ingredient Search -->
    <!-- ===================================================== -->

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
                placeholder="Example: chicken, egg, rice, garlic"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
            >{{ request('ingredients') }}</textarea>


            <div class="flex flex-col sm:flex-row gap-3 mt-4">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                >
                    🔎 Find Recipes
                </button>


                @if (request()->filled('ingredients'))

                    <a
                        href="{{ route('recipes.explore', ['mode' => 'ingredients']) }}"
                        class="inline-flex items-center justify-center bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
                    >
                        Clear Search
                    </a>

                @endif

            </div>

        </form>

    </section>


    <!-- ===================================================== -->
    <!-- Entered Ingredients -->
    <!-- ===================================================== -->

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

                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-3 py-1.5 rounded-full text-sm font-medium">
                        ✓ {{ $ingredient }}
                    </span>

                @endforeach

            </div>

        </section>

    @endif

    <!-- ===================================================== -->
    <!-- Ingredient Results -->
    <!-- ===================================================== -->

    @if (request()->filled('ingredients'))

        @if ($ingredientResults->isNotEmpty())

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">

                <div>

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

            </div>


            <div class="space-y-5">

                @foreach ($ingredientResults as $result)

                    @php
                        $recipe = $result['recipe'];
                    @endphp


                    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">

                        <div class="flex flex-col lg:flex-row gap-6">


                            <!-- Image -->

                            <div class="w-full lg:w-56 h-40 shrink-0 rounded-2xl overflow-hidden bg-orange-50">

                                @if ($recipe->image)

                                    <img
                                        src="{{ asset('storage/' . $recipe->image) }}"
                                        alt="{{ $recipe->name }}"
                                        class="w-full h-full object-cover"
                                    >

                                @else

                                    <div class="w-full h-full flex items-center justify-center">

                                        <span class="text-5xl">
                                            🍳
                                        </span>

                                    </div>

                                @endif

                            </div>


                            <!-- Content -->

                            <div class="flex-1 min-w-0">


                                <!-- Title / Owner / Match -->

                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

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

                                        <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 px-3 py-1.5 rounded-full text-sm font-bold">
                                            {{ $result['percentage'] }}%
                                            match
                                        </span>

                                    </div>

                                </div>


                                <!-- Match Progress -->

                                <div class="mt-5">

                                    <div class="flex items-center justify-between gap-3 text-sm mb-2">

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


                                    <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">

                                        <div
                                            class="h-full bg-orange-500 rounded-full transition-all duration-500"
                                            style="width: {{ $result['percentage'] }}%"
                                        ></div>

                                    </div>

                                </div>


                                <!-- You Have -->

                                <div class="mt-5">

                                    <p class="text-sm font-semibold text-gray-700 mb-2">
                                        ✓ You Have
                                    </p>


                                    @if (!empty($result['matched']))

                                        <div class="flex flex-wrap gap-2">

                                            @foreach ($result['matched'] as $ingredient)

                                                <span class="bg-green-50 text-green-700 border border-green-100 px-3 py-1 rounded-full text-sm">
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


                                <!-- Still Needed -->

                                @if (!empty($result['missing']))

                                    <div class="mt-4">

                                        <p class="text-sm font-semibold text-gray-700 mb-2">
                                            ✕ Still Needed
                                        </p>


                                        <div class="flex flex-wrap gap-2">

                                            @foreach ($result['missing'] as $ingredient)

                                                <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-full text-sm">
                                                    {{ $ingredient }}
                                                </span>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif


                                <!-- Actions -->

                                <div class="flex flex-col sm:flex-row gap-3 mt-6">

                                    <a
                                        href="{{ url('/recipes/' . $recipe->id) }}"
                                        class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition"
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
                                        class="inline-flex items-center justify-center gap-2 border border-orange-200 text-orange-500 px-5 py-2.5 rounded-xl font-semibold hover:bg-orange-50 transition"
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

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

                <div class="mx-auto w-20 h-20 rounded-3xl bg-orange-50 flex items-center justify-center text-5xl">
                    🥕
                </div>


                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
                    No Matches
                </p>


                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                    No Matching Recipes
                </h2>


                <p class="text-gray-500 mt-3 max-w-md mx-auto">
                    Try adding another ingredient or using a more general ingredient name.
                </p>

            </div>

        @endif

	@endif


    @else


        <!-- ================================================= -->
        <!-- Discover Public Recipes -->
        <!-- ================================================= -->

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


            <!-- Browse Filters -->

            <form
                action="{{ route('recipes.explore') }}"
                method="GET"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-7"
            >

                <input
                    type="hidden"
                    name="mode"
                    value="browse"
                >


                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">


                    <!-- Search -->

                    <div class="md:col-span-2">

                        <label
                            for="exploreSearch"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                        >
                            🔎 Search
                        </label>

                        <input
                            type="text"
                            id="exploreSearch"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search public recipes..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                        >

                    </div>


                    <!-- Category -->

                    <div>

                        <label
                            for="exploreCategory"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                        >
                            🏷 Category
                        </label>

                        <select
                            id="exploreCategory"
                            name="category"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
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

                        <label
                            for="exploreSort"
                            class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2"
                        >
                            ↕ Sort By
                        </label>

                        <select
                            id="exploreSort"
                            name="sort"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
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


                    <!-- Search -->

                    <div class="flex items-end">

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                        >
                            🔎 Search
                        </button>

                    </div>

                </div>

            </form>


            @if ($publicRecipes->isNotEmpty())


                <!-- Public Recipe Cards -->

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach ($publicRecipes as $recipe)

                        <article class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition duration-300">


                            <!-- Image -->

                            <div class="relative h-48 bg-orange-50 overflow-hidden">

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


                                <span class="absolute top-4 left-4 inline-flex items-center gap-1 bg-white/95 backdrop-blur text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                    🌎 Public
                                </span>

                            </div>


                            <!-- Content -->

                            <div class="p-6">

                                <h3 class="text-xl font-bold text-gray-800 group-hover:text-orange-600 transition">
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


                                <!-- Actions -->

                                <div class="grid grid-cols-2 gap-2 mt-6">

                                    <a
                                        href="{{ url('/recipes/' . $recipe->id) }}"
                                        class="inline-flex items-center justify-center gap-1 bg-orange-500 text-white py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition"
                                    >
                                        View →
                                    </a>


                                    @auth

                                        <form
                                            action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                                            method="POST"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1 border border-orange-200 text-orange-500 py-2.5 rounded-xl font-semibold hover:bg-orange-50 transition"
                                            >
                                                ♡ Favorite
                                            </button>

                                        </form>

                                    @endauth

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


                <!-- Pagination -->

                @if ($publicRecipes->hasPages())

                    <div class="mt-10 flex justify-center">

                        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-4 py-3">

                            {{ $publicRecipes->links() }}

                        </div>

                    </div>

                @endif


            @else

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

                    <div class="mx-auto w-20 h-20 rounded-3xl bg-orange-50 flex items-center justify-center text-5xl">
                        🌎
                    </div>

                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
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

    @endif

</div>

@else

    <!-- Discover / Public Recipes -->

    <form
        action="{{ route('recipes.explore') }}"
        method="GET"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8"
    >

        <input
            type="hidden"
            name="mode"
            value="browse"
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
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        All
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
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white"
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


            <!-- Search Button -->

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


    @if ($publicRecipes->isNotEmpty())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($publicRecipes as $recipe)

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition">

                    @if ($recipe->image)

                        <img
                            src="{{ asset('storage/' . $recipe->image) }}"
                            alt="{{ $recipe->name }}"
                            class="w-full h-48 object-cover"
                        >

                    @else

                        <div class="bg-orange-100 h-48 flex items-center justify-center">

                            <span class="text-6xl">
                                🍳
                            </span>

                        </div>

                    @endif


                    <div class="p-6">

                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $recipe->name }}
                        </h2>


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

                            <p class="text-gray-500 mt-3 line-clamp-2">
                                {{ $recipe->description }}
                            </p>

                        @endif


                        <div class="flex flex-wrap gap-2 mt-4">

                            <span class="bg-orange-50 text-orange-700 px-3 py-1 rounded-full text-sm">
                                🏷 {{ $recipe->category }}
                            </span>

                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">
                                ⏱ {{ $recipe->cooking_time }} min
                            </span>

                            <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-sm">
                                ⭐ {{ $recipe->difficulty }}
                            </span>

                        </div>


                        <div class="flex gap-3 mt-6">

                            <a
                                href="{{ url('/recipes/' . $recipe->id) }}"
                                class="flex-1 text-center bg-orange-500 text-white py-2.5 rounded-lg font-semibold hover:bg-orange-600 transition"
                            >
                                View Recipe
                            </a>


                            @auth

                                <form
                                    action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                                    method="POST"
                                    class="flex-1"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="w-full border border-orange-200 text-orange-500 py-2.5 rounded-lg font-semibold hover:bg-orange-50 transition"
                                    >
                                        ♡ Favorite
                                    </button>

                                </form>

                            @endauth

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        @if ($publicRecipes->hasPages())

            <div class="mt-10 flex justify-center">

                <div class="bg-white border border-gray-100 rounded-xl shadow-sm px-4 py-3">

                    {{ $publicRecipes->links() }}

                </div>

            </div>

        @endif

    @else

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">

            <div class="text-6xl mb-5">
                🌎
            </div>

            <h2 class="text-2xl font-bold text-gray-800">
                No Public Recipes Found
            </h2>

            <p class="text-gray-500 mt-2">
                Try changing your search or category.
            </p>

        </div>

    @endif

@endif

</div>

@endsection

