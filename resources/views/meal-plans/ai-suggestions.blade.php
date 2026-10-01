@extends('layouts.app')

@section('title', 'AI Meal Suggestions')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

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
            🤖 AI Weekly Meal Plan
        </h1>

        <p class="text-gray-500 mt-2 max-w-2xl">
            Review the suggestions, change anything you don't like,
            and accept the plan when you're ready.
        </p>


        <div class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 px-4 py-2 rounded-xl font-semibold text-sm mt-4">
            📅
            {{ $startOfWeek->format('d M') }}
            -
            {{ $endOfWeek->format('d M Y') }}
        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- Plan Status -->
<!-- ========================================================= -->

@if ($missingSlots === 0)

    <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-4">

        <div class="flex items-start gap-3">

            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-lg shrink-0">
                ✓
            </div>

            <div>

                <p class="font-semibold text-green-800">
                    Your AI plan is complete
                </p>

                <p class="text-sm text-green-700 mt-1">
                    {{ $filledSlots }} of {{ $totalSlots }}
                    meal slots have been planned.
                </p>

            </div>

        </div>

    </div>

@else

    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-4">

        <div class="flex items-start gap-3">

            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center text-lg shrink-0">
                ⚠️
            </div>

            <div>

                <p class="font-semibold text-yellow-800">
                    Your AI plan is not complete
                </p>

                <p class="text-sm text-yellow-700 mt-1">
                    {{ $filledSlots }} of {{ $totalSlots }}
                    meal slots have been suggested.
                    {{ $missingSlots }}
                    {{ $missingSlots === 1 ? 'slot needs' : 'slots need' }}
                    attention.
                </p>

            </div>

        </div>

    </div>

@endif


<!-- About -->

<div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8">

    <div class="flex items-start gap-3">

        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-lg shrink-0">
            ℹ️
        </div>

        <div>

            <p class="font-semibold text-blue-800">
                About this plan
            </p>

            <p class="text-sm text-blue-700 mt-1 leading-6">
                AI suggestions are based on your recipes and preferences.
                You can change any suggested meal before accepting the weekly plan.
            </p>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- AI Suggestions -->
<!-- ========================================================= -->

<div class="space-y-6">

    @foreach ($suggestions as $day => $meals)

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <!-- Day Header -->

            <div class="bg-orange-50 border-b border-orange-100 px-6 py-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                            Suggested Meals
                        </p>

                        <h2 class="text-2xl font-bold text-gray-800 mt-1">
                            {{ $day }}
                        </h2>

                    </div>

                </div>

            </div>


            <!-- Day Meals -->

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                @if (empty($meals))

                    <div class="md:col-span-2 bg-stone-50 border border-dashed border-gray-200 rounded-2xl p-8 text-center">

                        <div class="text-4xl">
                            ℹ️
                        </div>

                        <p class="font-semibold text-gray-700 mt-3">
                            No suitable meals were suggested for this day.
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            You can add a meal manually from your recipe collection.
                        </p>

                        <a
                            href="{{ url('/meal-planner') }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-orange-500 hover:text-orange-600 mt-4"
                        >
                            📅 Open Meal Planner →
                        </a>

                    </div>

                @else

                    @foreach ($meals as $mealType => $meal)

                        @php

                            $mealIcon = match ($mealType) {
                                'Breakfast' => '🍳',
                                'Lunch' => '🍱',
                                'Dinner' => '🍽️',
                                default => '🍴',
                            };

                            $mealId = 'meal-'
                                . strtolower(str_replace(' ', '-', $day))
                                . '-'
                                . strtolower($mealType);

                        @endphp


                        <div
                            id="{{ $mealId }}"
                            class="border border-gray-100 rounded-2xl p-5 bg-white hover:border-orange-200 transition"
                        >

                            <!-- Meal Type -->

                            <div class="flex items-center justify-between mb-4">

                                <p class="text-sm font-bold text-gray-600">
                                    {{ $mealIcon }}
                                    {{ $mealType }}
                                </p>

                                <span class="text-xs text-gray-400">
                                    AI Suggestion
                                </span>

                            </div>


                            @if (
                                isset($meal['recipe_name']) &&
                                $meal['recipe_name'] !== 'No suggestion'
                            )

                                <div class="bg-stone-50 border border-gray-100 rounded-xl p-4">

                                    <p class="recipe-name font-semibold text-gray-800">
                                        {{ $meal['recipe_name'] }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Recommended from your available recipes
                                    </p>

                                </div>


                                <button
                                    type="button"
                                    onclick="openChangeModal(
                                        @js($day),
                                        @js($mealType),
                                        {{ $meal['recipe_id'] }}
                                    )"
                                    class="w-full mt-4 inline-flex items-center justify-center gap-2 bg-gray-900 text-white py-2.5 rounded-xl font-semibold hover:bg-gray-800 transition"
                                >
                                    ↔ Change Meal
                                </button>


                                <input
                                    type="hidden"
                                    class="selected-recipe-id"
                                    data-day="{{ $day }}"
                                    data-meal-type="{{ $mealType }}"
                                    value="{{ $meal['recipe_id'] }}"
                                >

                            @else

                                <div class="bg-stone-50 border border-dashed border-gray-200 rounded-xl p-5">

                                    <p class="text-sm text-gray-500">
                                        No suitable meal was found for this slot.
                                    </p>


                                    <a
                                        href="{{ url('/meal-planner') }}"
                                        class="inline-flex items-center gap-2 text-sm text-orange-500 font-semibold hover:text-orange-600 mt-3"
                                    >
                                        + Add a meal manually
                                    </a>

                                </div>

                            @endif

                        </div>

                    @endforeach

                @endif

            </div>

        </div>

    @endforeach

