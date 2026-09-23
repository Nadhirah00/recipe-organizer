@extends('layouts.app')

@section('title', $recipe->name)

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

<!-- Back -->


<!-- Recipe Card -->

<article class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">


    <!-- ===================================================== -->
    <!-- Hero Image -->
    <!-- ===================================================== -->

    <div class="relative h-72 md:h-[420px] bg-orange-50 overflow-hidden">

        @if ($recipe->image)

            <img
                src="{{ asset('storage/' . $recipe->image) }}"
                alt="{{ $recipe->name }}"
                class="w-full h-full object-cover"
            >

        @else

            <div class="w-full h-full flex items-center justify-center">

                <span class="text-8xl">
                    🍳
                </span>

            </div>

        @endif


        <!-- Visibility Badge -->

        <div class="absolute top-5 left-5">

            @if ($recipe->is_public)

                <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold text-green-700 shadow-sm">
                    🌎 Public
                </span>

            @else

                <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold text-gray-600 shadow-sm">
                    🔒 Private
                </span>

            @endif

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- Main Content -->
    <!-- ===================================================== -->

    <div class="p-6 md:p-10">


        <!-- Owner -->

        <div class="flex items-center gap-2 mb-4">

            <span class="text-sm text-gray-500">
                👨‍🍳 By
            </span>

            @auth

                @if ($recipe->user_id === auth()->id())

                    <span class="text-sm font-semibold text-orange-600">
                        You
                    </span>

                @else

                    <span class="text-sm font-semibold text-gray-700">
                        {{ $recipe->user->name }}
                    </span>

                @endif

            @else

                <span class="text-sm font-semibold text-gray-700">
                    {{ $recipe->user->name }}
                </span>

            @endauth

        </div>


        <!-- Title -->

        <div class="max-w-4xl">

            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-800">
                {{ $recipe->name }}
            </h1>


            @if ($recipe->description)

                <p class="text-lg text-gray-500 leading-8 mt-4">
                    {{ $recipe->description }}
                </p>

            @endif

        </div>


        <!-- Recipe Summary -->

        <div class="flex flex-wrap gap-2 mt-6">

            <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 px-4 py-2 rounded-full text-sm font-semibold">
                🏷️ {{ $recipe->category }}
            </span>

            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">
                ⏱️ {{ $recipe->cooking_time }} min
            </span>

            <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 px-4 py-2 rounded-full text-sm font-semibold">
                ⭐ {{ $recipe->difficulty }}
            </span>

            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
                👥 {{ $recipe->servings }}
                {{ $recipe->servings == 1 ? 'serving' : 'servings' }}
            </span>

        </div>


        <!-- ================================================= -->
        <!-- Actions -->
        <!-- ================================================= -->

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8">

            <!-- Favorite -->

            @auth

                @if (
                    $recipe->favorites()
                        ->where('user_id', auth()->id())
                        ->exists()
                )

                    <form
                        action="{{ url('/recipes/' . $recipe->id . '/favorite') }}"
                        method="POST"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-red-50 border border-red-200 text-red-600 py-3 rounded-xl font-semibold hover:bg-red-100 transition"
                        >
                            ❤️ Remove from Favorites
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
                            class="w-full inline-flex items-center justify-center gap-2 bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
                        >
                            ♡ Add to Favorites
                        </button>

                    </form>

                @endif

            @endauth


            <!-- Meal Plan -->

            <button
                type="button"
                onclick="openRecipeMealModal()"
                class="w-full inline-flex items-center justify-center gap-2 bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-gray-800 transition"
            >
                📅 Add to Meal Plan
            </button>

        </div>


        <!-- ================================================= -->
        <!-- Ingredients + Instructions -->
        <!-- ================================================= -->

        <div class="border-t border-gray-100 mt-10 pt-10">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">


                <!-- Ingredients -->

                <section class="lg:col-span-5">

                    <div class="lg:sticky lg:top-24">

                        <div class="mb-4">

                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                                What you need
                            </p>

                            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                                🥕 Ingredients
                            </h2>

                        </div>


                        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-6">

                            <div class="whitespace-pre-line text-gray-700 leading-7">
                                {{ $recipe->ingredients }}
                            </div>

                        </div>

                    </div>

                </section>


                <!-- Instructions -->

                <section class="lg:col-span-7">

                    <div class="mb-4">

                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                            How to make it
                        </p>

                        <h2 class="text-2xl font-bold text-gray-800 mt-1">
                            👨‍🍳 Instructions
                        </h2>

                    </div>


                    <div class="bg-stone-50 border border-gray-100 rounded-2xl p-6">

                        <div class="whitespace-pre-line text-gray-700 leading-8">
                            {{ $recipe->instructions }}
                        </div>

                    </div>

                </section>

            </div>

        </div>


        <!-- ================================================= -->
        <!-- Tutorial -->
        <!-- ================================================= -->

        @if ($recipe->tutorial_url)

            <section class="border-t border-gray-100 mt-10 pt-10">

                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-orange-500">
                    Learn visually
                </p>

                <h2 class="text-2xl font-bold text-gray-800 mt-1">
                    🎥 Tutorial
                </h2>

                <p class="text-sm text-gray-500 mt-2">
                    Follow the recipe with a video tutorial.
                </p>


                <a
                    href="{{ $recipe->tutorial_url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 bg-red-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-red-600 transition mt-5"
                >
                    ▶ Watch Tutorial
                </a>

            </section>

        @endif


        <!-- ================================================= -->
        <!-- Owner Actions -->
        <!-- ================================================= -->

        @auth

            @if ($recipe->user_id === auth()->id())

                <section class="border-t border-gray-100 mt-10 pt-10">

                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-gray-400">
                        Recipe Management
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">

                        <a
                            href="{{ url('/recipes/' . $recipe->id . '/edit') }}"
                            class="inline-flex items-center justify-center gap-2 bg-gray-800 text-white py-3 rounded-xl font-semibold hover:bg-gray-700 transition"
                        >
                            ✏️ Edit Recipe
                        </a>


                        <form
                            action="{{ url('/recipes/' . $recipe->id) }}"
                            method="POST"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                onclick="return confirm('Are you sure you want to delete this recipe?')"
                                class="w-full inline-flex items-center justify-center gap-2 border border-red-200 text-red-500 py-3 rounded-xl font-semibold hover:bg-red-50 transition"
                            >
                                🗑️ Delete Recipe
                            </button>

                        </form>

                    </div>

                </section>

            @endif

        @endauth

    </div>

