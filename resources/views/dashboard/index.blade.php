@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Welcome -->
<!-- ========================================================= -->

<section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-500 to-amber-400 p-8 md:p-10 mb-10">

    <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-white/10"></div>

    <div class="absolute right-20 -bottom-16 w-40 h-40 rounded-full bg-white/10"></div>


    <div class="relative z-10 max-w-3xl">

        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/80">
            Your kitchen, organized
        </p>

        <h1 class="text-4xl md:text-5xl font-bold text-white mt-3">
            Welcome back, {{ auth()->user()->name }} 👋
        </h1>

        <p class="text-white/85 text-lg leading-8 mt-4 max-w-2xl">
            Organize your recipes, discover new ideas,
            plan your meals, and keep your shopping list ready.
        </p>


        <div class="flex flex-col sm:flex-row gap-3 mt-7">

            <a
                href="{{ route('recipes.explore') }}"
                class="inline-flex items-center justify-center gap-2 bg-white text-orange-600 px-5 py-3 rounded-xl font-semibold hover:bg-orange-50 transition"
            >
                🌎 Explore Recipes
            </a>

            <a
                href="{{ url('/recipes/create') }}"
                class="inline-flex items-center justify-center gap-2 border border-white/40 bg-white/10 text-white px-5 py-3 rounded-xl font-semibold hover:bg-white/20 transition"
            >
                + Add Recipe
            </a>

        </div>

    </div>

</section>


<!-- ========================================================= -->
<!-- Statistics -->
<!-- ========================================================= -->

<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">

    <!-- Recipes -->

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-500">
                    My Recipes
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $recipeCount }}
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center text-xl">
                🍴
            </div>

        </div>

        <a
            href="{{ url('/recipes') }}"
            class="inline-block mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
        >
            View Recipes →
        </a>

    </div>


    <!-- Favorites -->

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-500">
                    Favorites
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $favoriteCount }}
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center text-xl">
                ❤️
            </div>

        </div>

        <a
            href="{{ url('/favorites') }}"
            class="inline-block mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
        >
            View Favorites →
        </a>

    </div>


    <!-- Meal Plan -->

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-500">
                    This Week
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $mealPlanCount }}
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-xl">
                📅
            </div>

        </div>

        <a
            href="{{ url('/meal-planner') }}"
            class="inline-block mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
        >
            View Meal Plan →
        </a>

    </div>


    <!-- Shopping -->

    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-500">
                    Shopping Items
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-2">
                    {{ $shoppingTotal }}
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center text-xl">
                🛒
            </div>

        </div>

        <a
            href="{{ url('/shopping-list') }}"
            class="inline-block mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
        >
            View Shopping List →
        </a>

    </div>

</section>


<!-- ========================================================= -->
<!-- Today's Meals + Weekly Progress -->
<!-- ========================================================= -->

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

    <!-- Today's Meals -->

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-start justify-between mb-6">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Today
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    🍽️ Today's Meals
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ now()->format('l, d F Y') }}
                </p>

            </div>

            <a
                href="{{ url('/meal-planner') }}"
                class="text-sm font-semibold text-orange-500 hover:text-orange-600"
            >
                View Plan →
            </a>

        </div>


        @if ($todayMeals->isNotEmpty())

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                @foreach ($todayMeals as $meal)

                    @php

                        $mealIcon = match ($meal->meal_type) {

                            'Breakfast' => '🍳',

                            'Lunch' => '🍱',

                            'Dinner' => '🍽️',

                            default => '🍴',

                        };

                    @endphp


                    <a
                        href="{{ url('/recipes/' . $meal->recipe->id) }}"
                        class="group rounded-xl border border-gray-100 bg-stone-50 p-4 hover:border-orange-200 hover:bg-orange-50 transition"
                    >

                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            {{ $mealIcon }}
                            {{ $meal->meal_type }}
                        </p>

                        <h3 class="font-bold text-gray-800 mt-3 group-hover:text-orange-600 transition">
                            {{ $meal->recipe->name }}
                        </h3>

                        <p class="text-xs text-gray-500 mt-2">
                            {{ $meal->recipe->cooking_time }} min
                            ·
                            {{ $meal->recipe->difficulty }}
                        </p>

                    </a>

                @endforeach

            </div>

        @else

            <div class="rounded-xl border border-dashed border-gray-200 bg-stone-50 p-8 text-center">

                <div class="text-4xl">
                    🍽️
                </div>

                <h3 class="font-semibold text-gray-800 mt-3">
                    No meals planned today
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Add a meal to get started.
                </p>

                <a
                    href="{{ url('/meal-planner') }}"
                    class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
                >
                    📅 Open Meal Planner →
                </a>

            </div>

        @endif

    </div>


    <!-- Weekly Progress -->

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
            This Week
        </p>

        <h2 class="text-2xl font-bold text-gray-800 mt-1">
            📅 Meal Progress
        </h2>

        <p class="text-sm text-gray-500 mt-2">
            {{ $mealPlanCount }}
            of
            {{ $totalWeeklySlots }}
            meal slots planned
        </p>


        <div class="mt-8">

            <div class="flex items-end justify-between">

                <span class="text-4xl font-bold text-orange-500">
                    {{ $weeklyProgress }}%
                </span>

                <span class="text-xs text-gray-400">
                    complete
                </span>

            </div>


            <div class="h-3 bg-orange-50 rounded-full overflow-hidden mt-3">

                <div
                    class="h-full bg-orange-500 rounded-full transition-all duration-500"
                    style="width: {{ $weeklyProgress }}%"
                ></div>

            </div>

        </div>


        <div class="mt-6 pt-5 border-t border-gray-100">

            @if ($mealPlanCount === 0)

                <p class="text-sm text-gray-500">
                    Your week is empty.
                </p>

            @elseif ($mealPlanCount >= $totalWeeklySlots)

                <p class="text-sm text-green-600 font-medium">
                    ✓ Your week is fully planned.
                </p>

            @else

                <p class="text-sm text-gray-500">
                    {{ $totalWeeklySlots - $mealPlanCount }}
                    meal slots remaining.
                </p>

            @endif


            <a
                href="{{ url('/meal-planner') }}"
                class="inline-flex mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
            >
                Manage Meal Plan →
            </a>

        </div>

    </div>

