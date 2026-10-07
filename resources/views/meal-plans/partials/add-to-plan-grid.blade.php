{{--
|--------------------------------------------------------------------------
| Add to Plan - Week Picker
|--------------------------------------------------------------------------
|
| Shared week picker for every "Add to Plan" modal. It mirrors the weekly
| table on the meal planner page: days run along the columns and one row is
| drawn for each meal (Breakfast / Lunch / Dinner). The JavaScript in
| resources/js/app.js renders the grid, marks the slots that are already
| planned (using the same orange / blue / purple colours as the planner) and
| writes the selected slots into hidden inputs so they submit alongside the
| form. Occupied slots stay selectable and are replaced once the user
| confirms.
|
--}}

<div
    class="mb-6"
    data-meal-plan-picker
    data-meal-plan-slots-url="{{ route('meal-planner.slots.index') }}"
>

    <!-- Week Navigation -->

    <div class="flex items-center justify-between gap-3 mb-4">

        <button
            type="button"
            data-meal-plan-prev
            class="w-10 h-10 shrink-0 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-orange-300 hover:text-orange-500 transition"
            aria-label="Previous week"
        >
            ←
        </button>

        <div class="text-center min-w-0">

            <p
                data-meal-plan-range
                class="text-sm font-semibold text-gray-700"
            >
                Loading the planner…
            </p>

            <p class="text-xs text-gray-400 mt-1">
                Tap the slots where you want this recipe, then press Add to Plan
            </p>

        </div>

        <button
            type="button"
            data-meal-plan-next
            class="w-10 h-10 shrink-0 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:border-orange-300 hover:text-orange-500 transition"
            aria-label="Next week"
        >
            →
        </button>

    </div>


    <!-- Weekly Table (days as columns, meals as rows) -->

    <div
        data-meal-plan-grid
        class="grid grid-cols-[auto_repeat(7,minmax(0,1fr))] gap-1 overflow-x-auto min-w-[560px] pb-1"
    ></div>


    <!-- Selection Summary + Legend -->

    <div class="flex flex-wrap items-center justify-between gap-3 mt-3">

        <p
            data-meal-plan-count
            class="text-xs font-semibold text-gray-500"
        >
            No meals selected
        </p>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400">

            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded border border-dashed border-gray-300 bg-gray-50"></span>
                Free slot
            </span>

            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded border border-orange-200 bg-orange-50"></span>
                Already planned
            </span>

            <span class="inline-flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-orange-500"></span>
                Selected for this recipe
            </span>

        </div>

    </div>


    <p
        data-meal-plan-status
        class="hidden mt-3 text-xs text-gray-500"
    ></p>


    <!-- Selected slots are written here as hidden inputs -->

    <div data-meal-plan-inputs></div>

</div>