</article>

</div>

<!-- ============================================================= -->

<!-- Add to Meal Plan Modal -->

<!-- ============================================================= -->

<div
    id="recipeMealModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
>

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6">


    <!-- Header -->

    <div class="flex items-start justify-between gap-4 mb-6">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-orange-500">
                Meal Planner
            </p>

            <h2 class="text-2xl font-bold text-gray-800 mt-1">
                Add to Meal Plan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $recipe->name }}
            </p>

        </div>


        <button
            type="button"
            onclick="closeRecipeMealModal()"
            class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl"
            aria-label="Close"
        >
            &times;
        </button>

    </div>


    <!-- Form -->

    <form
        action="{{ url('/meal-planner') }}"
        method="POST"
    >

        @csrf


        <input
            type="hidden"
            name="recipe_id"
            value="{{ $recipe->id }}"
        >


        <!-- Date -->

        <div class="mb-5">

            <label
                for="recipeMealDate"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Date
            </label>

            <input
                type="date"
                name="date"
                id="recipeMealDate"
                value="{{ now()->format('Y-m-d') }}"
                min="{{ now()->format('Y-m-d') }}"
                required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
            >

        </div>


        <!-- Meal Type -->

        <div class="mb-6">

            <label
                for="recipeMealType"
                class="block text-sm font-semibold text-gray-700 mb-2"
            >
                Meal Type
            </label>

            <select
                name="meal_type"
                id="recipeMealType"
                required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-400"
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


            <p class="text-xs text-gray-400 mt-2">
                We selected a meal type based on the recipe category.
                You can change it.
            </p>

        </div>


        <!-- Buttons -->

        <div class="grid grid-cols-2 gap-3">

            <button
                type="button"
                onclick="closeRecipeMealModal()"
                class="bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition"
            >
                Cancel
            </button>


            <button
                type="submit"
                class="bg-orange-500 text-white py-3 rounded-xl font-semibold hover:bg-orange-600 transition"
            >
                Add to Plan
            </button>

        </div>

    </form>

</div>

</div>

<script>

    function openRecipeMealModal()
    {
        const modal =
            document.getElementById(
                'recipeMealModal'
            );


        const category =
            @js($recipe->category);


        const mealType =
            document.getElementById(
                'recipeMealType'
            );


        if (
            category === 'Breakfast' ||
            category === 'Lunch' ||
            category === 'Dinner'
        ) {

            mealType.value = category;

        } else {

            mealType.value = 'Lunch';

        }


        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeRecipeMealModal()
    {
        const modal =
            document.getElementById(
                'recipeMealModal'
            );

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>

@endsection