</div>


<!-- ========================================================= -->
<!-- Bottom Actions -->
<!-- ========================================================= -->

<section class="mt-10 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">

    <div class="text-center">

        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
            Review Complete
        </p>

        <h2 class="text-2xl font-bold text-gray-800 mt-1">
            🤖 Ready to accept your AI plan?
        </h2>

        <p class="text-gray-500 mt-2 max-w-xl mx-auto">
            Make any final changes above, then accept the weekly plan.
        </p>

    </div>


    <!-- Warning -->

    @php

        $plannedMealTypes = $requestedMealTypes;

        $keptMealTypes = array_values(
            array_diff(
                ['Breakfast', 'Lunch', 'Dinner'],
                $plannedMealTypes
            )
        );

    @endphp


    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mt-6">

        <div class="flex items-start gap-3">

            <div class="text-lg">
                ⚠️
            </div>

            <div>

                <p class="font-semibold text-yellow-800">
                    Before accepting
                </p>

                <p class="text-sm text-yellow-700 mt-1 leading-6">
                    This replaces your
                    <strong>{{ implode(' and ', $plannedMealTypes) }}</strong>
                    meals for
                    {{ $startOfWeek->format('d M') }}
                    -
                    {{ $endOfWeek->format('d M Y') }}
                    with the suggestions above.

                    @if ($keptMealTypes !== [])

                        Your
                        <strong>{{ implode(' and ', $keptMealTypes) }}</strong>
                        meals are not part of this plan and will be left
                        exactly as they are.

                    @endif

                    Any meal the AI could not fill keeps what you already had planned.
                </p>

            </div>

        </div>

    </div>


    <!-- Accept -->

    <form
        action="{{ route('meal-planner.accept') }}"
        method="POST"
        id="acceptWeeklyForm"
        class="mt-6"
    >

        @csrf

        <input
            type="hidden"
            name="weekly_plan"
            id="weeklyPlanInput"
        >

        {{-- Only these meal types were requested, so accepting must not touch any others. --}}

        <input
            type="hidden"
            name="meal_types"
            value="{{ implode(',', $requestedMealTypes) }}"
        >

        <input
            type="hidden"
            name="week"
            value="{{ $startOfWeek->format('Y-m-d') }}"
        >


        <button
            type="submit"
            onclick="return confirmAcceptPlan()"
            class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-8 py-3.5 rounded-xl font-semibold shadow-sm hover:bg-orange-600 hover:shadow-md transition"
        >
            ✓ Accept Weekly Plan
        </button>

    </form>


    <!-- Other Actions -->

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">

        <a
            href="{{ url('/preferences') }}"
            class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-5 py-3 rounded-xl font-semibold hover:bg-gray-800 transition"
        >
            ← Change Preferences
        </a>


        <a
            href="{{ url('/meal-planner') }}"
            class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl font-semibold hover:bg-gray-50 transition"
        >
            📅 Meal Planner
        </a>

    </div>