</section>


<!-- ========================================================= -->
<!-- Quick Actions -->
<!-- ========================================================= -->

<section class="mb-10">

    <div class="mb-5">

        <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
            Shortcuts
        </p>

        <h2 class="text-2xl font-bold text-gray-800 mt-1">
            Quick Actions
        </h2>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <a
            href="{{ url('/recipes/create') }}"
            class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-orange-200 hover:shadow-sm transition"
        >

            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-lg">
                ➕
            </div>

            <h3 class="font-bold text-gray-800 mt-4">
                Add Recipe
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Save something new.
            </p>

        </a>


        <a
            href="{{ route('recipes.explore') }}"
            class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-orange-200 hover:shadow-sm transition"
        >

            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-lg">
                🌎
            </div>

            <h3 class="font-bold text-gray-800 mt-4">
                Explore Recipes
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Discover something new.
            </p>

        </a>


        <a
            href="{{ url('/meal-planner') }}"
            class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-orange-200 hover:shadow-sm transition"
        >

            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-lg">
                📅
            </div>

            <h3 class="font-bold text-gray-800 mt-4">
                Plan Meals
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Organize your week.
            </p>

        </a>


        <a
            href="{{ url('/shopping-list?week=' . now()->startOfWeek()->format('Y-m-d')) }}"
            class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-orange-200 hover:shadow-sm transition"
        >

            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-lg">
                🛒
            </div>

            <h3 class="font-bold text-gray-800 mt-4">
                Shopping List
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Check what you need.
            </p>

        </a>

    </div>

</section>


<!-- ========================================================= -->
<!-- Bottom Section -->
<!-- ========================================================= -->

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Recent Recipes -->

    <div class="lg:col-span-2">

        <div class="flex items-end justify-between mb-5">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Your Collection
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    Recent Recipes
                </h2>

            </div>

            <a
                href="{{ url('/recipes') }}"
                class="text-sm font-semibold text-orange-500 hover:text-orange-600"
            >
                View All →
            </a>

        </div>


        @if ($recentRecipes->isNotEmpty())

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                @foreach ($recentRecipes as $recipe)

                    <a
                        href="{{ url('/recipes/' . $recipe->id) }}"
                        class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition"
                    >

                        <div class="h-40 bg-orange-50 overflow-hidden">

                            @if ($recipe->image)

                                <img
                                    src="{{ asset('storage/' . $recipe->image) }}"
                                    alt="{{ $recipe->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                >

                            @else

                                <div class="h-full flex items-center justify-center">
                                    <span class="text-5xl">
                                        🍳
                                    </span>
                                </div>

                            @endif

                        </div>


                        <div class="p-4">

                            <h3 class="font-bold text-gray-800 group-hover:text-orange-600 transition">
                                {{ $recipe->name }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-2">
                                {{ $recipe->category }}
                                ·
                                {{ $recipe->cooking_time }} min
                            </p>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">

                <div class="text-4xl">
                    🍳
                </div>

                <h3 class="font-semibold text-gray-800 mt-3">
                    No recipes yet
                </h3>

                <a
                    href="{{ url('/recipes/create') }}"
                    class="inline-flex mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
                >
                    Add your first recipe →
                </a>

            </div>

        @endif

    </div>


    <!-- Shopping Progress -->

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                    Shopping
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    🛒 Progress
                </h2>

            </div>

            <a
                href="{{ url('/shopping-list') }}"
                class="text-sm font-semibold text-orange-500 hover:text-orange-600"
            >
                View →
            </a>

        </div>


        @if ($shoppingTotal > 0)

            @php

                $shoppingPercentage = round(
                    ($shoppingCompleted / $shoppingTotal) * 100
                );

            @endphp


            <div class="mt-8">

                <div class="flex items-end justify-between">

                    <span class="text-4xl font-bold text-gray-800">
                        {{ $shoppingPercentage }}%
                    </span>

                    <span class="text-sm text-gray-500">
                        {{ $shoppingCompleted }}
                        /
                        {{ $shoppingTotal }}
                    </span>

                </div>


                <div class="h-3 bg-green-50 rounded-full overflow-hidden mt-3">

                    <div
                        class="h-full bg-green-500 rounded-full transition-all duration-500"
                        style="width: {{ $shoppingPercentage }}%"
                    ></div>

                </div>


                <p class="text-sm text-gray-500 mt-4">

                    @if ($shoppingPercentage === 100)

                        ✓ Everything is checked off.

                    @else

                        {{ $shoppingTotal - $shoppingCompleted }}
                        items remaining.

                    @endif

                </p>

            </div>

        @else

            <div class="mt-8 rounded-xl bg-stone-50 p-6 text-center">

                <div class="text-3xl">
                    🛒
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Your shopping list is empty.
                </p>

                <a
                    href="{{ url('/shopping-list') }}"
                    class="inline-flex mt-4 text-sm font-semibold text-orange-500 hover:text-orange-600"
                >
                    Open Shopping List →
                </a>

            </div>

        @endif

    </div>

</section>

</div>

@endsection
