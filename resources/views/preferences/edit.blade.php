@extends('layouts.app')

@section('title', 'Meal Preferences')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<div class="mb-8">

    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
        Personalization
    </p>

    <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
        ⚙️ Meal Preferences
    </h1>

    <p class="text-gray-500 mt-2 max-w-2xl">
        Tell us what you like so your meal suggestions can better match your preferences.
    </p>

</div>


<!-- ========================================================= -->
<!-- Success -->
<!-- ========================================================= -->




<!-- ========================================================= -->
<!-- Validation Errors -->
<!-- ========================================================= -->

@if ($errors->any())

    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-6">

        <div class="flex items-center gap-2 text-red-700 font-semibold">

            <span>
                ⚠️
            </span>

            <span>
                Please fix the following:
            </span>

        </div>


        <ul class="list-disc list-inside text-sm text-red-600 mt-3 space-y-1">

            @foreach ($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<!-- ========================================================= -->
<!-- Preferences Form -->
<!-- ========================================================= -->

<form
    action="{{ url('/preferences') }}"
    method="POST"
    class="space-y-6"
>

    @csrf
    @method('PUT')


    <!-- ===================================================== -->
    <!-- Cooking Preferences -->
    <!-- ===================================================== -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Cooking Style
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                🍳 How do you like to cook?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Set the difficulty and maximum time you prefer.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Difficulty -->

            <div>

                <label
                    for="preferred_difficulty"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    ⭐ Preferred Difficulty
                </label>

                <select
                    id="preferred_difficulty"
                    name="preferred_difficulty"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Any difficulty
                    </option>

                    <option
                        value="Easy"
                        {{ $preference->preferred_difficulty == 'Easy' ? 'selected' : '' }}
                    >
                        Easy
                    </option>

                    <option
                        value="Medium"
                        {{ $preference->preferred_difficulty == 'Medium' ? 'selected' : '' }}
                    >
                        Medium
                    </option>

                    <option
                        value="Hard"
                        {{ $preference->preferred_difficulty == 'Hard' ? 'selected' : '' }}
                    >
                        Hard
                    </option>

                </select>

            </div>


            <!-- Cooking Time -->

            <div>

                <label
                    for="max_cooking_time"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    ⏱ Maximum Cooking Time
                </label>

                <div class="relative">

                    <input
                        type="number"
                        id="max_cooking_time"
                        name="max_cooking_time"
                        value="{{ $preference->max_cooking_time }}"
                        min="1"
                        placeholder="30"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-20 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                    >

                    <span class="absolute right-4 top-3.5 text-sm text-gray-400">
                        minutes
                    </span>

                </div>

            </div>

        </div>

    </section>


    <!-- ===================================================== -->
    <!-- Categories -->
    <!-- ===================================================== -->

    @php

        $categories = $preference->preferred_categories
            ? array_map(
                'trim',
                explode(
                    ',',
                    $preference->preferred_categories
                )
            )
            : [];

    @endphp


    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Food Preferences
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                🥗 What kinds of food do you like?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Select all categories you enjoy.
            </p>

        </div>


        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

            @foreach ([
                'Breakfast',
                'Lunch',
                'Dinner',
                'Dessert',
                'Snack',
                'Drinks'
            ] as $category)

                @php

                    $categoryIcon = match ($category) {
                        'Breakfast' => '🍳',
                        'Lunch' => '🍱',
                        'Dinner' => '🍽️',
                        'Dessert' => '🍰',
                        'Snack' => '🍪',
                        'Drinks' => '🥤',
                        default => '🍴',
                    };

                @endphp


                <label class="cursor-pointer">

                    <input
                        type="checkbox"
                        name="preferred_categories[]"
                        value="{{ $category }}"
                        {{ in_array($category, $categories) ? 'checked' : '' }}
                        class="peer sr-only"
                    >


                    <div class="rounded-2xl border-2 border-gray-200 bg-white p-4 text-center transition hover:border-orange-300 peer-checked:border-orange-500 peer-checked:bg-orange-50">

                        <div class="text-2xl">
                            {{ $categoryIcon }}
                        </div>

                        <p class="font-semibold text-gray-800 mt-2">
                            {{ $category }}
                        </p>

                    </div>

                </label>

            @endforeach

        </div>

    </section>


    <!-- ===================================================== -->
    <!-- Meal Types -->
    <!-- ===================================================== -->

    @php

        $mealTypes = $preference->preferred_meal_types
            ? array_map(
                'trim',
                explode(
                    ',',
                    $preference->preferred_meal_types
                )
            )
            : [];

    @endphp


    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Meal Planning
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                📅 Which meals should we plan?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Choose the meal types you normally want included in your weekly plan.
            </p>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            @foreach ([
                'Breakfast' => '🍳',
                'Lunch' => '🍱',
                'Dinner' => '🍽️'
            ] as $meal => $icon)

                <label class="cursor-pointer">

                    <input
                        type="checkbox"
                        name="preferred_meal_types[]"
                        value="{{ $meal }}"
                        {{ in_array($meal, $mealTypes) ? 'checked' : '' }}
                        class="peer sr-only"
                    >


                    <div class="rounded-2xl border-2 border-gray-200 bg-white p-4 transition hover:border-orange-300 peer-checked:border-orange-500 peer-checked:bg-orange-50">

                        <div class="text-2xl">
                            {{ $icon }}
                        </div>

                        <p class="font-semibold text-gray-800 mt-2">
                            {{ $meal }}
                        </p>

                    </div>

                </label>

            @endforeach

        </div>

    </section>


    <!-- ===================================================== -->
    <!-- Additional Preferences -->
    <!-- ===================================================== -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Extra Preferences
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                💬 Anything else?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Add notes that can help personalize your meal suggestions.
            </p>

        </div>


        <textarea
            name="additional_preferences"
            rows="5"
            placeholder="Example: I prefer simple Malaysian meals and meals that don't take too long to prepare."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
        >{{ $preference->additional_preferences }}</textarea>

    </section>


    <!-- ===================================================== -->
    <!-- Save -->
    <!-- ===================================================== -->

    <div class="flex flex-col sm:flex-row gap-3">

        <button
            type="submit"
            class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >
            💾 Save Preferences
        </button>

    </div>

</form>


<!-- ========================================================= -->
<!-- AI Meal Planning -->
<!-- ========================================================= -->

<section class="mt-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 p-6 md:p-8">

    <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-orange-100/50"></div>


    <div class="relative z-10">

        <div class="flex items-start gap-4">

            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                🤖
            </div>

            <div>

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Smart Planning
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    AI Meal Planning
                </h2>

            </div>

        </div>


        <p class="text-gray-600 leading-7 mt-5 max-w-2xl">
            Once your preferences are saved, AI can suggest a weekly meal plan
            using your recipes and the preferences you selected.
        </p>


        <a
            href="{{ url('/meal-planner/ai') }}"
            class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-orange-600 transition mt-5"
        >
            🤖 Generate Weekly Plan
        </a>


        <p class="text-xs text-gray-400 mt-3">
            You can review and change the suggested meals before saving the plan.
        </p>

    </div>

</section>

</div>

@endsection
