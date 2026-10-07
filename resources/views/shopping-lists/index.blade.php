@extends('layouts.app')

@section('title', 'Shopping List')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-10">

<!-- ========================================================= -->
<!-- Header -->
<!-- ========================================================= -->

<div class="mb-8">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">

        <div>

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                Weekly Preparation
            </p>

            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-gray-800 mt-1">
                🛒 Shopping List
            </h1>

            <p class="text-gray-500 mt-2">
                Everything you need for your planned meals this week.
            </p>

        </div>


        <!-- Week Navigation -->

        <div class="flex items-center gap-2">

            <a
                href="{{ url('/shopping-list?week=' . $startOfWeek->copy()->subWeek()->format('Y-m-d')) }}"
                class="inline-flex items-center gap-1 border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
            >
                ← Previous
            </a>


            <a
                href="{{ url('/shopping-list') }}"
                class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                This Week
            </a>


            <a
                href="{{ url('/shopping-list?week=' . $startOfWeek->copy()->addWeek()->format('Y-m-d')) }}"
                class="inline-flex items-center gap-1 border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
            >
                Next →
            </a>

        </div>

    </div>


    <!-- Week Summary -->

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">

        <div>

            <p class="text-sm font-semibold text-gray-700">
                {{ $startOfWeek->format('d M') }}
                -
                {{ $endOfWeek->format('d M Y') }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Selected shopping week
            </p>

        </div>


        @if ($mealPlans->isNotEmpty())

            <div class="flex flex-wrap items-center gap-3">

                <button
                    type="button"
                    id="toggleAllSections"
                    data-toggle-all
                    aria-controls="shopping-list-sections"
                    title="Hide the ingredient list for every recipe"
                    class="inline-flex items-center gap-2 border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl font-semibold hover:border-orange-300 hover:text-orange-500 transition"
                >
                    <span
                        data-toggle-all-icon
                        aria-hidden="true"
                        class="inline-flex shrink-0"
                    >
                        <svg
                            data-toggle-all-icon="hide"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 3l18 18M10.6 10.6a3 3 0 004.2 4.2M9.4 5.3A9.7 9.7 0 0112 5c7 0 10 7 10 7a17.6 17.6 0 01-2.4 3.3M6.2 6.2A17.6 17.6 0 002 12s3 7 10 7a9.8 9.8 0 005.4-1.6"
                            />
                        </svg>

                        <svg
                            data-toggle-all-icon="show"
                            xmlns="http://www.w3.org/2000/svg"
                            class="hidden w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                            />
                        </svg>
                    </span>

                    <span data-toggle-all-label>Hide all ingredients</span>
                </button>


                <form
                    action="{{ url('/shopping-list/generate') }}"
                    method="POST"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="week"
                        value="{{ $startOfWeek->format('Y-m-d') }}"
                    >


                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-green-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-600 transition"
                    >
                        🔄 Refresh List
                    </button>

                </form>

            </div>

        @endif

    </div>

</div>


<!-- ========================================================= -->
<!-- Messages -->
<!-- ========================================================= -->


@if (session('error'))

    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 mb-6">

        <span class="text-lg">
            ⚠️
        </span>

        <p class="font-medium">
            {{ session('error') }}
        </p>

    </div>

@endif


<!-- ========================================================= -->
<!-- Shopping Progress -->
<!-- ========================================================= -->

@if ($shoppingTotal > 0)

    @php
        $shoppingPercentage = round(
            ($shoppingCompleted / $shoppingTotal) * 100
        );
    @endphp


    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Progress
                </p>

                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    Your Shopping Checklist
                </h2>

                <p
                    id="shoppingSummaryText"
                    class="text-sm text-gray-500 mt-1"
                >
                    {{ $shoppingCompleted }}
                    of
                    {{ $shoppingTotal }}
                    items completed
                </p>

            </div>


            <div class="text-right">

                <p
                    id="shoppingPercentageText"
                    class="text-3xl font-bold text-green-500"
                >
                    {{ $shoppingPercentage }}%
                </p>

                <p class="text-xs text-gray-400">
                    completed
                </p>

            </div>

        </div>


        <div class="h-3 bg-green-50 rounded-full overflow-hidden mt-5">

            <div
                id="shoppingProgressBar"
                class="h-full bg-green-500 rounded-full transition-all duration-500"
                style="width: {{ $shoppingPercentage }}%"
            ></div>

        </div>

    </div>

@endif


<!-- ========================================================= -->
<!-- Shopping List -->
<!-- ========================================================= -->

    <div class="space-y-6" id="shopping-list-sections">


    <!-- No Meals -->

    @if ($mealPlans->isEmpty())

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

            <div class="mx-auto w-20 h-20 rounded-3xl bg-orange-50 flex items-center justify-center text-5xl">
                📅
            </div>

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500 mt-6">
                Nothing planned
            </p>

            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                No Meals Planned
            </h2>

            <p class="text-gray-500 mt-3 max-w-md mx-auto">
                There are no meals planned for this week, so there are no ingredients to shop for yet.
            </p>


            <a
                href="{{ url('/meal-planner?week=' . $startOfWeek->format('Y-m-d')) }}"
                class="inline-flex items-center justify-center gap-2 bg-orange-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-600 transition mt-6"
            >
                📅 Plan This Week
            </a>

        </div>


    <!-- Meals Exist But List Not Generated -->

    @elseif ($groupedLists->isEmpty())

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 md:p-16 text-center">

            <div class="mx-auto w-20 h-20 rounded-3xl bg-green-50 flex items-center justify-center text-5xl">
                🛒
            </div>

            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-green-500 mt-6">
                Ready to generate
            </p>

            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">
                Shopping List Not Generated
            </h2>

            <p class="text-gray-500 mt-3 max-w-md mx-auto">
                Your meals are already planned. Generate the shopping list to see all the ingredients you need.
            </p>


            <form
                action="{{ url('/shopping-list/generate') }}"
                method="POST"
                class="mt-6"
            >

                @csrf

                <input
                    type="hidden"
                    name="week"
                    value="{{ $startOfWeek->format('Y-m-d') }}"
                >


                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-green-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-600 transition"
                >
                    🛒 Generate Shopping List
                </button>

            </form>

        </div>

    @else

        <!-- ================================================= -->
        <!-- Recipe Shopping Groups -->
        <!-- ================================================= -->

        @foreach ($groupedLists as $recipeId => $items)

            @php
                $recipe = $items->first()->recipe;
                $sectionId = 'shopping-section-' . $recipeId;
            @endphp


            @if ($recipe)

                <section
                    data-shopping-section
                    data-recipe-id="{{ $recipeId }}"
                    data-expanded="true"
                    data-recipe-name="{{ $recipe->name }}"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                >


                    <!-- Recipe Header -->

                    <div class="bg-orange-50 border-b border-orange-100 px-6 py-5">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                            <h2 class="flex-1 min-w-0">

                                <button
                                    type="button"
                                    data-section-toggle
                                    aria-expanded="true"
                                    aria-controls="{{ $sectionId }}"
                                    title="Hide the ingredients for {{ $recipe->name }}"
                                    class="group flex items-center gap-3 text-left w-full rounded-xl"
                                >

                                    <span
                                        data-section-chevron
                                        aria-hidden="true"
                                        class="shrink-0 w-8 h-8 inline-flex items-center justify-center rounded-lg bg-white border border-orange-200 text-orange-600 transition group-hover:border-orange-300"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4 transition-transform duration-200"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m6 9 6 6 6-6"
                                            />
                                        </svg>
                                    </span>

                                    <span class="min-w-0">

                                        <span
                                            data-section-title
                                            class="block text-xl font-bold text-gray-800 mt-1"
                                        >
                                            🍳 {{ $recipe->name }}
                                        </span>

                                    </span>


                                </button>

                            </h2>


                            <a
                                href="{{ url('/recipes/' . $recipe->id) }}"
                                class="inline-flex items-center justify-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 shrink-0"
                            >
                                View Recipe →
                            </a>

                        </div>


                        <div class="flex flex-wrap items-center gap-2 mt-4 pl-11">

                            @if ($recipe->category)

                                <span class="bg-white/80 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    🏷 {{ $recipe->category }}
                                </span>

                            @endif


                            <span class="bg-white/80 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                👥 Serves {{ $recipe->servings }}
                            </span>


                            <span
                                data-section-count
                                class="bg-white/80 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold"
                            >
                                🧺 {{ $items->count() }}
                                {{ Str::plural('ingredient', $items->count()) }}
                            </span>

                        </div>

                    </div>


                    <!-- Ingredients -->

                    <div
                        id="{{ $sectionId }}"
                        data-section-body
                        class="divide-y divide-gray-100"
                    >

                        <p class="px-6 pt-4 pb-1 text-xs text-gray-500">
                            📝 These ingredients are for one preparation of this recipe.
                            If the same recipe is used for another meal, it is listed separately according to the planned recipe.
                        </p>

                        @foreach ($items as $item)

                            <div
                                data-shopping-item="{{ $item->id }}"
                                class="grid grid-cols-[auto_1fr_7rem_auto] items-center gap-4 px-6 py-4 hover:bg-stone-50 transition"
                            >

                                <!-- Checkbox -->

                                <form
                                    action="{{ url('/shopping-list/' . $item->id . '/toggle') }}"
                                    method="POST"
                                    class="flex items-center"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <input
                                        type="checkbox"
                                        onchange="toggleShoppingItem(this)"
                                        {{ $item->is_checked ? 'checked' : '' }}
                                        class="w-5 h-5 text-green-500 border-gray-300 rounded focus:ring-orange-400 cursor-pointer"
                                    >

                                </form>


                                <!-- Ingredient -->

                                <p
                                    data-ingredient
                                    class="min-w-0 font-medium transition
                                    {{ $item->is_checked
                                        ? 'text-gray-400 line-through'
                                        : 'text-gray-800' }}"
                                >
                                    {{ $item->ingredient }}
                                </p>


                                <!-- Quantity -->

                                <span
                                    data-quantity
                                    class="text-right text-sm font-semibold text-gray-500 tabular-nums"
                                >
                                    {{ $item->quantity }}
                                </span>


                                <!-- Remove -->

                                <form
                                    action="{{ url('/shopping-list/' . $item->id) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        onclick="return confirm('Remove this ingredient from the shopping list?')"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                        title="Remove ingredient"
                                        aria-label="Remove ingredient"
                                    >
                                        🗑️
                                    </button>

                                </form>

                            </div>

                        @endforeach

                    </div>

                </section>

            @endif

        @endforeach


        <!-- ================================================= -->
        <!-- Clear Week -->
        <!-- ================================================= -->

        <div class="flex justify-end pt-2">

            <form
                action="{{ url('/shopping-list') }}"
                method="POST"
            >

                @csrf
                @method('DELETE')


                <input
                    type="hidden"
                    name="week"
                    value="{{ $startOfWeek->format('Y-m-d') }}"
                >


                <button
                    type="submit"
                    onclick="return confirm('Clear the shopping list for this week?')"
                    class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-600 font-semibold"
                >
                    🗑️ Clear This Week
                </button>

            </form>

        </div>

    @endif

</div>

</div>

<script>
    /*
    |--------------------------------------------------------------------------
    | Collapsible Recipe Sections
    |--------------------------------------------------------------------------
    |
    | Each recipe header toggles its own ingredient list. The "Hide all
    | ingredients" button collapses every section at once so only the recipe
    | names are left, and flips to "Show all ingredients" once everything
    | is hidden.
    |
    */

    const shoppingListStorageKey = 'shoppingList:sections:{{ auth()->id() }}';

    const readCollapsedState = () => {
        try {
            return JSON.parse(localStorage.getItem(shoppingListStorageKey)) || {};
        } catch (error) {
            return {};
        }
    };

    const writeCollapsedState = (state) => {
        try {
            localStorage.setItem(shoppingListStorageKey, JSON.stringify(state));
        } catch (error) {
            // Ignore storage errors (private mode, quota, etc.)
        }
    };

    const shoppingSections = Array.from(
        document.querySelectorAll('[data-shopping-section]')
    );

    const collapsedState = readCollapsedState();

    const toggleAllButton = document.querySelector('[data-toggle-all]');
    const toggleAllLabel = toggleAllButton?.querySelector('[data-toggle-all-label]');
    const toggleAllIcon = toggleAllButton?.querySelector('[data-toggle-all-icon]');

    const setSectionExpanded = (section, expanded) => {
        const body = section.querySelector('[data-section-body]');
        const toggle = section.querySelector('[data-section-toggle]');
        const chevron = section.querySelector('[data-section-chevron]');
        const action = section.querySelector('[data-section-action]');
        const recipeName = section.dataset.recipeName || 'this recipe';

        section.dataset.expanded = expanded ? 'true' : 'false';

        if (body) {
            body.classList.toggle('hidden', !expanded);
        }

        if (chevron) {
            chevron.querySelector('svg')?.classList.toggle('rotate-180', !expanded);
        }

        if (action) {
            action.textContent = expanded
                ? 'Hide ingredients'
                : 'Show ingredients';
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            toggle.setAttribute(
                'title',
                `${expanded ? 'Hide' : 'Show'} the ingredients for ${recipeName}`
            );
            toggle.setAttribute(
                'aria-label',
                `${expanded ? 'Hide' : 'Show'} ingredients for ${recipeName}`
            );
        }
    };

    const syncToggleAllLabel = () => {
        if (!toggleAllButton || !toggleAllLabel) {
            return;
        }

        const allExpanded = shoppingSections.every(
            (section) => section.dataset.expanded !== 'false'
        );

        toggleAllLabel.textContent = allExpanded
            ? 'Hide all ingredients'
            : 'Show all ingredients';

        toggleAllIcon
            ?.querySelector('[data-toggle-all-icon="hide"]')
            ?.classList.toggle('hidden', !allExpanded);

        toggleAllIcon
            ?.querySelector('[data-toggle-all-icon="show"]')
            ?.classList.toggle('hidden', allExpanded);

        toggleAllButton.setAttribute(
            'title',
            allExpanded
                ? 'Hide the ingredient list for every recipe'
                : 'Show the ingredient list for every recipe'
        );

        toggleAllButton.setAttribute('aria-label', allExpanded
            ? 'Hide all ingredients and show recipe names only'
            : 'Show all ingredients');
    };

    shoppingSections.forEach((section) => {
        const toggle = section.querySelector('[data-section-toggle]');

        if (!toggle) {
            return;
        }

        const recipeId = section.dataset.recipeId;
        const isCollapsed = collapsedState[recipeId] === 'collapsed';

        setSectionExpanded(section, !isCollapsed);

        toggle.addEventListener('click', () => {
            const expanded = section.dataset.expanded === 'false';

            setSectionExpanded(section, expanded);

            if (recipeId) {
                collapsedState[recipeId] = expanded ? 'expanded' : 'collapsed';
                writeCollapsedState(collapsedState);
            }

            syncToggleAllLabel();
        });
    });

    if (toggleAllButton) {
        toggleAllButton.addEventListener('click', () => {
            const allExpanded = shoppingSections.every(
                (section) => section.dataset.expanded !== 'false'
            );

            shoppingSections.forEach((section) => {
                setSectionExpanded(section, !allExpanded);

                if (section.dataset.recipeId) {
                    collapsedState[section.dataset.recipeId] = !allExpanded
                        ? 'expanded'
                        : 'collapsed';
                }
            });

            writeCollapsedState(collapsedState);
            syncToggleAllLabel();
        });

        syncToggleAllLabel();
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Ingredient
    |--------------------------------------------------------------------------
    */

    async function toggleShoppingItem(checkbox) {
        const form = checkbox.closest('form');
        const row = checkbox.closest('[data-shopping-item]');
        const ingredient = row.querySelector('[data-ingredient]');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    'Accept': 'text/html,application/xhtml+xml',
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }

            const data = await response.json();

            // Update the appearance without reloading the page
            if (checkbox.checked) {
                ingredient.classList.remove('text-gray-800');
                ingredient.classList.add('text-gray-400', 'line-through');
            } else {
                ingredient.classList.remove('text-gray-400', 'line-through');
                ingredient.classList.add('text-gray-800');
            }

            // Update the progress indicator
            if (typeof data.total === 'number') {
                const summary = document.getElementById('shoppingSummaryText');
                const percentage = document.getElementById('shoppingPercentageText');
                const bar = document.getElementById('shoppingProgressBar');

                if (summary) {
                    summary.textContent =
                        `${data.completed} of ${data.total} items completed`;
                }

                if (percentage) {
                    percentage.textContent = `${data.percentage}%`;
                }

                if (bar) {
                    bar.style.width = `${data.percentage}%`;
                }
            }

        } catch (error) {
            console.error('Shopping item update failed:', error);

            // Undo checkbox if request really failed
            checkbox.checked = !checkbox.checked;

            alert('Unable to update the shopping item.');
        }
    }
</script>

@endsection