</section>

</div>

<!-- ============================================================= -->

<!-- Change Meal Modal -->

<!-- ============================================================= -->

<div
    id="changeModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 px-4"
>

<div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] p-6 flex flex-col">


    <!-- Header -->

    <div class="flex items-start justify-between gap-4 mb-5 shrink-0">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                AI Meal Plan
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Change Meal
            </h2>

            <p
                id="modalMealInfo"
                class="text-sm text-gray-500 mt-1"
            ></p>

        </div>


        <button
            type="button"
            onclick="closeChangeModal()"
            class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl"
            aria-label="Close"
        >
            &times;
        </button>

    </div>


    <!-- Search -->

    <div class="mb-4">

        <label
            for="recipeSearch"
            class="block text-sm font-semibold text-gray-700 mb-2"
        >
            Search Recipe
        </label>

        <input
            type="text"
            id="recipeSearch"
            placeholder="🔎 Search your recipes..."
            onkeyup="filterRecipes()"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
        >

    </div>


    <!-- Recipe List -->

    <div
        id="recipeList"
        class="flex-1 min-h-0 overflow-y-auto space-y-2"
    >

        @foreach ($recipes as $recipe)

            <button
                type="button"
                class="recipe-option w-full text-left border border-gray-100 bg-white rounded-xl px-4 py-3 hover:bg-orange-50 hover:border-orange-200 transition"
                data-id="{{ $recipe->id }}"
                data-name="{{ strtolower($recipe->name) }}"
                onclick="selectRecipe(
                    {{ $recipe->id }},
                    @js($recipe->name)
                )"
            >

                <div class="flex items-center justify-between gap-4">

                    <div class="min-w-0">

                        <span class="block font-semibold text-gray-800 truncate">
                            {{ $recipe->name }}
                        </span>

                        @if ($recipe->category)

                            <span class="block text-sm text-gray-400 mt-1">
                                {{ $recipe->category }}
                                ·
                                {{ $recipe->cooking_time }} min
                                ·
                                {{ $recipe->difficulty }}
                            </span>

                        @endif

                    </div>

                    <span class="text-orange-500 text-sm font-semibold whitespace-nowrap">
                        Select →
                    </span>

                </div>

            </button>

        @endforeach

    </div>


    <!-- Modal Actions -->

    <div class="grid grid-cols-2 gap-3 mt-5">

        <button
            type="button"
            onclick="closeChangeModal()"
            class="bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
        >
            Cancel
        </button>


        <button
            type="button"
            onclick="confirmRecipeChange()"
            class="bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
        >
            Select Recipe
        </button>

    </div>

</div>

</div>

