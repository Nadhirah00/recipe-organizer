@extends('layouts.app')

@section('title', 'AI Meal Planning')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<div class="mb-8">

    <a
        href="{{ url('/meal-planner?week=' . $startOfWeek->format('Y-m-d')) }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-orange-500 transition"
    >
        ← Back to Meal Planner
    </a>


    <div class="mt-6">

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
            Smart Planning
        </p>

        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-gray-800 mt-1">
            🤖 Plan With AI
        </h1>

        <p class="text-gray-500 mt-2">
            Create a personalized meal plan for
            <span class="font-semibold text-gray-700">
                {{ $startOfWeek->format('d M') }}
                -
                {{ $endOfWeek->format('d M Y') }}
            </span>
        </p>

    </div>

</div>


<!-- ========================================================= -->
<!-- Form -->
<!-- ========================================================= -->

<form
    action="{{ route('meal-planner.ai.generate') }}"
    method="POST"
    class="space-y-6"
>

    @csrf


    <input
        type="hidden"
        name="week"
        value="{{ $startOfWeek->format('Y-m-d') }}"
    >


    <!-- ===================================================== -->
    <!-- Planning Mode -->
    <!-- ===================================================== -->

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 1
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                How should AI plan your week?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Your saved preferences will not be changed.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-4">


            <!-- Saved Preferences -->

            <label class="cursor-pointer">

                <input
                    type="radio"
                    name="use_custom"
                    value="0"
                    checked
                    class="peer sr-only"
                >


                <div class="rounded-2xl border-2 border-gray-200 bg-white p-5 transition peer-checked:border-orange-500 peer-checked:bg-orange-50">

                    <div class="flex items-start gap-4">

                        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center text-xl shrink-0">
                            ⚙️
                        </div>


                        <div class="flex-1">

                            <div class="flex items-center justify-between gap-3">

                                <p class="font-semibold text-gray-800">
                                    Use My Saved Preferences
                                </p>

                                <span class="hidden peer-checked:block text-orange-600 font-bold">
                                    ✓
                                </span>

                            </div>

                            <p class="text-sm text-gray-500 mt-1 leading-6">
                                Use the preferences you normally use for AI meal planning.
                            </p>

                        </div>

                    </div>

                </div>

            </label>


            <!-- Custom Preferences -->

            <label class="cursor-pointer">

                <input
                    type="radio"
                    name="use_custom"
                    value="1"
                    class="peer sr-only"
                >


                <div class="rounded-2xl border-2 border-gray-200 bg-white p-5 transition peer-checked:border-orange-500 peer-checked:bg-orange-50">

                    <div class="flex items-start gap-4">

                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-xl shrink-0">
                            ✨
                        </div>


                        <div class="flex-1">

                            <div class="flex items-center justify-between gap-3">

                                <p class="font-semibold text-gray-800">
                                    Customize For This Week
                                </p>

                                <span class="hidden peer-checked:block text-orange-600 font-bold">
                                    ✓
                                </span>

                            </div>

                            <p class="text-sm text-gray-500 mt-1 leading-6">
                                Make temporary changes for this week without changing your saved preferences.
                            </p>

                        </div>

                    </div>

                </div>

            </label>

        </div>

    </section>


    <!-- ===================================================== -->
    <!-- Custom Preferences -->
    <!-- ===================================================== -->

    <section
        id="customPreferences"
        class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8"
    >

        <div class="mb-6">

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Step 2
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Customize This Week
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                These settings apply only to this week's AI plan.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


            <!-- Difficulty -->

            <div>

                <label
                    for="difficulty"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    ⭐ Difficulty
                </label>

                <select
                    id="difficulty"
                    name="difficulty"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Any Difficulty
                    </option>

                    <option value="Easy">
                        Easy
                    </option>

                    <option value="Medium">
                        Medium
                    </option>

                    <option value="Hard">
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

                <select
                    id="max_cooking_time"
                    name="max_cooking_time"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Any Time
                    </option>

                    <option value="15">
                        Under 15 minutes
                    </option>

                    <option value="30">
                        Under 30 minutes
                    </option>

                    <option value="60">
                        Under 60 minutes
                    </option>

                </select>

            </div>


            <!-- Category -->

            <div>

                <label
                    for="categories"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    🏷 Category
                </label>

                <select
                    id="categories"
                    name="categories"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="">
                        Any Category
                    </option>

                    <option value="Breakfast">
                        Breakfast
                    </option>

                    <option value="Lunch">
                        Lunch
                    </option>

                    <option value="Dinner">
                        Dinner
                    </option>

                    <option value="Healthy">
                        Healthy
                    </option>

                    <option value="Dessert">
                        Dessert
                    </option>

                </select>

            </div>


            <!-- Meal Types -->

            <div>

                <label
                    for="meal_types"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    📅 Meals to Plan
                </label>

                <select
                    id="meal_types"
                    name="meal_types"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                >

                    <option value="Lunch,Dinner">
                        Lunch & Dinner
                    </option>

                    <option value="Breakfast,Lunch,Dinner">
                        Breakfast, Lunch & Dinner
                    </option>

                    <option value="Breakfast">
                        Breakfast Only
                    </option>

                    <option value="Lunch">
                        Lunch Only
                    </option>

                    <option value="Dinner">
                        Dinner Only
                    </option>

                </select>

            </div>


            <!-- Additional Preferences -->

            <div class="md:col-span-2">

                <label
                    for="additional_preferences"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    💬 Additional Preferences
                </label>

                <textarea
                    id="additional_preferences"
                    name="additional_preferences"
                    rows="4"
                    placeholder="Example: I want simple Malaysian-style meals this week."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition"
                ></textarea>

            </div>

        </div>

    </section>


    <!-- ===================================================== -->
    <!-- Generate -->
    <!-- ===================================================== -->

    <section class="relative overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-3xl p-6 md:p-8">

        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-orange-100/60"></div>


        <div class="relative z-10">

            <div class="flex items-start gap-4">

                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                    🤖
                </div>

                <div>

                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                        Ready?
                    </p>

                    <h2 class="text-2xl font-bold text-gray-800 mt-1">
                        Generate Your Weekly Plan
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        AI will suggest meals based on your selected preferences and available recipes.
                    </p>

                </div>

            </div>


            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6">

                <a
                    href="{{ url('/meal-planner?week=' . $startOfWeek->format('Y-m-d')) }}"
                    class="flex-1 inline-flex items-center justify-center bg-white border border-gray-200 text-gray-700 py-3.5 rounded-xl font-semibold hover:bg-gray-50 transition"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
                >
                    🤖 Generate AI Plan
                </button>

            </div>

        </div>

    </section>

</form>

</div>

<script>

    function toggleCustomPreferences(show)
    {
        const section =
            document.getElementById(
                'customPreferences'
            );


        if (show) {

            section.classList.remove('hidden');

        } else {

            section.classList.add('hidden');

        }
    }


    document
        .querySelectorAll(
            'input[name="use_custom"]'
        )
        .forEach(
            radio => {

                radio.addEventListener(
                    'change',
                    function () {

                        toggleCustomPreferences(
                            this.value === '1'
                        );

                    }
                );

            }
        );

</script>

@endsection
