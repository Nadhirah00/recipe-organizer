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

            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800 mt-1">
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

                <p class="text-sm text-gray-500 mt-1">
                    {{ $shoppingCompleted }}
                    of
                    {{ $shoppingTotal }}
                    items completed
                </p>

            </div>


            <div class="text-right">

                <p class="text-3xl font-bold text-green-500">
                    {{ $shoppingPercentage }}%
                </p>

                <p class="text-xs text-gray-400">
                    completed
                </p>

            </div>

        </div>


        <div class="h-3 bg-green-50 rounded-full overflow-hidden mt-5">

            <div
                class="h-full bg-green-500 rounded-full transition-all duration-500"
                style="width: {{ $shoppingPercentage }}%"
            ></div>

        </div>

    </div>

@endif


<!-- ========================================================= -->
<!-- Shopping List -->
<!-- ========================================================= -->

<div class="space-y-6">


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
            @endphp


            @if ($recipe)

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">


                    <!-- Recipe Header -->

                    <div class="bg-orange-50 border-b border-orange-100 px-6 py-5">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                                    Recipe
                                </p>

                                <h2 class="text-xl font-bold text-gray-800 mt-1">
                                    🍳 {{ $recipe->name }}
                                </h2>


                                <div class="flex flex-wrap items-center gap-2 mt-3">

                                    @if ($recipe->category)

                                        <span class="bg-white/80 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            🏷 {{ $recipe->category }}
                                        </span>

                                    @endif


                                    <span class="bg-white/80 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                        👥 Serves {{ $recipe->servings }}
                                    </span>

                                </div>

                            </div>


                            <a
                                href="{{ url('/recipes/' . $recipe->id) }}"
                                class="inline-flex items-center justify-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700"
                            >
                                View Recipe →
                            </a>

                        </div>


                        <p class="text-xs text-gray-500 mt-4">
                            📝 These ingredients are for one preparation of this recipe.
                            If the same recipe is used for another meal, it is listed separately according to the planned recipe.
                        </p>

                    </div>


                    <!-- Ingredients -->

                    <div class="divide-y divide-gray-100">

                        @foreach ($items as $item)

                            <div
                                data-shopping-item="{{ $item->id }}"
                                class="flex items-center gap-4 px-6 py-4 hover:bg-stone-50 transition"
                            >

                                <!-- Checkbox -->

                                <form
                                    action="{{ url('/shopping-list/' . $item->id . '/toggle') }}"
                                    method="POST"
                                    class="flex items-center shrink-0"
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
                                    class="flex-1 min-w-0 font-medium transition
                                    {{ $item->is_checked
                                        ? 'text-gray-400 line-through'
                                        : 'text-gray-800' }}"
                                >
                                    {{ $item->ingredient }}
                                </p>


                                <!-- Quantity -->

                                @if ($item->quantity)

                                    <span
                                        class="shrink-0 min-w-[90px] text-right text-sm font-semibold text-gray-500"
                                    >
                                        {{ $item->quantity }}
                                    </span>

                                @endif


                                <!-- Remove -->

                                <form
                                    action="{{ url('/shopping-list/' . $item->id) }}"
                                    method="POST"
                                    class="shrink-0"
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

            // Update the appearance without reloading the page
            if (checkbox.checked) {
                ingredient.classList.remove('text-gray-800');
                ingredient.classList.add('text-gray-400', 'line-through');
            } else {
                ingredient.classList.remove('text-gray-400', 'line-through');
                ingredient.classList.add('text-gray-800');
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