<script>

    let currentDay = null;
    let currentMealType = null;
    let selectedRecipeId = null;
    let selectedRecipeName = null;


    /*
    |--------------------------------------------------------------------------
    | Open Change Modal
    |--------------------------------------------------------------------------
    */

    function openChangeModal(
        day,
        mealType,
        currentRecipeId
    )
    {
        currentDay = day;
        currentMealType = mealType;

        selectedRecipeId = currentRecipeId;
        selectedRecipeName = null;


        document.getElementById(
            'modalMealInfo'
        ).innerText =
            day + ' · ' + mealType;


        document.getElementById(
            'recipeSearch'
        ).value = '';


        filterRecipes();


        const modal =
            document.getElementById(
                'changeModal'
            );


        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    function closeChangeModal()
    {
        const modal =
            document.getElementById(
                'changeModal'
            );


        modal.classList.add(
            'hidden'
        );

        modal.classList.remove(
            'flex'
        );


        selectedRecipeId = null;
        selectedRecipeName = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Search Recipes
    |--------------------------------------------------------------------------
    */

    function filterRecipes()
    {
        const search =
            document
                .getElementById(
                    'recipeSearch'
                )
                .value
                .toLowerCase();


        const recipes =
            document.querySelectorAll(
                '.recipe-option'
            );


        recipes.forEach(
            recipe => {

                const name =
                    recipe.dataset.name;


                if (name.includes(search)) {

                    recipe.classList.remove(
                        'hidden'
                    );

                } else {

                    recipe.classList.add(
                        'hidden'
                    );

                }

            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Select Recipe
    |--------------------------------------------------------------------------
    */

    function selectRecipe(
        id,
        name
    )
    {
        selectedRecipeId = id;
        selectedRecipeName = name;


        document
            .querySelectorAll(
                '.recipe-option'
            )
            .forEach(
                option => {

                    option.classList.remove(
                        'bg-orange-100',
                        'border-orange-500'
                    );

                }
            );


        const selectedOption =
            document.querySelector(
                `.recipe-option[data-id="${id}"]`
            );


        if (selectedOption) {

            selectedOption.classList.add(
                'bg-orange-100',
                'border-orange-500'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Recipe Change
    |--------------------------------------------------------------------------
    */

    function confirmRecipeChange()
    {
        if (
            !selectedRecipeId ||
            !selectedRecipeName
        ) {

            alert(
                'Please select a recipe first.'
            );

            return;

        }


        const mealCardId =
            'meal-' +
            currentDay
                .toLowerCase()
                .replace(/\s+/g, '-') +
            '-' +
            currentMealType.toLowerCase();


        const mealCard =
            document.getElementById(
                mealCardId
            );


        if (!mealCard) {

            closeChangeModal();
            return;

        }


        const recipeName =
            mealCard.querySelector(
                '.recipe-name'
            );


        if (recipeName) {

            recipeName.innerText =
                selectedRecipeName;

        }


        const hiddenInput =
            mealCard.querySelector(
                '.selected-recipe-id'
            );


        if (hiddenInput) {

            hiddenInput.value =
                selectedRecipeId;

        }


        closeChangeModal();
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Weekly Plan
    |--------------------------------------------------------------------------
    */

    document
        .getElementById(
            'acceptWeeklyForm'
        )
        .addEventListener(
            'submit',
            function (event)
            {

                const inputs =
                    document.querySelectorAll(
                        '.selected-recipe-id'
                    );


                const weeklyPlan = [];


                inputs.forEach(
                    input => {

                        const recipeId =
                            input.value;

                        const day =
                            input.dataset.day;

                        const mealType =
                            input.dataset.mealType;


                        if (
                            recipeId &&
                            day &&
                            mealType
                        ) {

                            weeklyPlan.push({
                                day: day,
                                meal_type: mealType,
                                recipe_id: recipeId
                            });

                        }

                    }
                );


                if (
                    weeklyPlan.length === 0
                ) {

                    event.preventDefault();

                    alert(
                        'There are no valid AI meal suggestions to accept.'
                    );

                    return;

                }


                document.getElementById(
                    'weeklyPlanInput'
                ).value =
                    JSON.stringify(
                        weeklyPlan
                    );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Confirm Accept
    |--------------------------------------------------------------------------
    */

    function confirmAcceptPlan()
    {
        const missingSlots =
            {{ $missingSlots }};

        const plannedMealTypes =
            @json($plannedMealTypes);

        const keptMealTypes =
            @json($keptMealTypes);

        const keptNote =
            keptMealTypes.length
                ? ' Your ' +
                    keptMealTypes.join(' and ') +
                    ' meals will not be touched.'
                : '';


        if (missingSlots > 0) {

            return confirm(
                'This AI plan has ' +
                missingSlots +
                ' unplanned meal ' +
                (
                    missingSlots === 1
                        ? 'slot'
                        : 'slots'
                ) +
                '. Anything it could not fill keeps the meal you already have.' +
                keptNote +
                ' Accept anyway?'
            );

        }


        return confirm(
            'Replace your ' +
            plannedMealTypes.join(' and ') +
            ' meals with these suggestions?' +
            keptNote
        );
    }

</script>

@endsection
